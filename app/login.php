<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);
require_once '../assets/util/Session.php';
include("../assets/plugins/captcha/simple-php-captcha.php");
Session::setAttribute("captcha",simple_php_captcha());
$error = Session::getAttribute2("error");
$acceso= Session::getAttribute2("acceso");
$version = "?".date("Y-m-d H:i:s");?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario | Iniciar sesión</title>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.2/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/login/iofrm-style.min.css<?=$version?>">
    <link rel="stylesheet" type="text/css" href="../assets/css/login/iofrm-theme3.min.css<?=$version?>">
    <link rel="stylesheet" type="text/css" href="../assets/css/login/disable-arrow-input-number.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/login/cookies.css<?=$version?>">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/img/ico/favicon.ico">
    <script src="../assets/js/site.js<?=$version?>"></script>
</head>
<body>
    <div class="form-body">
        <div class="website-logo format-logo">
            <a href="../index.php">
                <div class="logo">
                    <img class="logo-size" src="../assets/img/login/logo_confi.png" alt="">
                </div>
            </a>
        </div>
        <div class="row">
            <div class="img-holder">
                <div class="bg"></div>
                <div class="info-holder"></div>
            </div>
            <div class="form-holder">
                <div class="form-content">
                    <div class="form-items">
                        <h3 class="text_hiden text-center">CONTROL DE INVENTARIO</h3>
                        <p class="text_hiden text-center">Iniciar sesión</p>
                        <div id="msj-error">
                            <?php if($error!==NULL){ ?>
                                <div class="alert alert-warning alert-dismissible fade show with-icon" role="alert">
                                    <?=$error?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php }else { echo "";} ?>
                        </div>
                        <form action="../controller/LoginController.php" id="login-form" method="POST">
                            <input type="hidden" name="action" value="valida_logueoUsuario">
                            <div class="row">
                                <div class="col-md mb-14">
                                    <input type="number" class="form-control border-input text-center" placeholder="nro. documento" tabindex="1"
                                           id="txtNroDoc" name="txtNroDoc" required="required" autocomplete="off" maxlength="12"
                                           oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                           step="1" min="1" onkeydown="return event.keyCode !== 69">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md mb-14">
                                    <input type="password" class="form-control border-input text-center" placeholder="contraseña" tabindex="2"
                                           id="txtPassword" name="txtPassword" required="required" autocomplete="off" maxlength="30">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-7 col-md-7 col-sm-7 col-xs-7 mb-10 col-part-60">
                                    <input id="codecaptcha" name="codecaptcha" type="number" placeholder="Código" tabindex="3"
                                           class="form-control text-center border-input" required="required" autocomplete="off"
                                           oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                           step="1" min="1" maxlength="5" onkeydown="return event.keyCode !== 69">
                                </div>
                                <div class="col-lg-5 col-md-5 col-sm-5 col-xs-5 mb-10 text-right col-part-40">
                                    <div id="spiner_CaptchaContend"></div>
                                    <?php $captchaIMG = Session::getAttribute2("captcha");?>
                                    <img src="<?=$captchaIMG['image_src']?>" alt="CAPTCHA code" id="captcha" style="height:45px; margin-top:5px;display:inline">
                                </div>
                            </div>
                            <div class="form-button mt-20 mb-20">
                                <button type="submit" class="ibtn ibtn_war btn-hover-transform btn-block"
                                        tabindex="4" style="height:45px;font-size:18px;">
                                    Ingresar
                                </button>
                            </div>
                        </form>

                        <div class="footer text-center">
                            <?php
                            $f_ant = 2020;
                            $f_act = date("Y");
                            if((int)$f_ant == (int)$f_act){ $fecha = $f_act; }
                            else if((int)$f_act > (int)$f_ant){ $fecha = $f_ant." - ".$f_act; }
                            ?>
                            <p>IMC &copy; <?=$fecha?> All Rights Reserved.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div id="cajacookies" style="display: none">
        <div class="container">
            <div id="popup-text">
                <h2>
                    Utilizamos  cookies en el sitio para mejorar su experiencia de usuario.
                </h2>
                <h2>
                    Al dar click en el botón Aceptar, usted acepta las políticas de uso de cookies.
                    <a href="https://confipetrol.com/es/politica-de-cookies" target="_blank" style="color:#ffffff"><ins>Mas información</ins></a>
                </h2>
            </div>
            <div id="popup-buttons">
                <a onclick="aceptarCookies()" style="margin-right: 10px;cursor:pointer;color:#ffffff"> Acepto</a>
                <button onclick="deniegaCookies()" style="cursor:pointer;"> No, gracias</button>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="../assets/js/login.js<?=$version?>"></script>
    <script src="../assets/js/cookies.js<?=$version?>"></script>
</body>
</html>