<?php

namespace app\controllers;

use app\models\Users;
use yii\web\Controller;

class ApiController extends Controller
{
    public function actionUpdate_users()
    {
        if (!isset($_GET['token']) || $_GET['token'] !== 'abrakadabra') return 'invalid token';

        $username = "api";
        $password = "password-api";
        $remote_url = 'http://site-server.loc/api/users';
        $opts = array(
            'http'=>array(
                'method'=>"GET",
                'header' => "Authorization: Basic " . base64_encode("$username:$password")
            )
        );

        $context = stream_context_create($opts);
        $data = file_get_contents($remote_url, false, $context);
        $data = json_decode($data, true);

        $pageCount = $data['_meta']["pageCount"];
        $user_count = Users::find()->count();

        for ( $i = 1; $i < $pageCount+1; $i++){

            $url = 'http://site-server.loc/api/users/index?page=' . $i;
            $datapage = json_decode(file_get_contents($url, false, $context), true);
            Users::UpdateUsers($datapage, $i, $user_count);
        }

        return 'updated';
    }

}
