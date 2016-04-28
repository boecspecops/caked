<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CakeD\Core;
use Cake\ORM\TableRegistry;
use CakeD\Core\Transfer\Configs\DefaultConfig;
use CakeD\Core\Exceptions;

/**
 * Description of Subtask
 *
 * @author boecspecops
 */


class SubtaskStatus {
    const WAIT          = 1;
    const QUEUE         = 2;
    const TRANSFER      = 3;
    const PAUSED        = 4;
    const COMPLETE      = 5;
    const ERROR         = 6;
    const NOT_EXIST     = 7;
}


class Subtask {
    private static $table = null;
    private $task;
    
    /**
     * Function returns CakePHP table object of subtasks.
     * 
     * @return type
     */    
    public static function getTable() {
        if(is_null(self::$table)) {
            self::$table = TableRegistry::get('cake_d_subtasks');
        }
        return self::$table;        
    }
    
    /**
     * This function returns subtasks of task with id = tID.
     * 
     * @param type $tID
     * @return \CakeD\Core\Subtask
     */    
    public static function getSubtasks($tID) {
        $query = self::getTable()->find();
        $ent_subtasks = $query->select()
            ->where(['tID' => $tID])
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
     * @param type $tID
     * @param type $file
     */    
    public static function addSubtask($tID, $file){
        $ent_subtask = self::getTable()->newEntity();
        $ent_subtask->tID  = $tID;
        $ent_subtask->file = $file;
        $ent_subtask->status = SubtaskStatus::WAIT;
        
        $subtask = new Subtask($ent_subtask);
        $subtask->save();
        
        return $subtask;
    }            
    
    public function __construct($subtask) {
        $this->task = $subtask;
    }
    
    /**
     * This function sends file to server.
     * 
     * @param type $fs_adapter
     */
    
    public function execute($fs_adapter) {
        if(!file_exists($this->task->file)) {
            throw(new Exceptions\FileNotFound(["file" => $this->task->file]));
        } else {
            try {
                $this->setStatus(SubtaskStatus::QUEUE);

                $this->setStatus(SubtaskStatus::TRANSFER);
                $fs_adapter->write($this->task->file);
                
                $this->task->error = null;
                $this->setStatus(SubtaskStatus::COMPLETE);
            }
            catch(Exceptions\RemoteException $e) {
                $this->task->error = $e->getMessage();
                $this->setStatus(SubtaskStatus::ERROR);
            }
            catch(Exceptions\ConnectionReset $e) {
                $this->task->error = $e->getMessage();
                $this->setStatus(SubtaskStatus::ERROR);
            }
        }
    }    
    public function save() {
        self::getTable()->save($this->task);
    }
    
    public function setStatus($status) {
        $this->task->status = $status;
        $this->save();
    }
}
