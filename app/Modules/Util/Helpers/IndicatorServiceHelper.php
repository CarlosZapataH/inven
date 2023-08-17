<?php
class IndicatorServiceHelper{
    public static function getAll(){
        return self::values();
    }

    private static function values(){
        return [
            ['code' => '01', 'description' => 'Indicador de transbordo programado', 'required_mtc' => false],
            ['code' => '02', 'description' => 'Indicador de traslado de vehiculo M1 y L', 'required_mtc' => true],
            ['code' => '03', 'description' => 'Indicador de retorno de vehículo envase vacio', 'required_mtc' => false],
            ['code' => '04', 'description' => 'Indicador de retorno de vehículo vacio', 'required_mtc' => false],
            ['code' => '05', 'description' => 'Indicador de traslado de total DAM o DS', 'required_mtc' => false],
            ['code' => '06', 'description' => 'Indicador de vehiculo de conductores transporte', 'required_mtc' => false]
        ];
    }
}