<?php
namespace CakeD\Shell;

use Cake\Console\Shell;
use CakeD\Core\Core;
use CakeD\Core\Task;

/**
 * TMDaemon shell command.
 */
class TaskShell extends Shell
{
    public $tasks = ['CakeD.Task'];
    /**
     * Manage the available sub-commands along with their arguments and help
     *
     * @see http://book.cakephp.org/3.0/en/console-and-shells.html#configuring-options-and-generating-help
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        return $parser;
    }
    
    public function main() 
    {
        $tasks = Task::getIncompletedTasks();
        foreach($tasks as $task) {
            if(Task::count() < Core::getConfig()['limitations']['max_tasks']) {
                $this->Task->main($task);
            } else {
                break;
            }            
        }
    }
    
    public function add($pattern, $exec_time, $method = "DROPBOX") {
        $task = Task::addTask($method, $exec_time);
        $task->addfile($pattern);
    }
}
