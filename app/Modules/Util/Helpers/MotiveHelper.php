<?php
class MotiveHelper{
    public static function getAll(){
        return self::values();
    }

    private static function values(){
        return [
            ['code' => '4', 'description' => 'Traslado entre establecimientos de la misma empresa'],
            ['code' => '6', 'description' => 'Devolución'],
            ['code' => '13', 'description' => 'Otros']
        ];
    }
}