<?php

namespace CakeD\Core;
use Symfony\Component\Yaml\Yaml;
use Migrations\Migrations;
use Composer\Script\Event;

class Core {
    
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
}
