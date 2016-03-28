<?php

namespace CakeD\Core\Transfer\Adapters;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AbstractAdapter
 *
 * @author boecspecops
 */
class AbstractAdapter {
    
    private $instance;
    private $config = [];
    
    public function __construct($config) {
        $this->config = array_replace_recursive($this->config, $config);
    }
    
    public function __destruct();
    
    public function write($file, $path = null);
    
    public function read($path);
    
    public function is_dir($path);
    
    public function dir_exists($path);
}
