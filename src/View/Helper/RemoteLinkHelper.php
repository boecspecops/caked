<?php

namespace CakeD\View\Helper;
use Cake\ORM\TableRegistry;
use CakeD\Core\Transfer\Adapters\DefaultAdapter;
use CakeD\Core\SubtaskStatus;
use Cake\View\Helper;


class RemoteLinkHelper extends Helper {
    private function getUrlBase($path) {
        $query = TableRegistry::get('cake_d_subtasks')->find();
        $result = $query->select(['task_id', 'file', 'status'])
            ->where(['file' => $path])
            ->order(['subtask_id' => 'DESC'])->fetch();
        
        $query = TableRegistry::get('cake_d_tasks')->find();
        $task_data = $query->select(['method', 'directory'])
            ->where(['task_id' => $result->task_id])->fetch();
        
        if($result->status == SubtaskStatus::COMPLETE) {
            $adapter = DefaultAdapter::getAdapter($task_data->method);
            return $adapter->getUrl($path);
        } else {
            return $task_data->directory . $result->file;
        }
    }
}
