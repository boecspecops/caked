<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CakeD\Core\Status;

/**
 * Description of SubtaskStatus
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