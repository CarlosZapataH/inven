<?php
include ('header.php');
require_once '../assets/util/Session.php';
require_once '../controller/ControlSesion.php';?>

    <div class="page-title">
        <h4 class="mb-0 text-info">
            Mi Contraseña
        </h4>
        <ol class="breadcrumb mb-0 pl-0 pt-1 pb-0">
            <li class="breadcrumb-item text-muted">Cambiar Contraseña.</li>
        </ol>
    </div>
    <div class="card card-body">
        <div class="container-fluid">
            <p class="text-muted m-b-30 font-13 text-center">
                Ingrese su actual contraseña y luego una nueva para poder proceder a realizar el cambio.
            </p>
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 offset-lg-3 offset-md-3" id="mensaje_action_ps"></div>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 offset-lg-2 offset-md-2 mt-20">
                    <form id="form_Change_Password">
                        <input type="hidden" name="us_pss" id="us_pss" value="<?=$user['id_us']?>">
                        <div class="form-group row">
                            <div class="col-12">
                                <input type="password" class="form-control has-input" id="pass_actual" name="pass_actual"
                                       placeholder="contraseña actual" autocomplete="off" required>
                                <small class="text-muted">Ingrese su contraseña actual</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12">
                                <input type="text" class="form-control disableAction_pass has-input" id="clave_new" name="clave_new"
                                       placeholder="contraseña nueva" autocomplete="off" required disabled>
                                <small class="text-muted">Ingrese su contraseña nueva</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12">
                                <input type="text" class="form-control disableAction_pass has-input" id="clave_new_confirm" name="clave_new_confirm"
                                       placeholder="repita contraseña nueva" autocomplete="off" required disabled>
                                <small class="text-muted">Confirme la nueva contraseña</small>
                            </div>
                        </div>
                        <hr class="mt-0 mb-10">
                        <div class="form-group row">
                            <div class="col-12 text-center">
                                <div class="button-group">
                                    <button type="submit" class="btn btn-block waves-effect waves-light btn-default" id="btnChangePass_Modificar" disabled>
                                        Actualizar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div id="pswd_info" class="psw_info_text" style="margin-top: 0 !important;">
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
    </div>
    <br>

<?php
include ('footer.php');
?>
<script src="../assets/ajax/change_pass.js<?=$version?>"></script>

