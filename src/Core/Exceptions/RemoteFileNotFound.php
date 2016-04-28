<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CakeD\Core\Exceptions;

use Cake\Core\Exception\Exception;


class RemoteFileNotFound extends Exception {
    protected $_messageTemplate = 'File %s not found on remote server.';
    
    
    public function _displayError($error, $debug) {
        return 'There has been an error!';
    }
    
    public function _displayException($exception) {
        return 'There has been an exception!';
    }
}
