<?php

class GlobalHelper{
    public static function getPostData(){
        $postData = file_get_contents('php://input');
        return json_decode($postData, true);
    }

    public static function getUrlData(){
        $data = array();
        if (!empty($_GET)) {
            foreach ($_GET as $key => $value) {
                $data[$key] = $value;
            }
        }
        return $data;
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