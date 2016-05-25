<?php
namespace CakeD\Core\Transfer\Adapters;

use CakeD\Core\Exceptions;
use Cake\Core\Configure;

/**
 * FTP adapter provides basic functionality to communicate with FTP servers.
 *
 * @author boecspecops
 */
class FTPAdapter implements AdapterInterface {
    private $instance;
    private $config = null;
    
    public function __construct() {
        if($this->config === null) {
            $this->config = Configure::load('CakeD.config')['FTP'];
        }
        
        $this->instance = $this->getClient();
                
        if(!$this->dir_exists($this->config['directory']['root']) &&
                $this->config['directory']['create'])
        {
            $this->mkdir($this->config['directory']['root']);
        }
        
        $this->cd($this->config['directory']['root']);
    }
    
    
    public function getClient() {
        $conn = $this->config['connection'];
        
        if($this->config['ssl']) {
            $client = ftp_ssl_connect($conn['server'], $conn['port'], $conn['timeout']);
        }
        else {
            $client  = ftp_connect($conn['server'], $conn['port'], $conn['timeout']);
        }
        
        if($client == false) {
            throw(new Exceptions\ConnectionReset("Can't connect to server."));
        }
        
        if(!ftp_login($client, $conn['login'], $conn['password'])) {
            throw(new Exceptions\RemoteAuthFailed(["adapter" => "FTP"]));
        }
        
        return $client;
    }
    
    public function __destruct()
    {
        ftp_close( $this->instance );
    }
    
    public function write($root, $localfile) {
        $filelist = ftp_nlist($this->instance, './');
        
        if(in_array($localfile, $filelist) && !$this->config['rw'] )
        {
            throw(new Exceptions\RemoteException("[FTP] file writing failed. File alredy exists."));
        }    
                
        if(!ftp_put($this->instance, $file_name, $localfile, $this->config['method'])) {
            throw(new Exceptions\RemoteException("[FTP] Write file failed. Permission denied."));
        }
    }
    
    public function chmod($permissions, $path)
    {        
        if(!ftp_chmod($this->instance, $permissions, $path))
        {
            throw(new Exceptions\RemoteException("[FTP] Can't set permissions. Access denied for: ". $path));
        }
    }
    
    public function is_dir($path) {
        return ftp_nlist($this->instance, $path) !== false;
    }
    
    public function dir_exists($path)
    {
        if($this->is_dir($path))
        {
            $curr_dir = ftp_pwd($this->instance);
            if(@ftp_chdir($this->instance, $path))
            {
                @ftp_chdir($this->instance, $curr_dir);
                return true;
            }
        }
        return false;
    }
    
    public function cd($dir) {
        
        if(!@ftp_chdir($this->instance, $dir))
        {
            throw(new Exceptions\RemoteException("[FTP] Can't change directory. Access denied for: ". $dir));
        }
    }
    
    public function mkdir($dir) {
        $parts = explode(DS, $dir);
        $cur_dir = ftp_pwd( $this->instance );
        foreach($parts as $part) {
            if(!$this->dir_exists($part)) {
                $this->make_singe_directory($part);                
            }
            $this->cd($part);
        }
        $this->cd($cur_dir);
    }
    
    private function make_singe_directory($dir) {
        $result = ftp_mkdir($this->instance, $dir);
        
        if($result) {
            if($this->config['directory']['permissions']) {
                $this->chmod($this->config['directory']['permissions'], $dir);
            }
        } 
        else {
            throw(new Exceptions\RemoteException("[FTP] Can't create directory. Access denied for: ". $dir));
        }
        
        return $result;
    }
}
