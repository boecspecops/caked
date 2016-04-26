<?php

namespace CakeD\Core\Transfer\Configs;
use CakeD\Core\Exceptions\AdapterException;
use CakeD\Core\Transfer\Adapters\FTPAdapter;
use CakeD\Core\Transfer\Configs\ConfigInterface;

class FTPConfig extends DefaultConfig implements ConfigInterface {
    
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
            'permissions'   => false        # false - использовать серверные настройки.
        ],
        'mode'      => 'rw',                # rw - переписать файл, если существует.
                                            # любой другой файл будет вызывать проверку на его существование.
        'method'    => FTP_BINARY,          # FTP_BINARY/FTP_ASCII
        'ssl'       => false                # Использовать ssl?
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
    
    public function getClient() {
        $conn = $this->data['connection'];
        
        if($this->data['ssl']) {
            $client = ftp_ssl_connect($conn['server'], $conn['port'], $conn['timeout']);
        }
        else {
            $client  = ftp_connect($conn['server'], $conn['port'], $conn['timeout']);
        }
        
        if($client == false) {
            throw(new AdapterException("[FTP] connection failed."));
        }
        
        if(!ftp_login($client, $conn['login'], $conn['password'])) {
            throw(new AdapterException("[FTP] authorization failed."));
        }
        
        return $client;
    }
    
    public function getUrlBase($filename = "") {
        $client = $this->getClient();
        ftp_chdir($client, $this->data["directory"]["root"]);
        
        if($this->data["ssl"]) {
            $url = "ftps://";
        } else {
            $url = "ftp://";
        }
        
        $url .= $this->data["connection"]["server"] . "/" .
                $this->data["directory"]["root"] . "/" .  $filename;
        
        return $url;
    }
}
