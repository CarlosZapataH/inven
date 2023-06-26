<?php
class MotiveHelper{
    public static function getAll(){
        return self::values();
    }

    private static function values(){
        return [
            ['code' => '04', 'description' => 'Traslado entre establecimientos de la misma empresa', 'submotives' => null],
            ['code' => '06', 'description' => 'Devolución', 'submotives' => null],
            ['code' => '13', 'description' => 'Otros', 'submotives' => [
                [
                    "value" => "Traslado de muestras de aceite para análisis", "description" => "Traslado de muestras de aceite para análisis"
                ],
                [
                    "value" => "Traslado de equipos para su mantenimiento/reparación", "description" => "Traslado de equipos para su mantenimiento/reparación"
                ],
                [
                    "value" => "Traslado de instrumentos de medición para calibración", "description" => "Traslado de instrumentos de medición para calibración"
                ],
                [
                    "value" => "Traslado de materiales EPPS, instrumentos al trabajador", "description" => "Traslado de materiales EPPS, instrumentos al trabajador"
                ],
                [
                    "value" => "Traslado para uso en servicio", "description" => "Traslado para uso en servicio"
                ]
            ]]
        ];
    }
}