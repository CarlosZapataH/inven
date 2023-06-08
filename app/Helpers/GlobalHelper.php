<?php

class GlobalHelper{
    public static function getPostData(){
        $postData = file_get_contents('php://input');
        return json_decode($postData, true);
    }

    public static function getGlobalResponse(){
        return [
            'data' => null,
            'success' => false,
            'message' => 'Error',
            'errors' => null,
            'code' => 400
        ];
    }
}