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

    public static function getDaysBetweenDates($firstDate, $secondDate){
        $timestampSecondDate = strtotime($secondDate);
        $timestampFirstDate = strtotime($firstDate);

        $diffSeconds = $timestampFirstDate - $timestampSecondDate;
        $diffDays = floor($diffSeconds / (60 * 60 * 24));

        return $diffDays;
    }

    public static function getDiffHours($startDate, $endDate){

        // Crea objetos DateTime con las fechas y horas
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);

        // Calcula la diferencia entre las dos fechas
        $between = $start->diff($end);

        // Obtiene los días, las horas y los minutos transcurridos
        $days = $between->days;
        $hours = $between->h;

        // Calcula el total de horas considerando los días completos
        $totalHours = ($days * 24) + $hours;

        return $totalHours;
    }

    public static function firstDateThanSecond($first, $second){
        $firstDate = new DateTime($first);
        $secondDate = new DateTime($second);
        return ($firstDate >= $secondDate);
    }
}