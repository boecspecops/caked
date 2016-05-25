<?php

namespace CakeD\Core;


use Cake\ORM\TableRegistry;
use CakeD\Core\Subtask;
use CakeD\Core\Transfer\Adapters\DefaultAdapter;
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


class Task implements \ArrayAccess {
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
    public static function getIncompleted() {
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
        $tasks = Task::getIncompleted();
        foreach($tasks as $task) {
            Task::init_and_execute($task);
        }
        
        return $tasks;
    }
    
    
    public static function init_and_execute($task_entity) {
        $task = new Task($task_entity);
        return $task->execute();
    }
    
    
    public static function add($method, $directory, $exec_time = Null) {
        $ent_task = self::getTable()->newEntity();
        $ent_task->exec_time = is_null($exec_time) ? new \DateTime('now') : $exec_time;
        $ent_task->status = TaskStatus::WAIT;
        $ent_task->method = $method;
        $ent_task->directory = $directory;
        
        $task = new Task($ent_task);
        $task->save();
        
        return $task;
    }
    
    
    public static function getById($task_id) {
        $query = self::getTable()->find();
        $query->select();
        return new Task(self::getTable()->get($task_id));
    }
    
    
    public function __construct($task) {
        $this->task = $task;
        $this->subtasks = Subtask::getSubtasks($this->task->task_id);
    }
    
    
    public function addfile($file_pattern) {
        $subtasks = [];
        if(is_array($file_pattern)) {
            foreach($file_pattern as $pattern) {
                $files = glob($this->task->directory . $pattern);
                $subtasks = array_merge($subtasks, $files);
            }
        }
        else {
            $subtasks = glob($this->task->directory . $file_pattern);
        }
        
        return Subtask::addSubtask($this->task->task_id, $subtasks);
    }
            
    
    public function execute()
    {
        $statistics = ["subtasks" => count($this->subtasks), "success" => 0];
        try {
            $this->setStatus(TaskStatus::CONNECTING);
            $this->fs_adapter = DefaultAdapter::getAdapter($this->task->method);
            $this->setStatus(TaskStatus::PROCESSING);
            
            foreach($this->subtasks as $subtask) {
                !$subtask->execute($this->fs_adapter) ? : $statistics["completed"]++;
            }
        
            if($statistics["completed"] == $statistics["subtasks"]) {
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
        catch(Exceptions\ConfigParamNotFound $e) {
            $this->task->error = $e->getMessage();    
            $this->setStatus(TaskStatus::ERROR);
        }
        finally {
            unset($this->fs_adapter);
            
            return $statistics;
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
    
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->task[] = $value;
        } else {
            $this->task[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->task[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->task[$offset]);
    }

    public function offsetGet($offset) {
        if(strcmp($offset, "subtasks") == 0) {
            return $this->subtasks;
        } else {
            return isset($this->task[$offset])? $this->task[$offset] : null;
        }
    }
}
