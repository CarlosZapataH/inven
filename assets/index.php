<?php
date_default_timezone_set("America/Lima");
setlocale(LC_ALL,"es_PE.UTF-8");
setlocale(LC_TIME,"es_ES.UTF-8");
setlocale(LC_TIME, 'spanish');
//iniciamos la sesión 
session_start();
require_once '../assets/util/Session.php';
$tipoAcceso = Session::getAttribute("tipo");
$autentificado = Session::getAttribute("autentificado");
// Controla el inicio de sesión
if( !Session::existsAttribute("usuario")  || $autentificado != "SI") {
    header("location: ../index.php");
    return;
}
else if($autentificado == "SI") {
    header("location: sistema.php");
    return;
}

