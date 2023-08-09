<?php

namespace DarkPHP;

use Exception;
use stdClass;

class SoundCloud extends Request
{
    private string $soundCloudUrl = "https://soundcloud.com/";
    private string $soundCloudApiUrl = "https://api-v2.soundcloud.com/";
    private string $soundCloudAuthUrl = "https://api-auth.soundcloud.com/";

    public function __construct()
    {
        if (!$this->checkClientId()) {
            $this->setClientId();
        }
    }

    public function searchMusic(string $query): stdClass|array
    {
        try {
            $musics = [];
            $searchData = json_decode(
                self::get(
                    "{$this->soundCloudApiUrl}search/tracks?" . self::arrayToUrlEncode(
                        [
                            'q' => $query,
                            'sc_a_id' => ' 801c8a51-9227-40d9-9c11-ac16f6bca216',
                            'variant_ids' => '',
                            'facet' => 'genre',
                            'user_id' => '367935-565481-285242-514567',
                            'client_id' => $this->getClientId(),
                            'limit' => '20',
                            'offset' => '0',
                            'linked_partitioning' => '1',
                            'app_version' => '1667398191',
                            'app_locale' => 'en'
                        ]
                    )
                )
            )->collection;
            foreach ($searchData as $music) {
                if ($music->kind == 'track' and $music->streamable) {
                    $musics[] = [
                        'id' => $music->id,
                        "picture" => str_replace("large", "original", $music->artwork_url) ?? $music->user->avatar_url,
                        "name" => $music->title,
                        'release' => $music->created_at,
                        'genre' => $music->genre,
                        'duration' => $music->duration,
                        'soundcloudurl' => $music->permalink_url
                    ];
                }
            }
            return $musics;
        } catch (Exception $e) {
            return $this->internalError();
        }
    }

    public function getMusic(string $id, bool $exportOriginal = false): bool|stdClass|array
    {
        try {
            $musicData = json_decode(
                self::get(
                    "{$this->soundCloudApiUrl}tracks?" .
                    self::arrayToUrlEncode(
                        [
                            'ids' => $id,
                            'client_id' => $this->getClientId(),
                            '%5Bobject%20Object%5D' => '',
                            'app_version' => '1667398191',
                            'app_locale' => 'en'
                        ]
                    )
                )
            )[0];
            if (is_null($musicData)) {
                return false;
            } elseif ($exportOriginal) {
                return $musicData;
            } else {
                return [
                    'status' => true,
                    'id' => $id,
                    'picture' => str_replace("large", "original", $musicData->artwork_url) ?? $musicData->user->avatar_url,
                    'title' => $musicData->title,
                    'release' => $musicData->created_at,
                    'genre' => $musicData->genre,
                    'duration' => $musicData->duration,
                    'soundcloudurl' => $musicData->permalink_url
                ];
            }
        } catch (Exception $e) {
            return $this->internalError();
        }
    }

    public function downloadMusic(string $id): void
    {
        $dataMusic = $this->getMusic($id, true);
        $trackAuthorization = $dataMusic->track_authorization;
        $url = $dataMusic->media->transcodings[0]->url;
        $trackName = $dataMusic->title . '.mp3';
        $data = json_decode(
            self::get(
                "$url?" . self::arrayToUrlEncode(
                    [
                        'client_id' => $this->getClientId(),
                        'track_authorization' => $trackAuthorization
                    ]
                )
            )
        );
        $urls = $this->exportUrl($data->url);
        $this->downloadAndPlay($urls, $trackName);
    }

    public function getMusicWithUrl(string $url): bool|stdClass|array
    {
        if (
            filter_var($url, FILTER_VALIDATE_URL) and
            preg_match(
                '/^https?:\/\/(?:www\.)?(?:m\.)?soundcloud\.com\/[a-zA-Z0-9-_]+\/[a-zA-Z0-9-_]+(?:\/)?/m',
                $url,
            )
        ) {
            $webPageContent = self::get($url);
            preg_match_all('/soundcloud:\/\/sounds:(.*?)"/m', $webPageContent, $matches);
            if (isset($matches[1][0])) {
                return $this->getMusic($matches[1][0]);
            } else {
                return false;
            }
        } else {
            return false;
        }
        return false;
    }

    private function setClientId(): void
    {
        $pageData = self::get($this->soundCloudUrl);
        preg_match_all('/src=\"(https:\/\/a-v2\.sndcdn\.com\/assets\/[^.]+\.js)"/', $pageData, $assets);
        $assetUrl = end($assets[1]);
        $assetContent = self::get($assetUrl);
        preg_match_all('/[,|{]client_id:\"([^\"]+)\"/', $assetContent, $clientIds);
        $clientIdsNumber = array_rand($clientIds[1], 1);
        file_put_contents("./soundcloud_clientId", $clientIds[1][$clientIdsNumber]);
    }

    private function getClientId(): string|false
    {
        return file_get_contents("./soundcloud_clientId", true) ?? false;
    }

    private function checkClientId(): bool
    {
        $data = json_decode(self::get("{$this->soundCloudAuthUrl}oauth/session?" . self::arrayToUrlEncode(['client_id' => $this->getClientId()])));
        return isset($data->session);
    }


    private function getRemoteFileSize($url): int
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_NOBODY, TRUE);
        $data = curl_exec($ch);
        $fileSize = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        $httpResponseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return intval($fileSize);

    }

    private function exportUrl(string $url): array
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curl);
        curl_close($curl);
        $return_value = [];
        $urls = explode("\n", $data);
        foreach ($urls as $url) {
            if (filter_var($url, FILTER_VALIDATE_URL)) {
                $return_value[] = $url;
            }
        }
        return $return_value;
    }

    private function downloadAndPlay(array $urls, $trackName): void
    {
        $size = 0;
        foreach ($urls as $url) {
            $size += $this->getRemoteFileSize($url);
        }
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary");
        header("Content-Length: $size");
        header('Content-Disposition: attachment; filename="' . $trackName . '"');
        foreach ($urls as $url) {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            echo $resp = curl_exec($curl);
            curl_close($curl);
        }
    }

    private function internalError(): array
    {
        return [
            'status' => false,
            'status_det' => "INTERNAL_ERROR"
        ];
    }
}
