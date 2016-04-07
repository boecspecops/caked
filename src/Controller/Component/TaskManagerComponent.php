<?php
namespace CakeD\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;

use CakeD\Core\Task;

/**
 * TaskManager component
 */

class TaskManagerComponent extends Component
{

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];
    
    /**
     * Function creates a task.
     * 
     * @param type $config
     * @param type $exec_time
     * @return type
     */
    
    public function createTask($config, $exec_time = null) {
        if(is_string($exec_time)) {
            $interval = $exec_time;
            $exec_time = new \DateTime('now');
            $exec_time->add(new \DateInterval($interval));
        } else {
            $exec_time = new \DateTime('now');
        } 
            
        return Task::addTask($config, $exec_time);
    }
    
    /**
     * Function adds file/files to task.
     * 
     * @param type $task
     * @param type $files
     */
    
    public function addfile($task, $files) {
        return $task->addfile($files);
    }
    
    /**
     * Method analyses task list.
     * 
     * @return type
     */
    
    public function tick() {
        return Task::tick();
    }
}
