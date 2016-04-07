<?php

namespace CakeD\Core;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Cake\ORM\TableRegistry;
use CakeD\Core\Subtask;
use CakeD\Core\Transfer\Adapters\AdapterFTP;
use CakeD\Core\Exceptions\AdapterException;

/**
 * Description of Task
 *
 * @author boecspecops
 */

class TaskStatus {
    const WAIT          = 1;
    const CONNECTING    = 2;
    const ERROR         = 3;
    const PROCESSING    = 4;
    const COMPLETE      = 5;
    const PAUSED        = 6;           
}


class Task {
    
    private static $table;
    private $fs_adapter = null;
    private $subtasks = [];
    private $task;
    
        
    /**
     * Function returns CakePHP table object of tasks.
     * 
     * @return type
     */    
    public static function getTable()
    {
        if(is_null(self::$table))
        {
            self::$table = TableRegistry::get('tasks');
        }
        return self::$table;        
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
            Task::init_and_execute($task);
        }
    }
    
    
    public static function init_and_execute($task_entity) {
        $task = new Task($task_entity);
        $task->execute();
    }
    
    
    public static function addTask($config, $exec_time = Null) {
        $ent_task = self::getTable()->newEntity();
        $ent_task->exec_time = is_null($exec_time) ? new \DateTime('now') : $exec_time;
        $ent_task->status = TaskStatus::WAIT;
        $ent_task->config_file = $config;
        
        $task = new Task($ent_task, $config);
        $task->save();
        
        return $task;
    }
    
    
    public function __construct($task) {
        $this->task = $task;
        $this->subtasks = Subtask::getSubtasks($this->task->tID);
    }
    
    
    public function addSubtask($files) {
        if(is_array($files)) {
            foreach($files as $file) {
                $this->addSubtask($file);
            }
        }
        else {
            $subtask = Subtask::addSubtask($this->task->tID, $files);
        }
    }
    
    
    public function execute()
    {
        try {
            $this->setStatus(TaskStatus::CONNECTING);
            $this->fs_adapter = new AdapterFTP($this->read_config());
            $this->setStatus(TaskStatus::PROCESSING);
            
            foreach($this->subtasks as $subtask) {
                $subtask->execute();
            }

            $this->setStatus(TaskStatus::COMPLETE);
        } catch (AdapterException $e) {
            $this->task->error = $e->getMessage();
            
            $this->setStatus(TaskStatus::ERROR);
            throw($e);
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
    
    
    private function read_config()
    {
        if(file_exists($this->task->config_file)) {
            return \Symfony\Component\Yaml\Yaml::parse( file_get_contents($this->task->config_file) );
        } else {
            
        }        
    }
}
