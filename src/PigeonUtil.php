<?php

namespace PigeonCloudSdk;

class PigeonUtil
{
    public static function getMasterAuth()
    {
        return self::makeAuth($_ENV['PIGEON_MASTER_ID'], $_ENV['PIGEON_MASTER_PASSWORD']);
    }
    public static function makeAuth(string $id, string $password): string
    {
        return base64_encode($id . ':' . $password);
    }

    public static function query(string $query, bool $debug = false): ?array
    {
        $auth = self::getMasterAuth();
        $response = PigeonHttpRequest::post('/raw-query', ['query' => $query], $auth, $debug);
        if ($response['result'] != 'success'){
            return null;
        }
        return $response['data']['query-result'];
    }

}