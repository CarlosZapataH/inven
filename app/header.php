<?php
error_reporting(E_ALL & ~E_NOTICE);
session_start();
require_once '../assets/util/Session.php';
require_once '../controller/ControlSesion.php';
require_once '../model/FuncionesModel.php';
require_once '../model/UsuarioModel.php';
$user = Session::getAttribute("usuario");
$obj_fn = new FuncionesModel();
$version = "?".date("Y-m-d H:i:s");?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="IMC Confipetrol">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/img/ico/favicon.ico">
    <title>Inventario | Principal</title>
    <!--google font-->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/plugins/lobicard/css/lobicard.css<?=$version?>" rel="stylesheet">
    <script src="https://kit.fontawesome.com/e632f1f723.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/simple-line-icons@2.5.5/dist/styles/simple-line-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css">
    <link href="../assets/css/preloader.min.css<?=$version?>" rel="stylesheet">
    <link href="../assets/css/main.css<?=$version?>" rel="stylesheet">
    <link href="../assets/css/colors.css<?=$version?>" rel="stylesheet">
</head>
<style>
    .selected .field-checkbox {
        border-color: lightseagreen;
        background-color: lightseagreen;
    }

    .selected .field-checkbox::before {
        content: '';
        width: 6px;
        height: 12px;
        position: absolute;
        left: 6px;
        top: 2px;
        border-right: 2px solid white;
        border-bottom: 2px solid white;
        transform: rotate(45deg);
    }

    .field-checkbox {
        position: relative;
        width: 20px;
        height: 20px;
        margin-top: 7px;
        border: 1px solid darkgray;
        border-radius: 4px;
        cursor: pointer;
    }
</style>
<body class="app header-fixed left-sidebar-fixed" onload="sga.funcion.deshabilitaRetroceso()" style="overflow-x: hidden;">
<div class="modal" id="ModalProgressBar_Load" tabindex="-1" role="dialog" aria-hidden="true"></div>
<div class="modal" id="ModalAction_ContainerForm" data-backdrop="static" data-keyboard="false" role="dialog" style="display:none;"></div>
<input type="hidden" id="idustk" value="<?=$obj_fn->encrypt_decrypt('encrypt',$user['id_us'])?>">
<!--===========PreLoader===========-->
<div class="preloader">
    <div class="loader">
        <div class="loader__figure"></div>
        <p class="loader__label">Confipetrol</p>
    </div>
</div>
<!--===========header start===========-->
<header class="app-header navbar">
    <!--brand start-->
    <div class="navbar-brand">
        <a class="" href="sistema.php">
            <img src="../assets/img/login/logo_confi.png" width="125" height="40" alt="logo"/>
        </a>
    </div>
    <!--brand end-->
    <ul class="nav navbar-nav mr-auto">
        <li class="nav-item d-lg-none">
            <button class="navbar-toggler mobile-leftside-toggler" type="button"><i class="ti-align-right"></i></button>
        </li>
        <li class="nav-item d-md-down-none">
            <a class="nav-link navbar-toggler left-sidebar-toggler cursor-pointer"><i class=" ti-align-right"></i></a>
        </li>
    </ul>
    <!--right side nav start-->
    <ul class="nav navbar-nav ml-auto">
        <li class="nav-item dropdown dropdown-slide d-md-down-none">
            <a class="nav-link cursor-pointer"  data-toggle="dropdown" role="button">
                <i class="icon-info text-primary-400 font-weight-bold"></i>
                <div class="notify">
                    <span class="heartbit"></span>
                    <span class="point"></span>
                </div>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <div class="dropdown-header pb-3">
                    <strong>Descargables</strong>
                </div>

                <a href="../assets/manual/Manual Plataforma - Usuario.pdf" class="dropdown-item cursor-pointer" download="manualUsuario.pdf"
                   title="Manual usuario">
                    <i class="fa fa-file-pdf-o text-danger-800 font-weight-bold"></i>
                    Manual de Usuario
                </a>

            </div>
        </li>

        <li class="nav-item dropdown dropdown-slide d-md-down-none">
            <a class="nav-link nav-pill" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                <i class=" ti-view-grid"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right dropdown-menu-ql-gird">

                <div class="dropdown-header pb-3">
                    <strong>Accesos rápidos</strong>
                </div>

                <div class="quick-links-grid">
                    <a  class="ql-grid-item cursor-pointer" href="inventario.php">
                        <i class="icon-layers text-info cursor-pointer"></i>
                        <span class="ql-grid-title">Inventario</span>
                    </a>
                    <a class="ql-grid-item cursor-pointer" href="transito.php">
                        <i class="ti-truck text-warning"></i>
                        <span class="ql-grid-title">Transito</span>
                    </a>
                    <a class="ql-grid-item cursor-pointer" href="calibra.php">
                        <i class="ti-ruler-pencil text-purple-600"></i>
                        <span class="ql-grid-title">Calibración</span>
                    </a>
                    <a class="ql-grid-item cursor-pointer" href="reporte-depreciacion.php">
                        <i class="ti-money text-teal-600"></i>
                        <span class="ql-grid-title">Depreciación Activo</span>
                    </a>
                </div>

            </div>
        </li>

        <li class="nav-item dropdown dropdown-slide d-md-down-none show">
            <a class="nav-link">
                Bienvenido : <span class="font-weight-bold"><?=$user['nombres']?></span>
            </a>
        </li>

        <li class="nav-item dropdown dropdown-slide">
            <a class="nav-link nav-pill user-avatar" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                <img src="../assets/img/avatar/<?=$user['avatar']?>.png" alt="usuario">
            </a>
            <div class="dropdown-menu dropdown-menu-right dropdown-menu-accout">
                <div class="dropdown-header pb-3">
                    <div class="media d-user">
                        <img class="align-self-center mr-3" src="../assets/img/avatar/<?=$user['avatar']?>.png" alt="usuario">
                        <div class="media-body">
                            <h5 class="mt-0 mb-0"><?=$user['nombres'].", ".$user['ape_pa']?></h5>
                            <span><?=$user['email']?></span>
                        </div>
                    </div>
                </div>
                <a class="dropdown-item" href="ChangePassword.php">
                    <i class="ti-lock cursor-pointer"></i>
                    Cambiar contraseña
                </a>
                <a class="dropdown-item cursor-pointer" id="btn_logout">
                    <i class="ti-power-off"></i>
                    Cerrar sesión
                </a>
            </div>
        </li>

        <!--right side toggler-->
        <li class="nav-item d-lg-none">

        </li>
        <li class="nav-item d-md-down-none">

        </li>
    </ul>
    <!--right side nav end-->
</header>
<!--===========app body start===========-->
<div class="app-body">
    <div class="left-sidebar">
        <nav class="sidebar-menu">
            <ul id="nav-accordion">
                <?php include('menu_opciones.php');?>
            </ul>
        </nav>
    </div>
    <main class="main-content">
