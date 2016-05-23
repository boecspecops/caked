<?php

namespace CakeD\Core\Transfer\Adapters;

use CakeD\Core\Transfer\Adapters;
use CakeD\Core\Exceptions;

class DefaultAdapter {
    
    
    public static function getAdapter($method) {
        switch($method) {
            case "FTP": {
                return new Adapters\FTPAdapter();
            }
            case "DROPBOX": {
                return new Adapters\DropboxAdapter();
            }
            case Null: {
                throw new Exceptions\ConfigParamNotFound("[Config] Adapter not selected.");
            }
            default: {
                throw new Exceptions\ConfigParamNotFound("[Config] Adapter not found.");
            }
        }
    }
}
