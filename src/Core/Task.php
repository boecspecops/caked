<?php

namespace CakeD\Core;


use Cake\ORM\TableRegistry;
use CakeD\Core\Subtask;
use CakeD\Core\Transfer\Configs\DefaultConfig;
use CakeD\Core\Exceptions;
use CakeD\Core\Core;

class TaskStatus {
    const WAIT          = "WAIT";
    const CONNECTING    = "CONNECTING";
    const ERROR         = "ERROR";
    const PROCESSING    = "PROCESSING";
    const COMPLETE      = "COMPLETE";
    const PAUSED        = "PAUSED";
}


class Task {
    private $fs_adapter = null;
    private $subtasks = [];
    private $task;
    
        
    /**
     * Alias of TableRegister::get(..).
     * 
     * @return ORM/Table
     */    
    public static function getTable() {
        return TableRegistry::get('cake_d_tasks');
    }
    
    
    public static function count() {
        $query = static::getTable()->find()->select();
        return $query->where(function ($exp) {
            return $exp->eq('status', TaskStatus::PROCESSING);
        })->orWhere(function ($exp) {
            return $exp->eq('status', TaskStatus::CONNECTING);
        })->count();
    }
    
    
    /**
     * Function returns CakePHP ORM entities of tasks.
     * 
     * @return type
     */
    public static function getIncompletedTasks() {
        $query = self::getTable()->find();
        $query->select();
        return $query->where(function ($exp) {
        return $exp
                ->lte('exec_time', new \DateTime('now'));
        })
        ->andWhere(function ($exp) {
        return $exp
            ->notEq('status', TaskStatus::COMPLETE);
        });
    }
    
    
    public static function tick() {
        $tasks = Task::getIncompletedTasks();
        foreach($tasks as $task) {
            if(self::countTasks() < Core::getConfig()['limitations']['max_tasks']) {
                Task::init_and_execute($task);                
            }
        }
    }
    
    
    public static function init_and_execute($task_entity) {
        $task = new Task($task_entity);
        $task->execute();
    }
    
    
    public static function addTask($method, $exec_time = Null) {
        $ent_task = self::getTable()->newEntity();
        $ent_task->exec_time = is_null($exec_time) ? new \DateTime('now') : $exec_time;
        $ent_task->status = TaskStatus::WAIT;
        $ent_task->method = $method;
        
        $task = new Task($ent_task);
        $task->save();
        
        return $task;
    }
    
    
    public function __construct($task) {
        $this->task = $task;
        $this->subtasks = Subtask::getSubtasks($this->task->task_id);
    }
    
    
    public function addfile($file_pattern) {
        $subtasks = [];
        if(is_array($file_pattern)) {
            foreach($file_pattern as $pattern) {
                $files = glob($pattern);
                $subtasks = array_merge($subtasks, $files);
            }
        }
        else {
            $subtasks = glob($file_pattern);
        }
        
        return Subtask::addSubtask($this->task->task_id, $subtasks);
    }
            
    
    public function execute()
    {
        $task_exec_status = true;
        try {
            $this->setStatus(TaskStatus::CONNECTING);
            $this->fs_adapter = DefaultConfig::getAdapter($this->task->method);
            $this->setStatus(TaskStatus::PROCESSING);
            
            foreach($this->subtasks as $subtask) {
                $subtask->execute($this->fs_adapter) ? : $task_exec_status = false;
            }
        
            if($task_exec_status) {
                $this->task->error = null;
                $this->setStatus(TaskStatus::COMPLETE);
            } else {
                $this->task->error = "[Task] Transfer completed with problems.";
                $this->setStatus(TaskStatus::ERROR);
            }
        } catch (Exceptions\RemoteAuthFailed $e) {
            $this->task->error = $e->getMessage();
            $this->setStatus(TaskStatus::ERROR);
        } 
        catch(Exceptions\RemoteException $e) {
            $this->task->error = $e->getMessage();
            $this->setStatus(TaskStatus::ERROR);
        } 
        catch(Exceptions\FileNotFound $e) {       // Config file not found
            $this->task->error = $e->getMessage();    
            $this->setStatus(TaskStatus::ERROR);
        }
        finally {
            unset($this->fs_adapter);
        }
    }
    
    
    public function setStatus($status)
    {
        $this->task->status = $status;
        $this->save();
    }
    
    
    public function save() {
        self::getTable()->save($this->task);
    }
}
