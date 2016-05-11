<?php

namespace CakeD\Core;
use Symfony\Component\Yaml\Yaml;
use Migrations\Migrations;
use Composer\Script\Event;

class Core {
    
    public static function getConfig() {
        return array(
            "limitations" => [
                "max_tasks" => 5
            ]
        );
    }
    
    public static function postUpdate(Event $event) {
        $composer = $event->getComposer();
        self::migrate();
    }
    
    public static function postInstall(Event $event) {
        $composer = $event->getComposer();
        self::migrate();
    }
    
    public static function migrate(Event $event) {
        $event->getComposer();
        $event->getIO()->write("Потому что это чёртов БЕЕЕЙНБЛЕЕЙД!!1");
    }
}
