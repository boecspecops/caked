<?php

namespace CakeD\Core\Status;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TaskStatus
 *
 * @author boecspecops
 */
class TaskStatus {
    const WAIT          = 1;
    const CONNECTING    = 2;
    const ERROR         = 3;
    const PROCESSING    = 4;
    const COMPLETE      = 5;
    const PAUSED        = 6;           
}