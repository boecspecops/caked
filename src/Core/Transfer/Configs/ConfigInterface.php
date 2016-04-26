<?php

namespace CakeD\Core\Transfer\Configs;


Interface ConfigInterface {
    public static function invokeAdapter($config);
    public function getClient();
    public function getUrlBase($filename = "");
}
