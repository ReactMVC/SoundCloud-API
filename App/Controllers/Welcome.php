<?php

namespace Monster\App\Controllers;

use Monster\App\Models\CORS;
use Monster\App\Models\Env;
use Monster\App\Models\Json;

class Welcome
{
    public function index()
    {
        $env = new Env('.env');
        $app_url = $env->get("APP_URL");
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
                'get' => 'for get music info ' . $app_url . '/api/get?url=xxx',
                'download' => 'for download music' . $app_url . '/api/download?url=xxx',
                'search' => 'for search music' . $app_url . '/api/search?text=xxx'
            ]
        ];

        $json = new Json();
        $json->clean($data);
    }
}