<?php

namespace CakeD\Core\Transfer\Adapters;
use Dropbox as dbx;
use CakeD\Core\Transfer\Configs\DropboxConfig;
use CakeD\Core\Exceptions;

/**
 * This adapter provides basic functionality to write files on dropbox servers
 * using their API.
 *
 * @author boecspecops
 */
class DropboxAdapter implements AdapterInterface {
    
    private $instance;
    private $config = [
            'connection' => [
                'token' => null,
            ],
            'directory' => [
                'root' => '/'
            ],
            'mode' => 'rw'
        ];
    
    public function __construct() {
        if($this->config === null) {
            $this->config = Configure::load('CakeD.config')['DROPBOX'];
        }
        $this->instance = $this->config->getClient();
    }
       
    public function getClient() {
        $connection = $this->config['token'];
        if($connection !== null) {
            $client = new dbx\Client($connection, $this->config['directory']);
        } else {
            throw(new Exceptions\ConfigParamNotFound(["param" => "token"]));
        }
        
        return $client;
    }
    
    public function write($localfile, $file_name = Null) {        
        if($file_name === Null) {
            $file_name = basename($localfile);
        }
        
        $path = $this->config['directory']['root'];
        
        $f = fopen($localfile, "rb");
        switch($this->config["mode"]) {
            case "rw": {
                $request = dbx\WriteMode::force();
                break;
            }
            case "a": {
                $request = dbx\WriteMode::add();
                break;
            }
        }
        try{
            $this->instance->uploadFile($path . $file_name, $request, $f);
        }
        catch(dbx\Exception_NetworkIO $e) {
            throw(new Exceptions\ConnectionReset($e->getMessage()));
        } 
        finally {
            fclose($f);
        }
    }
    
    public function is_dir($path) {
        
    }
    
    public function dir_exists($path) {
        
    }
}
