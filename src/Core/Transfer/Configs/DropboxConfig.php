<?php

namespace CakeD\Core\Transfer\Configs;
use Dropbox as dbx;
use CakeD\Core\Exceptions\AdapterException;
use CakeD\Core\Transfer\Adapters\DropboxAdapter;
use CakeD\Core\Transfer\Configs\ConfigInterface;


class DropboxConfig extends DefaultConfig implements ConfigInterface {
    
    protected $data = [
        'connection' => [
            'token' => null,
        ],
        'directory' => [
            'root' => '/'
        ],
        'mode' => 'rw'
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
    
    public function getClient() {
        $connection = $this->data['connection'];
        if($connection['token'] !== null) {
            $client = new dbx\Client($connection['token'], $this->config['directory']['root']);
        } else {
            throw(new AdapterException("[Dropbox] Can't detect access token."));
        }
        
        return $client;
    }
    
    public function getUrlBase() {
        
    }
}
