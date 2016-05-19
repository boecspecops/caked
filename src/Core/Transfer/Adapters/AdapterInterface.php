<?php

namespace CakeD\Core\Transfer\Adapters;


interface AdapterInterface {
        
    public function __construct($config);
    
    public function getClient();
    
    public function write($file, $path = null);
    
    public function is_dir($path);
    
    public function dir_exists($path);
}
