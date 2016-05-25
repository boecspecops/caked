<?php

return [
    'FTP' => [
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
                                            # любое другое значение будет вызывать проверку файла на существование.
        'method'    => FTP_BINARY,          # FTP_BINARY/FTP_ASCII
        'ssl'       => false                # Использовать ssl?
    ],
    
    'DROPBOX' => [
        'token'     => 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX-YYYYYYYYY',
        'directory' => '/'
    ]
];
