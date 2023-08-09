<?php

namespace Monster\App\Controllers;

use DarkPHP\SoundCloud;
use Monster\App\Models\CORS;
use Monster\App\Models\Env;
use Monster\App\Models\Json;

class SoundController
{
    public function download()
    {
        $cors = new CORS();
        $cors->origin(['*'])
            ->methods(['GET', 'POST'])
            ->headers(['Content-Type', 'Authorization'])
            ->maxAge(0)
            ->credentials(false)
            ->setHeaders();

        if (!isset($_GET['url'])) {
            echo "Please enter the url parameter.";
        } else {
            $url = $_GET['url'];
            $soundcloud = new SoundCloud();
            $get = $soundcloud->getMusicWithUrl($url);
            if ($get) {
                $id = $get['id'];
                $soundcloud->downloadMusic($id);
            } else {
                echo "Error in download.";
            }
        }
    }

    public function downloadPOST()
    {
        $cors = new CORS();
        $cors->origin(['*'])
            ->methods(['GET', 'POST'])
            ->headers(['Content-Type', 'Authorization'])
            ->maxAge(0)
            ->credentials(false)
            ->setHeaders();

        if (!isset($_POST['url'])) {
            echo "Please enter the url parameter.";
        } else {
            $url = $_POST['url'];
            $soundcloud = new SoundCloud();
            $get = $soundcloud->getMusicWithUrl($url);
            if ($get) {
                $id = $get['id'];
                $soundcloud->downloadMusic($id);
            } else {
                echo "Error in download.";
            }
        }
    }

    public function get()
    {
        $json = new Json();
        $cors = new CORS();
        $env = new Env('.env');
        $app_url = $env->get("APP_URL");
        $cors->origin(['*'])
            ->methods(['GET', 'POST'])
            ->headers(['Content-Type', 'Authorization'])
            ->maxAge(0)
            ->credentials(false)
            ->setHeaders();

        if (!isset($_GET['url'])) {
            http_response_code(400);
            $json->clean(['status' => false, 'code' => 400, 'message' => 'Please enter the url parameter.']);
        } else {
            $url = $_GET['url'];
            $soundcloud = new SoundCloud();
            $get = $soundcloud->getMusicWithUrl($url);
            if ($get) {
                $link = ['download' => $app_url . '/api/download?url=' . $url];
                $api = array_merge($get, $link);
                $json->clean($api);
            } else {
                http_response_code(403);
                $json->clean(['status' => false, 'code' => 403, 'message' => 'Error in get info.']);
            }
        }
    }

    public function getPOST()
    {
        $json = new Json();
        $cors = new CORS();
        $env = new Env('.env');
        $app_url = $env->get("APP_URL");
        $cors->origin(['*'])
            ->methods(['GET', 'POST'])
            ->headers(['Content-Type', 'Authorization'])
            ->maxAge(0)
            ->credentials(false)
            ->setHeaders();

        if (!isset($_POST['url'])) {
            http_response_code(400);
            $json->clean(['status' => false, 'code' => 400, 'message' => 'Please enter the url parameter.']);
        } else {
            $url = $_POST['url'];
            $soundcloud = new SoundCloud();
            $get = $soundcloud->getMusicWithUrl($url);
            if ($get) {
                $link = ['download' => $app_url . '/api/download?url=' . $url];
                $api = array_merge($get, $link);
                $json->clean($api);
            } else {
                http_response_code(403);
                $json->clean(['status' => false, 'code' => 403, 'message' => 'Error in get info.']);
            }
        }
    }

}