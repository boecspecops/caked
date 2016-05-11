<?php

namespace CakeD\Core\Transfer\Configs;
use Symfony\Component\Yaml\Yaml;
use CakeD\Core\Exceptions\ConfigException;

/**
 * Классы конфигураций упрощают хранение/изменение настроек поумолчанию,
 * а так же позволяют получить ссылку на базовый каталог сервера, в который 
 * загружаются файлы.
 */

class DefaultConfig implements \ArrayAccess{
    protected $data = null;
    
    protected static function parse($config) {
        return Yaml::parse( file_get_contents($config) );
    }
    
    protected function __construct($config) {
        if(is_string($config)) {
            $config = self::parse($config);
        } 
        if(is_array($config)) {
            $this->config = $config;
        }
    }
        
    public final static function getAdapter($config) {
        $config = self::parse($config);
        $config['adapter'] !== null ? $a_name = \strtoupper(\trim($config['adapter'])) : $a_name = null;
        switch($a_name) {
            case "FTP": {
                return FTPConfig::invokeAdapter($config);
            }
            case "DROPBOX": {
                return DropboxConfig::invokeAdapter($config);
            }
            case null: {
                throw new ConfigParamNotFound("[Config] Adapter not selected.");
            }
            default: {
                throw new ConfigParamNotFound("[Config] Adapter " . $a_name . " not found.");
            }
        }
    }
    
    public final static function parseConfig($config) {
        $config = self::parse($config);
        $config['adapter'] !== null ? $a_name = \strtoupper(\trim($config['adapter'])) : $a_name = null;
        switch($a_name) {
            case "FTP": {
                return new FTPConfig($config);
            }
            case "DROPBOX": {
                return new DropboxConfig($config);
            }
            case null: {
                throw new ConfigException("[Config] Adapter not selected.");
            }
            default: {
                throw new ConfigException("[Config] Adapter " . $a_name . " not found.");
            }
        }
    }
    
    public function set(array $config) {
        $this->data = array_replace_recursive($this->data, $config);
    }

    public function offsetSet($offset, $value) {}
    
    public function offsetExists($offset) {
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->data[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }
}
