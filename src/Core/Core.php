<?php

namespace CakeD\Core;
use Symfony\Component\Yaml\Yaml;
use Migrations\Migrations;
use Composer\Script\Event;

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
        echo "[CakeD] Migrating...";
        $migrations = new Migrations(["plugin" => "CakeD"]);
        $migrations->migrate();
    }
    
    public static function getConfig() {
        $config = [];
        self::$config = array_replace_recursive(self::$config, $config);
        return self::$config;
    }
}
