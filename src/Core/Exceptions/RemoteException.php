<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CakeD\Core\Exceptions;

use Cake\Core\Exception\Exception;


class RemoteException extends Exception {
    protected $_messageTemplate = 'Remote server throws exception: %s.';
    
    
    public function _displayError($error, $debug)
    {
        return 'There has been an error!';
    }
    
    public function _displayException($exception)
    {
        return 'There has been an exception!';
    }
}
