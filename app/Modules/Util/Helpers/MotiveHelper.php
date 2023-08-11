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
                    "value" => "Tras. de muestras de aceite para análisis", "description" => "Tras. de muestras de aceite para análisis"
                ],
                [
                    "value" => "Reparación", "description" => "Reparación"
                ],
                [
                    "value" => "Tras. de instrumentos de medición para calibración", "description" => "Tras. de instrumentos de medición para calibración"
                ],
                [
                    "value" => "Tras. de materiales EPPS", "description" => "Tras. de materiales EPPS"
                ],
                [
                    "value" => "Tras. para uso en servicio", "description" => "Tras. para uso en servicio"
                ]
            ]]
        ];
    }
}