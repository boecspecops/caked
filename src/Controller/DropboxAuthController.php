<?php

namespace CakeD\Controller;
use Dropbox as dbx;
use Cake\Network\Session;

class DropboxAuthController extends AppController {
    
    public function index() {
        $session = $this->request->session();
        
        if ($this->request->is('post')) {
            
            /* TODO:
             * Доделать авторизацию dropbox;
             */
            
            $app = $this->request->data('Appname');
            $key = $this->request->data('Key');
            $secret = $this->request->data('Secret');
            if($app !== null && $key !== null && $secret !== null) {
                $dbx_cfg = ['app' => $app, 'key'=>$key, 'sec'=>$secret];
                $session->write('CakeD.dropbox.auth_data', $dbx_cfg);
            } else {
                $dbx_cfg = $session->consume('CakeD.dropbox.auth_data');
            }
            
            $appInfo = new dbx\AppInfo($dbx_cfg['key'], $dbx_cfg['sec']);
            $webAuth = new dbx\WebAuthNoRedirect($appInfo, $dbx_cfg['app']);
            $authorizeUrl = $webAuth->start();
            
            $this->set('auth_url', $authorizeUrl);
            
            if($this->request->data('AuthCode')) {
                list($accessToken, $dropboxUserId) = $webAuth->finish($this->request->data('AuthCode'));
                $this->set('token', $accessToken);
            }
        }
    }
    
    
}
