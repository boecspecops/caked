<?php

namespace CakeD\Core\Transfer\Adapters;

use CakeD\Core\Transfer\Adapters;
use CakeD\Core\Exceptions\AdapterException;

abstract class AbstractAdapter {
    
    private $instance;
    private $config = [];
    
    public final static function getAdapter($config) {
        $config['adapter'] !== null ? $a_name = \strtoupper(\trim($config['adapter'])) : $a_name = null;
        switch($a_name) {
            case "FTP": {
                return new Adapters\FTPAdapter($config);
            }
            case "DROPBOX": {
                return new Adapters\DropboxAdapter($config);
            }
            case null: {
                throw new AdapterException("[Task] Adapter not selected.");
            }
            default: {
                throw new AdapterException("[Task] Adapter " . $a_name . " not found.");
            }
        }
    }
    
    abstract public function __construct($config);
    
    abstract public function write($file, $path = null);
    
    abstract public function is_dir($path);
    
    abstract public function dir_exists($path);
}
