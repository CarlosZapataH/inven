<?php
/**
 * Created by PhpStorm.
 * User: Administrador
 * Date: 18/07/2017
 * Time: 09:34 AM
 */
    session_start();
    error_reporting(E_ALL & ~E_NOTICE);
    require_once 'Session.php';

    include("simple-php-captcha.php");

    Session::setAttribute("captcha",simple_php_captcha());
    $captcha = Session::getAttribute("captcha");
    echo json_encode($captcha);
