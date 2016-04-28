<?php


namespace CakeD\Core\Exceptions;

use Cake\Core\Exception\Exception;


class FileNotFound extends Exception {
    protected $_messageTemplate = 'File %s not found.';
    
    
    public function _displayError($error, $debug)
    {
        return 'There has been an error!';
    }
    public function _displayException($exception)
    {
        return 'There has been an exception!';
    }
}
