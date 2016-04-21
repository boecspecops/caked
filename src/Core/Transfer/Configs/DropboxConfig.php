<?php

namespace CakeD\Core\Transfer\Configs;
use CakeD\Core\Transfer\Adapters\DropboxAdapter;


class DropboxConfig extends DefaultConfig {
    
    protected $data = [
        'connection' => [
            'token' => null,
        ],
        'directory' => [
            'root' => '/'
        ]
    ];
    
    public static function invokeAdapter($config) {
        return new DropboxAdapter($config);
    }
    
    public function __construct($config) {
        parent::__construct($this->data);
        if(is_string($config)) {
            $this->parse($config);
        } elseif(is_array($config)) {
            $this->set($config);
        }
    }
    
    public function getUrlBase() {
        
    }
}
