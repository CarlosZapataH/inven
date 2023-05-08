<?php
set_time_limit(0);
ignore_user_abort(true);
require_once '../model/UsuarioModel.php';

$obj_us = new UsuarioModel();
$lstUsuarios = $obj_us->listar_Usuarios_Visualizacion_All();

if(is_array($lstUsuarios)) {

    date_default_timezone_set("America/Lima");
    setlocale(LC_TIME, 'es_PE.UTF-8');

    $fechaActual = date("Y-m-d");
    foreach ($lstUsuarios as $usuario) {
        $dif_day = (strtotime($fechaActual) - strtotime($usuario['fechareg_us'])) / 86400;
        $dif_day = abs($dif_day);
        $dif_day = floor($dif_day);
        if((int)$dif_day >= 90){
            $datesUSERS[0] = $usuario['id_us'];
            $datesUSERS[1] = 0;
            $datesUSERS[2] = date("Y-m-d");
            $obj_us->renovar_password_xUsuario($datesUSERS);
        }
    }
}

