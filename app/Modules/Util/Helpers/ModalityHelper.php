<?php
class ModalityHelper{
    public static function getAll(){
        return self::values();
    }

    private static function values(){
        return [
            ['code' => '1', 'description' => 'Transporte público'],
            ['code' => '2', 'description' => 'Transporte privado']
        ];
    }
}