<?php

namespace CakeD\Core\Transfer\Adapters;
use Dropbox as dbx;
use CakeD\Core\Transfer\Configs\DropboxConfig;

/**
 * This adapter provides basic functionality to write files on dropbox servers
 * using their API.
 *
 * @author boecspecops
 */
class DropboxAdapter implements AdapterInterface {
    
    private $instance;
    private $config = null;
    
    public function __construct($config) {
        $this->config = new DropboxConfig($config);
        $this->instance = $this->config->getClient();
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
        $this->instance->uploadFile($path . $file_name, $request, $f);
        fclose($f);
    }
    
    public function is_dir($path) {
        
    }
    
    public function dir_exists($path) {
        
    }
}
