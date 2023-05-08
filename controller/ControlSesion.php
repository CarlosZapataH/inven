<?php
date_default_timezone_set("America/Lima");
setlocale(LC_ALL,"es_PE.UTF-8");
setlocale(LC_TIME,"es_ES.UTF-8");
setlocale(LC_TIME, 'spanish');
//iniciamos la sesión 
session_start();
require_once '../assets/util/Session.php';
$autentificado = Session::getAttribute("autentificado");
$ultimoAcceso = Session::getAttribute("ultimoAcceso");
// Controla el inicio de sesión
if( !Session::existsAttribute("usuario") ) {
    header("location: ../index.php");
    return;
}
if($autentificado!=="SI") {
    //si no está logueado lo envío a la página de autentificación 
    header("location: ../index.php");
    return;
}