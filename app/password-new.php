<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);
require_once '../assets/util/Session.php';
require_once '../controller/ControlSesion_temp.php';
$usert = Session::getAttribute("usuario_temp");
$version = "?".date("Y-m-d H:i:s");?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="IMC Confipetrol">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/img/ico/favicon.ico">
    <title>Inventario | Cambio Contraseña</title>
    <!--google font-->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/plugins/lobicard/css/lobicard.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/e632f1f723.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/simple-line-icons@2.5.5/dist/styles/simple-line-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css">
    <link href="../assets/css/preloader.min.css" rel="stylesheet">
    <link href="../assets/css/main.css<?=$version?>" rel="stylesheet">
    <link href="../assets/css/colors.css<?=$version?>" rel="stylesheet">

</head>
<body class="app appMobile header-fixed left-sidebar-fixed" onload="sga.funcion.deshabilitaRetroceso()">
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
            <a class="" href="password-new.php">
                <img src="../assets/img/login/logo_confi.png" width="125" height="35" alt="logo"/>
            </a>
        </div>
        <!--brand end-->

        <!--right side nav start-->
        <ul class="nav navbar-nav ml-auto">
            <li class="nav-item d-md-down-none show text-right" style="margin-right: 20px;">
                <?php
                $nameApellido = $usert['ape_pa'].", ".$usert['nombres'];
                if(!empty(trim($usert['ape_ma'])) && strlen(trim($usert['ape_ma']))>0){
                    $nameApellido = $usert['ape_pa']." ".$usert['ape_ma'].", ".$usert['nombres'];
                }?>
                <p class="mb-0">Bienvenid@ <span class="font-weight-bold"><?=$nameApellido;?></span></p>
                <a class="cursor-pointer text-danger nav-link no-margin no-padding animado" id="btn_logout">
                    Cerrar Sesión
                    <i class="ti-power-off position-right fz-14"></i>
                </a>
            </li>
        </ul>
        <!--right side nav end-->
    </header>
    <!--===========app body start===========-->
    <div class="app-body">
        <main class="main-content">
            <div class="container mt-10">
                <br>
                <div class="row">
                    <div class="col-12 text-center">
                        <div class="pl-title">
                            <div class="card" style="background: none;">
                                <h2 id="passwordTitle" class="fz-25">Cambio de Contraseña Requerida.</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mb-10 text-center">
                        <p>
                            Se requiere el cambio de su contraseña actual, tener en cuenta que la nueva contraseña debe cumplir los criterios que se describen a continuacion.
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6 offset-lg-3 col-md-6 offset-md-3 col-xs-12 mt-10 text-danger-600 text-center" id="id_error_pass"></div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <form class="form-password-new" id="guardarDatos_Changepass" role="form">
                            <input type="hidden" name="idtkus_chps" value="<?=$usert['id_us']?>">
                            <div class="form-group">
                                <label for="clave_actual" class="sr-only">Contraseña actual</label>
                                <input type="text" id="clave_actual" name="iclave_actual" class="form-control has-input" maxlength="35"
                                       placeholder="Contraseña actual" required="required" autocomplete="off">
                            </div>

                            <div class="form-group">
                                <label for="clave_new" class="sr-only">Contraseña nueva</label>
                                <input type="text" id="clave_new" name="iclave_new" class="form-control has-input" maxlength="35"
                                       placeholder="Contraseña nueva" required="required" autocomplete="off">
                            </div>

                            <div class="form-group">
                                <label for="clave_new_confirm" class="sr-only">Contraseña nueva</label>
                                <input type="text" id="clave_new_confirm" name="iclave_new_confirm" class="form-control has-input" maxlength="35"
                                       placeholder="Contraseña nueva confirmación" required="required" autocomplete="off">
                            </div>
                            <button class="btn btn-lg btn-default btn-block" type="submit"
                                    id="btnChangePass_Modificar" disabled>Grabar</button>
                            <div class="mt-4 text-center">
                                <span> retornar a </span>
                                <a href="login.php" class="text-primary">Inicio</a>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-6">
                        <div id="pswd_info" class="psw_info_text">
                            <ul>
                                <li id="letter">Al menos debería tener <b>una letra</b><i class="ml-7"></i></li>
                                <li id="capital">Al menos debería tener <b>una letra en mayúsculas</b><i class="ml-7"></i></li>
                                <li id="number">Al menos debería tener <b>un número</b><i class="ml-7"></i></li>
                                <li id="length">Debería tener <b>8 carácteres</b> como mínimo<i class="ml-7"></i></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <!--===========app body end===========-->
    <footer class="app-footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 text-center">
                    <?php
                    $f_ant = 2020;
                    $f_act = date("Y");
                    if((int)$f_ant == (int)$f_act){ $fecha = $f_act; }
                    else if((int)$f_act > (int)$f_ant){ $fecha = $f_ant." - ".$f_act; }
                    ?>
                    IMC &copy; <?=$fecha?> All Rights Reserved.
                </div>
                <div class="col-4 d-none">
                    <a href="#" class="float-right back-top cursor-pointer">
                        <i class=" ti-arrow-circle-up"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>



    <link href = "https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.10.4/themes/black-tie/jquery-ui.min.css" rel = "stylesheet">
    <script src = "https://cdnjs.cloudflare.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src = "https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
    <script src="https://code.jquery.com/jquery-latest.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="../assets/plugins/lobicard/js/lobicard.js"></script>
    <!--  Menu Accordion -->
    <script src="https://cdn.jsdelivr.net/npm/dcjqaccordion@2.7.1/js/jquery.cookie.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dcjqaccordion@2.7.1/js/jquery.dcjqaccordion.2.7.min.js"></script>
    <!--  Top Scroll -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-scrollTo/1.4.6/jquery.scrollTo.min.js"></script>
    <!--  sweetalert -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.25.0/sweetalert2.min.css" rel="stylesheet" type="text/css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.25.0/sweetalert2.min.js"></script>
    <!--  blockUI -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>


    <!--init scripts-->
    <script src="../assets/js/main.js<?=$version?>"></script>
    <script src="../assets/js/site.js<?=$version?>"></script>
    <script src="../assets/js/sistema.js<?=$version?>"></script>
    <script src="../assets/ajax/new_password.js<?=$version?>"></script>
</body>
</html>