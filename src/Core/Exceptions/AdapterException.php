<?php

namespace CakeD\Core\Exceptions;

use Cake\Core\Exception\Exception;

class AdapterException extends Exception{    
    protected $_messageTemplate = 'Seems that %s is missing.';
}
