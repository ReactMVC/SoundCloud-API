<?php

namespace Monster\App\Controllers;

use Monster\App\Models\CORS;
use Monster\App\Models\Json;

class Welcome
{
    public function index()
    {
        $cors = new CORS();
        $cors->origin(['*'])
            ->methods(['GET', 'POST'])
            ->headers(['Content-Type', 'Authorization'])
            ->maxAge(0)
            ->credentials(false)
            ->setHeaders();

        $data = [
            'path' => '/',
            'message' => 'Welcome to SoundCloud API',
            'commands' => [
                'get' => 'for get music info /api/get?url=xxx',
                'download' => 'for download music /api/download?url=xxx'
            ]
        ];

        $json = new Json();
        $json->clean($data);
    }
}