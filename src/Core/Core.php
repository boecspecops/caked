<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace CakeD\Core;
use Symfony\Component\Yaml\Yaml;
use Migrations\Migrations;
use Composer\Script\Event;


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
    
    public static function postUpdate(Event $event) {
        $composer = $event->getComposer();
        self::migrate();
    }
    
    public static function postInstall(Event $event) {
        $composer = $event->getComposer();
        self::migrate();
    }
    
    private static function migrate() {
        $migrations = new Migrations(["plugin" => "CakeD"]);
        
        $migrations->migrate();
    }
    
    public static function getConfig() {
        $config = [];
        self::$config = array_replace_recursive(self::$config, $config);
        return self::$config;
    }
}
