<?php

namespace CakeD\Core\Transfer\Configs;
use Symfony\Component\Yaml\Yaml;
use \CakeD\Core\Exceptions;

/**
 * Классы конфигураций упрощают хранение/изменение настроек поумолчанию,
 * а так же позволяют получить ссылку на базовый каталог сервера, в который 
 * загружаются файлы.
 */

class DefaultConfig implements \ArrayAccess{
    protected $data = null;
    
    protected static function parse($config) {
        if(is_array($config)) {
            return $config;
        } elseif(!file_exists($config)) {
            throw(new Exceptions\FileNotFound("[Config] File not found: " . $config));
        }
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
                throw new Exceptions\ConfigParamNotFound("[Config] Adapter not selected.");
            }
            default: {
                throw new Exceptions\ConfigParamNotFound("[Config] Adapter " . $a_name . " not found.");
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
                throw new Exceptions\ConfigParamNotFound("[Config] Adapter not selected.");
            }
            default: {
                throw new Exceptions\ConfigParamNotFound("[Config] Adapter " . $a_name . " not found.");
            }
        }
    }
    
    public function set(array $config) {
        $this->data = array_replace_recursive($this->data, $config);
    }
    
    public function as_array() {
        return is_array($this->data) ? $this->data : [];
    }

    public function offsetSet($offset, $value) {
        $this->data[$offset] = $value;
    }
    
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
