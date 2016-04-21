<?php

namespace CakeD\Core\Transfer\Configs;
use CakeD\Core\Transfer\Adapters\FTPAdapter;

class FTPConfig extends DefaultConfig {
    
    protected $data = [
        'connection'=> [
                'server'    => '127.0.0.1',
                'login'     => 'anonymous',
                'password'  => '',
                'port'      => 21,
                'timeout'   => 90
        ],
        'directory' => [
            'root'          => '/',
            'create'        => true,
            'permissions'   => false        # false - use server's default value.
        ],
        'rewrite'   => true,
        'method'    => FTP_BINARY,
        'ssl'       => false
    ];
    
    public static function invokeAdapter($config) {
        return new FTPAdapter($config);
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
        $url = "ftp://" . $this->data["connection"]["server"];
        $url.= $this->data["directory"]["root"] . "/";
        return $url;
    }
}
