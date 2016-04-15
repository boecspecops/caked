<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CakeD\Shell\Task;

use Cake\Console\Shell;
use CakeD\Core\Task;

class TaskTask extends Shell
{
    public function main($task)
    {
        Task::init_and_execute($task);
    }
}
