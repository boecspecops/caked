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
}
