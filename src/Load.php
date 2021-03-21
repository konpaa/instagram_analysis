<?php

namespace instagramPhp\Load;

use function instagramPhp\Storage\saveUserDate;

function loadRemoteUserData(string $username): string
{
    $httpClient = new \GuzzleHttp\Client();
    $fullUrl = "https://www.instagram.com/$username/?__a=1";
    $response = $httpClient->get($fullUrl);
    $contents = $response->getBody()->getContents();
    $data = json_decode($contents);
    if (!is_object($data)) {
        throw new \Exception("User data is not object" . $contents);
    }
    return saveUserDate($username, $data);
}
