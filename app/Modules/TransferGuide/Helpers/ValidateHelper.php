<?php

class ValidateHelper{

    public static function validateProperty($arreglo, $propiedades, $nombreObjeto = '') {
        $valorEncontrado = null;

        foreach ($propiedades as $propiedad) {
            $nombres = explode('.', $propiedad);
            $nombrePropiedad = end($nombres);
    
            $objeto = $arreglo;
            foreach ($nombres as $nombre) {
                if (!isset($objeto[$nombre])) {
                    return null;
                }
                $objeto = $objeto[$nombre];
            }

            $valorEncontrado = $objeto;
    
            if (is_array($objeto)) {
                $subPropiedades = array_keys($objeto);
                if (!validateProperty($objeto, $subPropiedades, $nombreObjeto . '.' . $nombrePropiedad)) {
                    return null;
                }
            }
        }
    
        return $valorEncontrado;
    }
}