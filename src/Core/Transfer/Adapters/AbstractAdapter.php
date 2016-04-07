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
abstract class AbstractAdapter {
    
    private $instance;
    private $config = [];
    
    abstract public function __construct($config);
    
    abstract public function write($file, $path = null);
    
    abstract public function is_dir($path);
    
    abstract public function dir_exists($path);
}
