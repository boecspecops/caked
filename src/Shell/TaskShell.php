<?php
namespace CakeD\Shell;

use Cake\Console\Shell;
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
        $tasks = count($stats);
        $files = 0;
        $completed = 0;
        
        foreach($stats as $stat) {
            $files += $stat["subtasks"];
            $completed += $stat["success"];
        }
        
        $this->out('Tasks executed: ' . $tasks . '; Files sent: ' . $completed . '/' . $files, 1, Shell::QUIET);
    }
    
    public function add( $directory, $method = "DROPBOX", $exec_time = null) {
        $exec_time === null ? : $exec_time = new \DateTime($exec_time);
        $task = Task::add($method, $directory, $exec_time);
        $this->out("Creating task with root directory: $directory. Method: $method", 1, Shell::QUIET);
        $this->out('Created new task with id: ' . $task["task_id"], 1, Shell::QUIET);
    }
    
    public function addfiles($task_id, $pattern) {
        $this->addfile($task_id, $pattern);
    }
    
    public function addfile($task_id, $pattern) {
        $task = Task::getById($task_id);
        $this->out('Files added: ' . count($task->addfile($pattern)), 1, Shell::QUIET);
    }
    
    public function help() {
        $this->out('Methods of Task: ', 1, Shell::QUIET);
        $this->out('add "<directory>" ["<method>" ["<datetime>"]] - create new task. ', 1, Shell::QUIET);
        $this->out('      directory - place, where files can be found.\n'
                .  '      method    - which service use to store files.\n'
                .  '      datetime  - execute task after this date/time. ', 1, Shell::QUIET);
        $this->out('addfiles <task_id> "<pattern>" - add files to task.\n'
                .  '      task_id - id of created task.\n'
                .  '      pattern - add files, that can be found by pattern.', 1, Shell::QUIET);
        $this->out('addfile <task_id> "<pattern>" - same as addfiles.', 1, Shell::QUIET);
    }
}
