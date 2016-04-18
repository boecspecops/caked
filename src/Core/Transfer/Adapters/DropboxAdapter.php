<?php

namespace CakeD\Core\Transfer\Adapters;
use Dropbox;

/**
 * This adapter provides basic functionality to write files on dropbox servers
 * using their API.
 *
 * @author boecspecops
 */
class DropboxAdapter extends AbstractAdapter {
    
    private $instance;
    private $config = [
        'connection' => [
            'token' => null,
        ],
        'directory' => [
            'root' => '/'
        ]
    ];
    
    public function __construct($config) {
        $this->config = array_replace_recursive($this->config, $config);
        
        
        $connection = $this->config;
        if($connection['token'] !== null) {
            $this->instance = new dbx\Client($connection['token'], $this->config['directory']['root']);
        } else {
            throw(new AdapterException("[Dropbox] Can't detect access token."));
        }
    }
    
    public function write($localfile, $file_name = Null) {        
        if($file_name === Null) {
            $file_name = basename($localfile);
        }
        
        $f = fopen($localfile, "rb");
        $this->instance->uploadFile($file_name, Dropbox\WriteMode::add(), $f);
        fclose($f);
    }
    
    public function is_dir($path) {
        
    }
    
    public function dir_exists($path) {
        
    }
}
