<?php

namespace CakeD\Core;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Cake\ORM\TableRegistry;
use CakeD\Core\Status\TaskStatus;
use CakeD\Core\Status\SubtaskStatus;
use CakeD\Core\Transfer\Adapters\AdapterFTP;
use CakeD\Core\Exceptions\AdapterException;

/**
 * Description of Task
 *
 * @author boecspecops
 */

class SingletonTables
{
    private static $task_table = Null, $subtask_table = Null;
    
    public static function getTaskTable()
    {
        if(is_null(SingletonTables::$task_table))
        {
            SingletonTables::$task_table = TableRegistry::get('tasks');
        }
        return SingletonTables::$task_table;        
    }
    
    public static function getSubtaskTable()
    {
        if(is_null(SingletonTables::$subtask_table))
        {
            SingletonTables::$subtask_table = TableRegistry::get('subtasks');
        }
        return SingletonTables::$subtask_table;        
    }
}

class Task {
    
    private $fs_adapter;
    private $task;
    
    public static function addTask($config, $exec_time = Null)
    {
        $tasks = SingletonTables::getTaskTable();
        $ent_task = $tasks->newEntity();
        $ent_task->exec_time = is_null($exec_time) ? new \DateTime('now') : $exec_time;
        $ent_task->status = TaskStatus::WAIT;
        $ent_task->config_file = $config;
        
        $task = new Task($ent_task, $config);
        $task->save();
        
        return $task;
    }
    
    public static function getIncompletedTasks()
    {        
        $query = SingletonTables::getTaskTable()->find();
        $query->select();
        return $query->where(function ($exp) {
        return $exp
                ->lte('exec_time', new \DateTime('now'));
        });
    }
    
    public function addSubtask($files)
    {
        $this->task->tID;
        
        $subtasks = SingletonTables::getSubtaskTable();
        if(is_array($files)) {
            foreach($files as $file) {
                $this->addSubtask($file);
            }
        }
        else {
            $subtask = $subtasks->newEntity(['tID'=>$this->task->tID, 'file'=>$files, 'status' => TaskStatus::WAIT]);
            $subtasks->save($subtask);
        }
    }
    
    public static function tick()
    {
        foreach(Task::getIncompletedTasks() as $task) {
            Task::init_and_execute($task);
        }
    }
    
    public static function init_and_execute($task_entity)
    {
        $task = new Task($task_entity);
        $task->execute();
    }
    
    public function __construct($task) {
        $this->task = $task;       
    }
    
    public function execute()
    {
        if($this->task->status == TaskStatus::COMPLETE) {
            return ;
        }
                               
        $query = SingletonTables::getSubtaskTable()->find();
        $this->subtasks = $query->select()->where(['tID' => $this->task->tID]); 

        try {
            $this->fs_adapter = new AdapterFTP($this->read_config());

            foreach($this->subtasks as $subtask) {
                $this->sub_execute($subtask);
            }

            $this->task->status = TaskStatus::COMPLETE;
            SingletonTables::getTaskTable()->save($this->task);
        } catch (AdapterException $e) {
            $this->task->status = TaskStatus::ERROR;
            $this->task->error = $e->getMessage();
            SingletonTables::getTaskTable()->save($this->task);
            throw($e);
        }

        unset($this->fs_adapter);
    }
    
    public function save()
    {
        SingletonTables::getTaskTable()->save($this->task);
    }
    
    private function sub_execute($sub_task)
    {
        if($sub_task->status != SubtaskStatus::PAUSED ||
                $sub_task->status != SubtaskStatus::COMPLETE )
        {            
            try{
                // Вызов метода отправки файла вставить сюда.
                $this->fs_adapter->write($sub_task->file);                
                
                $sub_task->status = SubtaskStatus::COMPLETE;
            }
            catch(AdapterException $e) {
                $sub_task->status = SubtaskStatus::ERROR;
                $sub_task->error = $e->getMessage();
                SingletonTables::getSubtaskTable()->save($sub_task);
                throw($e);
            }
            
            SingletonTables::getSubtaskTable()->save($sub_task);
        }
    }
    
    private function read_config()
    {
        return \Symfony\Component\Yaml\Yaml::parse( file_get_contents($this->task->config_file) );
    }
}
