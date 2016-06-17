<?php

namespace CakeD\Core;


use Cake\ORM\TableRegistry;
use CakeD\Core\Subtask;
use CakeD\Core\Transfer\Adapters\DefaultAdapter;
use CakeD\Core\Exceptions;

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
    
    
    public static function tick(array $callable = []) {
        $stats = [];
        $tasks = Task::getIncompleted();
        foreach($tasks as $task) {
            array_push($stats, Task::init_and_execute($task, $callable));
        }
        
        return $stats;
    }
    
    
    public static function init_and_execute($task_entity, array $callable = []) {
        $task = new Task($task_entity);
        return $task->execute($callable);
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
    
    
    public static function getById($task_id = null) {
        if($task_id === null) {
            $result = self::getTable()->find('all', ['fields'=>'task_id'])->last();
            return new Task(self::getTable()->get($result->task_id));
        } else {
            return new Task(self::getTable()->get($task_id));
        }
    }
    
    
    public static function getUrlBase($path) {
        $query = Subtask::getTable()->find();
        $result = $query->select(['task_id', 'file', 'status'])
            ->where(['file' => $path])
            ->order(['subtask_id' => 'DESC'])->first();
        
        $query = self::getTable()->find();
        $task_data = $query->select(['method', 'directory'])
            ->where(['task_id' => $result->task_id])->first();
        
        if($result->status == SubtaskStatus::COMPLETE) {
            $adapter = DefaultAdapter::getAdapter($task_data->method);
            return $adapter->getUrlBase($path);
        } else {
            return $task_data->directory . $result->file;
        }
    }
    
    
    public function __construct($task) {
        $this->task = $task;
        $this->subtasks = Subtask::getSubtasks($this->task->task_id);
    }
    
    
    public function addfile($file_pattern) {
        $subtasks = [];
        $subtasks_checked = [];
        if(is_array($file_pattern)) {
            foreach($file_pattern as $pattern) {
                $files = glob(realpath($this->task->directory . $pattern));
                $subtasks = array_merge($subtasks, $files);
            }
        }
        else {
            $subtasks = glob($this->task->directory . $file_pattern);
        }
        
        foreach($subtasks as $subtask) {
            if(strpos($subtask, $this->task->directory) == 0 && !is_dir($subtask)) {
                array_push($subtasks_checked, substr( $subtask, strlen($this->task->directory)));
            }
        }
        
        return Subtask::addSubtask($this->task->task_id, $subtasks_checked);
    }
            
    
    public function execute(array $callables = [])
    {
        $statistics = ["subtasks" => count($this->subtasks), "success" => 0];
        try {
            if(key_exists('taskExecutePre', $callables)) {
                $callables['taskExecutePre']($this);
            }
            $this->setStatus(TaskStatus::CONNECTING);
            $this->fs_adapter = DefaultAdapter::getAdapter($this->task->method);
            $this->setStatus(TaskStatus::PROCESSING);
            
            foreach($this->subtasks as $subtask) {
                !$subtask->execute($this->fs_adapter, $this->task->directory, $callables) ? : $statistics["success"]++;
            }
        
            if($statistics["success"] == $statistics["subtasks"]) {
                $this->task->error = null;
                $this->setStatus(TaskStatus::COMPLETE);
            } else {
                $this->task->error = "[Task] Transfer completed with problems.";
                $this->setStatus(TaskStatus::ERROR);
            }
            
            if(key_exists('taskExecutePost', $callables)) {
                $callables['taskExecutePost']($this);
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
        catch(\Exception $exception) {
            if(key_exists('taskOnException', $callables)) {
                $callables['taskOnException']($this, $exception);
            }
        }
        return $statistics;
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
