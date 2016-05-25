<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CakeD\Core;
use Cake\ORM\TableRegistry;
use CakeD\Core\Exceptions;

/**
 * Description of Subtask
 *
 * @author boecspecops
 */


class SubtaskStatus {
    const WAIT          = "WAIT";
    const QUEUE         = "QUEUE";
    const TRANSFER      = "TRANSFER";
    const PAUSED        = "PAUSED";
    const COMPLETE      = "COMPLETE";
    const ERROR         = "ERROR";
    const NOT_EXIST     = "NOT_EXIST";
}


class Subtask {
    private static $table = null;
    private $task;
    
    /**
     * Alias for TableRegistry::get(..).
     * 
     * @return ORM/Table
     */    
    public static function getTable() {
        if(is_null(self::$table)) {
            self::$table = TableRegistry::get('cake_d_subtasks');
        }
        return self::$table;        
    }
    
    /**
     * This function returns subtasks of task with id = task_id.
     * 
     * @param type $taskID
     * @return \CakeD\Core\Subtask
     */    
    public static function getSubtasks($taskID) {
        $query = self::getTable()->find();
        $ent_subtasks = $query->select()
            ->where(['task_id' => $taskID])
            ->andWhere(function ($exp) {
            return $exp
                ->notEq('status', SubtaskStatus::COMPLETE);
        });
        
        $subtasks = [];
        
        foreach($ent_subtasks as $subtask) {
            array_push($subtasks, new Subtask($subtask));
        }
        
        return $subtasks;
    }
    
    public static function count() {
        $query = static::getTable()->find()->select();
        return $query->where(function ($exp) {
            return $exp->eq('status', SubtaskStatus::QUEUE);
        })->orWhere(function ($exp) {
            return $exp->eq('status', SubtaskStatus::TRANSFER);
        })->count();
    }
    
    
    /**
     * This function adds new subtask.
     * 
     * @param type $taskID
     * @param type $files
     * 
     * returns int | array (when multiple files found by pattern | null (when no files found by pattern)
     */    
    public static function addSubtask($taskID, $files){
        if(is_array($files)) {
            $subtasks = [];
            foreach ($files as $file ) {
                array_push($subtasks, self::addSubtask($taskID, $file));
            }
            return $subtasks;
        } else if($files !== NULL) {
            $ent_subtask = self::getTable()->newEntity();
            $ent_subtask->task_id  = $taskID;
            $ent_subtask->file = $files;
            $ent_subtask->status = SubtaskStatus::WAIT;

            $subtask = new Subtask($ent_subtask);
            $subtask->save();

            return $subtask;
        }
        
        return Null;
    }            
    
    public function __construct($subtask) {
        $this->task = $subtask;
    }
    
    /**
     * This function sends file to server.
     * 
     * @param type $fs_adapter
     */
    
    public function execute($fs_adapter, $directory) {
        $this->setStatus(SubtaskStatus::QUEUE);
        try {
            $this->setStatus(SubtaskStatus::TRANSFER);
            $fs_adapter->write($directory, $this->task->file);

            $this->task->error = null;
            $this->setStatus(SubtaskStatus::COMPLETE);
            return true;
        }
        catch(Exceptions\RemoteException $e) {
            $this->task->error = $e->getMessage();
            $this->setStatus(SubtaskStatus::ERROR);
            return false;
        }
        catch(Exceptions\ConnectionReset $e) {
            $this->task->error = $e->getMessage();
            $this->setStatus(SubtaskStatus::ERROR);
            return false;
        }
        
    }    
    public function save() {
        self::getTable()->save($this->task);
    }
    
    public function setStatus($status) {
        $this->task->status = $status;
        $this->save();
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
        return isset($this->task[$offset]) ? $this->task[$offset] : null;
    }
}
