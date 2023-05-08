<?php
date_default_timezone_set("America/Lima");
setlocale(LC_ALL,"es_PE.UTF-8");
setlocale(LC_TIME,"es_ES.UTF-8");
setlocale(LC_TIME, 'spanish');
//iniciamos la sesión 
session_start();
require_once '../util/Session.php';
$tipoAcceso = Session::getAttribute("tipo");
$autentificado = Session::getAttribute("autentificado");
// Controla el inicio de sesión
if( !Session::existsAttribute("usuario")  || $autentificado != "SI") {
    header("location: ../../index.php");
    return;
}
else if($autentificado == "SI") {
    if((int)$tipoAcceso == 1) {
        header("location: ../../app/sistema.php");
        return;
    }
    else  if((int)$tipoAcceso == 2) {
        header("location: ../../app/principal.php");
        return;
    }
}

