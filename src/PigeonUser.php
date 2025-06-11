<?php

namespace PigeonCloudSdk;

class PigeonUser
{
    public static function login($id, $password)
    {
        if (session_status() == PHP_SESSION_NONE) {
            throw new \Exception('Session not started.');
        }
        $auth = PigeonUtil::makeAuth($id, $password);
        $form_data = [
            'table' => 'admin',
            'condition' => [
                ['and_or' => 'and', 'field' => 'email', 'condition' => 'eq', 'value' => $id],
            ],
            'limit' => 1
        ];
        $response = PigeonHttpRequest::get('/record', $form_data, $auth);
        if (!is_array($response) || $response['result'] != 'success') {
            return false;
        }
        $_SESSION['pigeon_user'] = [
            'id' => $id,
            'name' => $response['data'][0]['view_data']['name'],
            'auth' => PigeonUtil::makeAuth($id, $password)
        ];
        return true;
    }
}