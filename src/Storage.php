<?php

namespace instagramPhp\Storage;

const STORAGE_DIR = __DIR__ . '/../data';

function saveUserDate(string $username, \stdClass $data): string
{
    if (!is_dir(STORAGE_DIR)) {
        mkdir(STORAGE_DIR, 0777, true);
    }
    $fullFilePath = STORAGE_DIR . "/$username.json";
    $encodedData = json_encode(
        $data,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    file_put_contents($fullFilePath, $encodedData);

    return $fullFilePath;
}