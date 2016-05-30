<?php

namespace CakeD\Core\Transfer\Adapters;


interface AdapterInterface {
        
    public function __construct();
    
    public function getClient();
    
    public function write($root, $file);
    
    public function is_dir($path);
    
    public function dir_exists($path);
}
