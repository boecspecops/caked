<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CakeD\Core;
use Symfony\Component\Yaml\Yaml;


/**
 * Description of Core
 *
 * @author boecspecops
 */
class Core {
    private static $config = [
        'limitations' => [
            'max_tasks' => 4
        ]
    ];
    
    public static function getConfig() {
        $config = [];
        self::$config = array_replace_recursive(self::$config, $config);
        return self::$config;
    }
}
