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
        $stats = Task::tick();
        $this->out('Ok. Files sent ' . $stats["subtasks"] . '/' . $stats["success"] . '.', 1, Shell::QUIET);
    }
    
    public function add($pattern, $method = "DROPBOX", $exec_time = null) {
        $exec_time === null ? : $exec_time = new \DateTime($exec_time);
        $task = Task::add($method, $exec_time);
        $task->addfile($pattern);
    }
    
    public function addfile($task_id, $pattern) {
        $task = Task::getById($task_id);
        $task->addfile($pattern);
    }
}
