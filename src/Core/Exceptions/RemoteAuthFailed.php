<?php

namespace CakeD\Core\Exceptions;

use Cake\Core\Exception\Exception;


class RemoteAuthFailed extends Exception {
    protected $_messageTemplate = "Can\'t authorize on %s server. Wrong username or password.";
    
    
    public function _displayError($error, $debug)
    {
        return 'There has been an error!';
    }
    public function _displayException($exception)
    {
        return 'There has been an exception!';
    }
}
