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
        $this->help();
    }
    
    public function exec() {
        $last_msg = '';
        $callables = [
            'taskExecutePre' => function($task) {
                $this->out('Executing task ' . $task['task_id'] . ' with root: "'.$task['directory'].'"', 1, Shell::QUIET);
            },
            'subtaskExecutePre' => function($subtask) {
                    $this->out('[TRANSFER]: ' . $subtask['file'], 0, Shell::QUIET);
                },
            'subtaskExecutePost' => function($subtask) {
                    $this->_io->overwrite('[OK]: ' . $subtask['file'], 1);
                },
            'subtaskOnException' => function ($subtask, $exception) {
                    $this->_io->overwrite('[FAIL]: ' . $subtask['file'], 1);
                    $this->out('[Exception]: ' . $exception->getMessage(), 1, Shell::QUIET);
                }
        ];
        $stats = Task::tick($callables);
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
    
    public function addfiles($param1, $param2 = null) {
        $this->addfile($param1, $param2);
    }
    
    public function addfile($param1, $param2 = null) {
        if(is_string($param1) && $param2 === null) {
            $task = Task::getById();
            $pattern = $param1;
        } else {
            $task = Task::getById($param1);
            $pattern = $param2;
        }
        
        $this->out('Files added: ' . count($task->addfile($pattern)), 1, Shell::QUIET);
    }
    
    public function help() {
        $this->out('Methods of Task: ', 1, Shell::QUIET);
        $this->out('add "<directory>" ["<method>" ["<datetime>"]] - create new task. ', 1, Shell::QUIET);
        $this->out('      directory - place, where files can be found.' . $this->nl(1)
                .  '      method    - which service use to store files.' . $this->nl(1)
                .  '      datetime  - execute task after this date/time. ', 1, Shell::QUIET);
        $this->out('addfiles <task_id> "<pattern>" - add files to task.' . $this->nl(1)
                .  '      task_id   - id of created task.' . $this->nl(1)
                .  '      pattern   - add files, that can be found by pattern.', 1, Shell::QUIET);
        $this->out('addfile <task_id> "<pattern>" - same as addfiles.', 1, Shell::QUIET);
        $this->out('geturl "<path>" - get link to file.', 1, Shell::QUIET);
        $this->out('exec - analyse and execute tasks.', 1, Shell::QUIET);
    }
    
    public function geturl($path) {
        $this->out('Generating link... ', 0, Shell::QUIET);
        $this->_io->overwrite('Generated link: ' . Task::getUrlBase($path), 1);
    }
}
