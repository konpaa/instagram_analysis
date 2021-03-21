<?php

namespace instagramPhp\Storage;

const STORAGE_DIR = __DIR__ . '/../data';

function saveUserDate(string $username, \stdClass $data): string
{
    if (!is_dir(STORAGE_DIR)) {
        mkdir(STORAGE_DIR, 0777, true);
    }

    $realFullFilePath = getFullFilePath($username);

    $encodedData = json_encode(
        $data,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    file_put_contents($realFullFilePath, $encodedData);

    return $realFullFilePath;
}

function getUserData(string $username): ?\stdClass
{
    $fullFilePath = getFullFilePath($username);
    if (!is_file($fullFilePath)) {
        return null;
    }

    $encodedDara = file_get_contents($fullFilePath);

    return json_decode($encodedDara);
}

function getFullFilePath(string $username): string
{
    $fullFilePath = STORAGE_DIR . "/$username.json";
    return realpath($fullFilePath);
}