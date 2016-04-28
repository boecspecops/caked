<?php

namespace CakeD\Core\Exceptions;

use Cake\Core\Exception\Exception;


class ConnectionReset extends Exception {
    protected $_messageTemplate = 'Seems that %s is missing.';
    
    
    public function _displayError($error, $debug)
    {
        return 'There has been an error!';
    }
    
    public function _displayException($exception)
    {
        return 'There has been an exception!';
    }
}
