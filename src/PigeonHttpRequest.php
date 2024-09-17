<?php

namespace PigeonCloudSdk;

class PigeonHttpRequest
{
    public static function get(string $api, array $form_data, string $auth, bool $debug = false): array|string
    {
        $curl = curl_init();
        $url = $_ENV['PIGEON_API_URL'] . $api . '?' . http_build_query($form_data);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ["X-Pigeon-Authorization: " . $auth]);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $response_raw = curl_exec($curl);
        $curlInfo = curl_getinfo($curl);
        curl_close($curl);
        if (str_contains($curlInfo['content_type'], 'application/json')) {
            $response = json_decode($response_raw, true);
        } else {
            $response = $response_raw;
        }
        if ($debug) {
            $response_json_unescaped = json_encode($response, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
            echo <<<EOT
                <pre>
                【URL】
                {$url}
                
                【JSON Response】
                {$response_json_unescaped}
                </pre>
                EOT;
            exit(0);
        }
        if (!$response) {
            throw new \Exception('Pigeon Response Error.');
        }
        return $response;
    }

    public static function post(string $api, array $form_data, string $auth, bool $debug = false): array
    {
        $curl = curl_init();
        $url = $_ENV['PIGEON_API_URL'] . $api;
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ["X-Pigeon-Authorization: " . $auth]);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $form_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        $response_raw = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response_raw, true);
        if ($debug) {
            $form_data_json = json_encode($form_data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
            $response_json_unescaped = json_encode($response, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
            echo <<<EOT
                <pre>
                【URL】
                {$url}
                
                【POST】
                {$form_data_json}
                
                【JSON Response】
                {$response_json_unescaped}
                </pre>
                EOT;
            exit(0);
        }
        if (!$response) {
            throw new \Exception('Pigeon Response Error.');
        }
        return $response;
    }
}