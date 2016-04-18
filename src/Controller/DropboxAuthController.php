<?php

namespace CakeD\Controller;
use Dropbox as dbx;

class DropboxAuthController extends AppController {
    
    public function index() {
        if ($this->request->is('post')) {
            $app = $this->request->data['appname'];
            $key = $this->request->data['key'];
            $secret = $this->request->data['secret'];
            $appInfo = dbx\AppInfo($key, $secret);
            $webAuth = new dbx\WebAuthNoRedirect($appInfo, $app);
            $authorizeUrl = $webAuth->start();
            echo "1. Go to: " . $authorizeUrl . "\n";
            echo "2. Click \"Allow\" (you might have to log in first).\n";
            echo "3. Copy the authorization code.\n";
            $authCode = \trim(\readline("Enter the authorization code here: "));
            list($accessToken, $dropboxUserId) = $webAuth->finish($authCode);
            print "Access Token: " . $accessToken . "\n";
        }
    }
    
}
