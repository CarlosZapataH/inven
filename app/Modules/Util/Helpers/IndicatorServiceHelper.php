<?php
class IndicatorServiceHelper{
    public static function getAll(){
        return self::values();
    }

    private static function values(){
        return [
            ['code' => '01', 'description' => 'Indicador de transbordo programado'],
            ['code' => '02', 'description' => 'Indicador de traslado de vehiculo M1 y L'],
            ['code' => '03', 'description' => 'Indicador de retorno de vehículo envase vacio'],
            ['code' => '04', 'description' => 'Indicador de retorno de vehículo vacio'],
            ['code' => '05', 'description' => 'Indicador de traslado de total DAM o DS'],
            ['code' => '06', 'description' => 'Indicador de vehiculo de conductores transporte']
        ];
    }
}