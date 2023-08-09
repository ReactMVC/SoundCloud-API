<?php
namespace DarkPHP;

class Request
{
    public static string $JSON_CONTENT_TYPE = "application/json";
    public static string $URL_ENCODED_CONTENT_TYPE = "application/x-www-form-urlencoded";
    public static function post(string $url, array $headers = [], mixed $data = ''): bool|string
    {
        return self::endpoint($url, true, $headers, $data);
    }

    public static function get(string $url, array $headers = [], mixed $data = ''): bool|string
    {
        return self::endpoint($url, false, $headers, $data);
    }
    public static function arrayToJson(array $value): bool|string
    {
        return json_encode($value);
    }
    public static function arrayToUrlEncode(array $value): bool|string
    {
        return http_build_query($value);
    }
    private static function endpoint(string $url, $post = false, $headers = [], mixed $data = ''): bool|string
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, $post);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        if ($data) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        $resp = curl_exec($curl);
        curl_close($curl);
        return $resp;
    }
}
