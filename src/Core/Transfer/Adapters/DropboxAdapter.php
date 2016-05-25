<?php

namespace CakeD\Core\Transfer\Adapters;
use Dropbox as dbx;
use CakeD\Core\Exceptions;
use Cake\Core\Configure;

/**
 * This adapter provides basic functionality to write files on dropbox servers
 * using their API.
 *
 * @author boecspecops
 */
class DropboxAdapter implements AdapterInterface {
    
    private $instance;
    private $config = null;
    
    public function __construct() {
        Configure::load('CakeD.config');
        $this->config = Configure::read('DROPBOX');
        
        $this->instance = $this->getClient();
    }
       
    public function getClient() {
        if($this->config['token'] === null) {
            throw(new Exceptions\ConfigParamNotFound('Parameter token is null.'));
        }
        if($this->config['directory'] === null) {
            $this->config['directory'] = '/';
        }
        if($this->config['mode'] === null) {
            $this->config['mode'] = 'rw';
        }
        
        if($this->config['token'] !== null) {
            $client = new dbx\Client($this->config['token'], $this->config['directory']);
        } else {
            throw(new Exceptions\ConfigParamNotFound('Parameter token is null.'));
        }
        
        return $client;
    }
    
    public function write($localfile) {
        $path = $this->config['directory'];
        
        $f = fopen($localfile, "rb");
        
        if(!$f) {
            throw(new Exceptions\FileNotFound("[DROPBOX] File \"$localfile\" not found."));
        }
        
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
            echo 'i\'m trying this!!! ' . $path . $localfile;
            $this->instance->uploadFile($path . $localfile, $request, $f);
        }
        catch(dbx\Exception_NetworkIO $e) {
            throw(new Exceptions\ConnectionReset($e->getMessage()));
        } 
        finally {
            fclose($f);
        }
        echo "\nДеревянными членами торгует!";
    }
    
    public function is_dir($path) {
        
    }
    
    public function dir_exists($path) {
        
    }
}
