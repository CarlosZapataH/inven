<?php

class GlobalHelper{
    CONST NOTRESPONSE = 0;
    CONST ACCEPTEDSUNAT = 1;
    CONST ACCEPTEDOBSSUNAT = 2;
    CONST REJECTEDSUNAT = 3;
    CONST EXCEPTIONPENDINGRESPONSE = 4;

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

    public static function statusTCI(){
        return [
            [
                'code' => self::NOTRESPONSE,
                'description' => 'Sin Respuesta'
            ],
            [
                'code' => self::ACCEPTEDSUNAT,
                'description' => 'Aceptado por Sunat'
            ],
            [
                'code' => self::ACCEPTEDOBSSUNAT,
                'description' => 'Aceptado con Observación por Sunat'
            ],
            [
                'code' => self::REJECTEDSUNAT,
                'description' => 'Rechazado por Sunat'
            ],
            [
                'code' => self::EXCEPTIONPENDINGRESPONSE,
                'description' => 'Excepción o pendiente de Respuesta'
            ]
        ];
    }
}