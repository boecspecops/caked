<?php

namespace CakeD\Core;

class Core {
    
    public static function getConfig() {
        return array(
            "limitations" => [
                "max_tasks" => 5
            ]
        );
    }
}
