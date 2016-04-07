<?php
namespace CakeD\Core\Transfer\Adapters;

use CakeD\Core\Exceptions\AdapterException;

/**
 * Description of AdapterFTP
 *
 * @author boecspecops
 */
class AdapterFTP extends AbstractAdapter {
    private $instance;
    private $config = [
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
    
    public function __construct($config) {
        $this->config = array_replace_recursive($this->config, $config);
                
        $conn = $this->config['connection'];
        
        if($this->config['ssl']) {
            $this->instance = ftp_ssl_connect($conn['server'], $conn['port'], $conn['timeout']);
        }
        else {
            $this->instance = ftp_connect($conn['server'], $conn['port'], $conn['timeout']);
        }
        
        if($this->instance == false) {
            throw(new AdapterException("[FTP] connection failed."));
        }
        
        if(!ftp_login($this->instance, $conn['login'], $conn['password'])) {
            throw(new AdapterException("[FTP] authorization failed."));
        }
        
        if(!$this->dir_exists($this->config['directory']['root']) &&
                $this->config['directory']['create'])
        {
            $this->mkdir($this->config['directory']['root']);
        }
        
        $this->cd($this->config['directory']['root']);
    }
    
    public function __destruct()
    {
        ftp_close( $this->instance );
    }
    
    public function write($localfile, $file_name = Null) {        
        if($file_name === Null) {
            $file_name = basename($localfile);
        }
        
        $filelist = ftp_nlist($this->instance, './');
        
        if(in_array($file_name, $filelist) && !$this->config['rewrite'] )
        {
            throw(new AdapterException("[FTP] file writing failed. File alredy exists."));
        }    
                
        if(!ftp_put($this->instance, $file_name, $localfile, $this->config['method'])) {
            throw(new AdapterException("[FTP] file writing failed."));
        }
    }
    
    public function chmod($permissions, $path)
    {        
        if(!ftp_chmod($this->instance, $permissions, $path))
        {
            throw(new AdapterException("[FTP] Can't set permissions. Access denied for: ". $path));
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
            throw(new AdapterException("[FTP] Can't change directory. Access denied for: ". $dir));
        }
        
        return $result;
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
            throw(new AdapterException("[FTP] Can't create directory. Access denied for: ". $dir));
        }
        
        return $result;
    }
            
}
