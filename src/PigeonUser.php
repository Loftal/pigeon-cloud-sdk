<?php

namespace PigeonCloudSdk;

class PigeonUser
{
    public static function login($email, $password)
    {
        if (session_status() == PHP_SESSION_NONE) {
            throw new \Exception('Session not started.');
        }
        $auth = PigeonUtil::makeAuth($email, $password);
        $form_data = [
            'table' => 'admin',
            'condition' => [
                ['and_or' => 'and', 'field' => 'email', 'condition' => 'eq', 'value' => $email],
            ],
            'limit' => 1
        ];
        $response = PigeonHttpRequest::get('/record', $form_data, $auth);
        if (!is_array($response) || $response['result'] != 'success') {
            return false;
        }
        $_SESSION['pigeon_user'] = [
            'id' => $response['data'][0]['raw_data']['id'],
            'email' => $email,
            'name' => $response['data'][0]['view_data']['name'],
            'auth' => PigeonUtil::makeAuth($email, $password),
            'detail' => $response['data'][0]
        ];
        return true;
    }
}