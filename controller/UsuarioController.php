<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);
require_once '../model/UsuarioModel.php';
require_once '../model/PersonaModel.php';
require_once '../model/PerfilModel.php';
require_once '../model/GerenciaModel.php';
require_once '../model/ServicioModel.php';
require_once '../model/FuncionesModel.php';
require_once '../model/AlmacenModel.php';
require_once '../assets/plugins/phpspreadsheet-1.17.1.0/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../assets/plugins/PHPMailer-5.2.25/src/Exception.php';
require '../assets/plugins/PHPMailer-5.2.25/src/PHPMailer.php';
require '../assets/plugins/PHPMailer-5.2.25/src/SMTP.php';

$action = $_REQUEST["action"];
$controller = new UsuarioController();
call_user_func(array($controller,$action));

class UsuarioController {

    public function lst_Usuarios_All_JSON(){
        try {
            $obj_us = new UsuarioModel();
            $lst_Usuarios = $obj_us->lst_Usuarios_All();

            $datos = array();
            if (is_array($lst_Usuarios)) {

                $obj_per = new PersonaModel();
                foreach ($lst_Usuarios as $usuario) {
                    $dtllePersona = $obj_per->detalle_Persona_xID($usuario['id_per']);


                    $estado = '<span class="label label-block text-danger">Suspendido</span>';
                    if ((int)$usuario['condicion_us'] == 1) {
                        $estado = '<span class="label label-block ">Activo</span>';
                    }

                    $HombreApollo = $dtllePersona['ape_pa_per'].", ".$dtllePersona['nombres_per'];
                    if(!empty(trim($persona['ape_ma_per']))){
                        $HombreApollo = $dtllePersona['ape_pa_per']." ".$dtllePersona['ape_ma_per'].", ".$dtllePersona['nombres_per'];
                    }

                    $obj_perfil = new PerfilModel();
                    $dtllePerfil = $obj_perfil->detalle_Perfil_xID($usuario['id_perfil']);
                    $perfil = "";
                    if(!is_null($dtllePerfil)){
                        $perfil = $dtllePerfil['titulo_perfil'];
                    }

                    $row = array(
                        0 => "",
                        1 => $usuario['id_us'],
                        2 => $dtllePersona['area_servicio_per'],
                        3 => $HombreApollo,
                        4 => $dtllePersona['ndoc_per'],
                        5 => $perfil,
                        6 => $dtllePersona['email_per'],
                        7 => $estado
                    );

                    array_push($datos, $row);
                }
            }

            $tabla = array('data' => $datos);
            echo json_encode($tabla);
            unset($datos);

        } catch (PDOException $e) {
            //Session::setAttribute("error", $e->getMessage());
            throw $e;
        }
    }

    public function loadCampos_NuevaUsuario_Ajax(){
        try {
            $obj_pf = new PerfilModel();
            $lstPerfil = $obj_pf->lst_Perfil_Activos_All();
            $obj_ge = new GerenciaModel();
            $lstGerencias = $obj_ge->lst_Gerencia_Activas();?>
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="page-title">
                            <h4 class="display-6 font-weight-bold text-info">
                                Nuevo Usuario
                            </h4>
                            <ol class="breadcrumb mb-0 pl-0 pt-1 pb-0">
                                <li class="breadcrumb-item text-muted">Complete los campos descritos para agregar un nuevo usuario.</li>
                            </ol>
                        </div>
                    </div>
                </div>
                <form id="formNewUsuario" role="form" method="post">
                    <div class="card shadow">
                        <div class="card-body">
                            <p class="mb-20">
                                Todos los campos descritos con <code>(*)</code>, son campos obligatorios.
                            </p>
                            <div class="row">
                                <div class="col-12" id="mensajes_actions_add"></div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-md-4 col-sm-12 text-right control-label d-none d-sm-block col-form-label">
                                    Servicio
                                    <span class="text-danger">*</span>
                                </label>
                                <label class="col-12 text-left control-label d-block d-sm-none col-form-label pb-0">
                                    Servicio
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <select id="servicioSelect" name="servicio" class="form-control selectSearchClass" data-placeholder="Seleccione..." required>
                                        <option></option>
                                        <?php
                                        if (is_array($lstGerencias)) {
                                            $obj_serv = new ServicioModel();
                                            foreach ($lstGerencias as $gerencia) {
                                                $lstServicios = $obj_serv->lst_Servicio_Activos_xGerencia_All($gerencia['id_ge']);
                                                if (is_array($lstServicios)) {?>
                                                    <optgroup label="<?= $gerencia['des_ge'] ?>">
                                                        <?php foreach ($lstServicios as $servicio) { ?>
                                                            <option value="<?= $servicio['id_serv'] ?>">
                                                                <?= $servicio['des_serv'] ?>
                                                            </option>
                                                        <?php } ?>
                                                    </optgroup>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-md-4 col-sm-12 text-right control-label d-none d-sm-block col-form-label">
                                    Almacén
                                    <span class="text-danger">*</span>
                                </label>
                                <label class="col-12 text-left control-label d-block d-sm-none col-form-label pb-0">
                                    Almacén
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <select id="almacenSelect" name="almacen" required data-style="form-control"
                                            multiple class="selectpicker selectMultiple"
                                            data-live-search="true" data-live-search-placeholder="Escriba para buscar.."
                                            data-none-selected-text="Seleccione..."  data-width="100%" data-size="auto"
                                            data-selected-text-format="count>1"></select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-4 col-md-4 col-sm-12 text-right control-label d-none d-sm-block col-form-label">
                                    Perfil
                                    <span class="text-danger">*</span>
                                </label>
                                <label class="col-12 text-left control-label d-block d-sm-none col-form-label pb-0">
                                    Perfil
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                    <select id="perfil" name="perfil" class="form-control selectClass" data-placeholder="Seleccione...">
                                        <option></option>
                                        <?php
                                        if(is_array($lstPerfil)){
                                            $i=1;
                                            foreach ($lstPerfil as $perfil) {?>
                                                <option value="<?= $perfil['id_perfil'] ?>">
                                                    <?= $i . " - " . $perfil['titulo_perfil'] ?>
                                                </option>
                                                <?php
                                                $i++;
                                            }
                                        } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="ape_pa" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Área/Servicio
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control input-md text-left"
                                           name="area" maxlength="45" required placeholder="área o servicio">
                                    <small class="form-text text-muted">Máximo 150 carácteres</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="ape_pa" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Apellido Paterno
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-md text-left"
                                           name="ape_pa" maxlength="45" required placeholder="apellido paterno">
                                    <small class="form-text text-muted">Máximo 45 carácteres</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="ape_ma" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Apellido Materno
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-md text-left"
                                           name="ape_ma" maxlength="45" required placeholder="apellido materno">
                                    <small class="form-text text-muted">Máximo 45 carácteres</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="nombres" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Nombres
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-md text-left"
                                           name="nombres" maxlength="45" required placeholder="nombres">
                                    <small class="form-text text-muted">Máximo 45 carácteres</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="ndoc" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Número de DNI/CE.
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-3">
                                    <input type="number" class="form-control" placeholder="nro. documento" name="ndoc" id="ndoc"  required autocomplete="off" maxlength="12"
                                           oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                           step="1" min="1" onkeydown="return event.keyCode !== 69">
                                    <small class="form-text text-muted">Máximo 12 digitos</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="puesto" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Cargo/Puesto
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-md text-left"
                                           name="puesto" maxlength="50" required placeholder="puesto de trabajo">
                                    <small class="form-text text-muted">Máximo 50 carácteres</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Email Personal/Corporativo
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-6">
                                    <input type="email" class="form-control input-md text-left"
                                           name="email" maxlength="45" required placeholder="email">
                                    <small class="form-text text-muted">Máximo 45 carácteres</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="avatar" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Avatar
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-2">
                                    <select name="avatar" class="form-control selectClass" data-placeholder="Seleccione...">
                                        <option></option>
                                        <option value="1" selected>Hombre</option>
                                        <option value="2">Mujer</option>
                                    </select>
                                </div>
                            </div>

                            <hr>
                            <div class="row form-group">
                                <div class="col-12 text-center mb-10">
                                    <h4 class="text-bold">Credenciales Usuario:<br></h4>
                                    <span class="text-muted">
                                            Los datos consignados en los campos siguientes son para el acceso al sistema.
                                        </span>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="col-lg-4 col-md-4 col-sm-12 text-right control-label d-none d-sm-block col-form-label">
                                    Usuario
                                    <span class="text-danger">*</span>
                                </label>
                                <label class="col-12 text-left control-label d-block d-sm-none col-form-label pb-0">
                                    Usuario
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="col-lg-3 col-md-3 col-sm-5 col-xs-12 mb-10">
                                    <input class="form-control" type="text" autocomplete="off" id="txt_usuario_us"
                                           name="txt_usuario_us" placeholder="nombre de usuario" disabled>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="col-lg-4 col-md-4 col-sm-12 text-right control-label d-none d-sm-block col-form-label">
                                    Contraseña
                                    <span class="text-danger">*</span>
                                </label>
                                <label class="col-12 text-left control-label d-block d-sm-none col-form-label pb-0">
                                    Contraseña
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="col-lg-3 col-md-3 col-sm-5 col-xs-12 mb-10">
                                    <input class="form-control" type="text" autocomplete="off" name="txt_password_us"
                                           placeholder="contraseña" required maxlength="35" value="<?="Cf*".date("dmY")?>">
                                </div>
                            </div>

                            <hr>
                            <div class="row form-group mb-10">
                                <div class="col-12 text-center">
                                    <h4 class="text-bold">Opciones de inicio de sesión:<br></h4>
                                    <span class="text-muted">
                                            Marque los accesos con el que contara el usuario al ingresar al sistema.
                                        </span>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 offset-lg-3 offset-md-3 text-center">
                                    <div class="form-group pt-15">
                                        <div class="radio">
                                            <input name="rdb_clave" type="radio" id="customRadio3" class="radio-col-grey" checked value="3"/>
                                            <label for="customRadio3">Solicitar cambio de contraseña cada 90 días <code>[Default]</code>.</label>
                                        </div>
                                        <div class="radio">
                                            <input name="rdb_clave" type="radio" id="customRadio1" class="radio-col-grey" value="1"/>
                                            <label for="customRadio1">Solicitar cambio de contraseña por única vez al siguiente inicio.</label>
                                        </div>
                                        <div class="radio">
                                            <input name="rdb_clave" type="radio" id="customRadio2" class="radio-col-grey" value="2"/>
                                            <label for="customRadio2">La Contraseña no caduca nunca.</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-12 text-center mb-10">
                                    <h4 class="text-bold">Envio de Credenciales:<br></h4>
                                    <span class="text-muted">
                                        Marque si requiere que se le envie las credenciales creadas al usuario ingresado.
                                    </span>
                                </div>

                                <div class="col-12 text-center">
                                    <div class="form-check">
                                        <label class="form-check-label cursor-pointer">
                                            <input class="form-check-input" type="checkbox" name="chk_senCredential" value="1"> Enviar credenciales
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-secondary-light-5">
                            <div class="row">
                                <div class="col-12 text-center">
                                    <button type="button" id="btnCancel_Tab" class="btn btn-light mr-10">
                                        Cancelar
                                    </button>
                                    <button type="submit" class="btn btn-info btn-hover-transform">
                                        Registrar usuario
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <br>
            <br>
            <?php
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_Usuario_JSON(){
        try {
            date_default_timezone_set("America/Lima");
            setlocale(LC_TIME, 'es_PE.UTF-8');

            $chkCredential = 0;
            if(isset($_POST['chk_senCredential'])){ $chkCredential = 1; }

            $obj_fn = new FuncionesModel();
            $nDocumento = $_POST['ndoc'];
            $perfil = (int)$_POST['perfil'];
            $idServicio = (int)$_POST['servicio'];
            $arrayAlmacen = explode(",",$_POST['arrayAlmacen']);
            $datesTAB[0] = $obj_fn->quitar_caracteresEspeciales($_POST['ape_pa']);
            $datesTAB[1] = $obj_fn->quitar_caracteresEspeciales($_POST['ape_ma']);
            $datesTAB[2] = $obj_fn->quitar_caracteresEspeciales($_POST['nombres']);
            $datesTAB[3] = $nDocumento;
            $datesTAB[4] = $_POST['email'];
            $datesTAB[5] = $obj_fn->quitar_caracteresEspeciales($_POST['puesto']);
            $datesTAB[6] = (int)$_POST['avatar'];
            $datesTAB[7] = $obj_fn->quitar_caracteresEspeciales($_POST['area']);

            $val = 0;
            $obj_per = new PersonaModel();
            //buscamos si la persona existe por nro doc
            $buscaPersona = $obj_per->buscar_Persona_xnDoc($nDocumento);

            if(is_null($buscaPersona)){
                $inserIDPER = $obj_per->registrar_Persona($datesTAB);
                if((int)$inserIDPER > 0){
                    $salt = '$bgr$/@.*¡¿]&';

                    $chpassNew = 0;
                    if((int)$_POST['rdb_clave'] == 2){ $chpassNew = 1; }

                    //Registramos datos Usuario Sistema
                    $textPassword = trim($_POST['txt_password_us']);
                    $userDetail[0] = (int)$perfil;
                    $userDetail[1] = (int)$inserIDPER;
                    $userDetail[2] = md5($salt.$textPassword);
                    $userDetail[3] = (int)$chpassNew;
                    $userDetail[4] = (int)$_POST['rdb_clave'];
                    $userDetail[5] = date("Y-m-d");
                    $obj_us = new UsuarioModel();
                    $insertIDUsuario = $obj_us->Registrar_Usuario_lastID($userDetail);
                    if((int)$insertIDUsuario > 0){
                        $val = 1;
                        //Usuario Servicio
                        $datesUserService[0] = (int)$idServicio;
                        $datesUserService[1] = (int)$insertIDUsuario;
                        $datesUserService[2] = date("Y-m-d H:i:s");
                        $obj_serv = new ServicioModel();
                        $inserIDSU = $obj_serv->registrar_Servicio_usuario_lastID($datesUserService);
                        if((int)$inserIDSU > 0){
                            //registramos Usuario Almacen
                            for ($i=0; $i<sizeof($arrayAlmacen); $i++){
                                $datesUserAlm[0] = (int)$inserIDSU;
                                $datesUserAlm[1] = (int)$arrayAlmacen[$i];
                                $datesUserAlm[2] = date("Y-m-d H:i:s");
                                $obj_us->registrar_Usuario_Almacen($datesUserAlm);
                            }
                        }

                        //Enviamos credenciales
                        if((int)$chkCredential == 1){
                            $this->enviarEmail_credencialesUsuario($_POST['ape_pa'],$_POST['ape_ma'],$_POST['nombres'],$_POST['email'],$nDocumento,$textPassword);
                        }
                    }
                }
            }
            else{
                $val = 2;}

            echo json_encode(array('status'=>$val));

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function enviarEmail_credencialesUsuario($ape_pa, $ape_ma, $nombres, $email, $ndoc, $pass){
        try {
            date_default_timezone_set("America/Lima");
            setlocale(LC_TIME, 'es_PE.UTF-8');

            $today = getdate();
            $hora = $today["hours"];
            if ($hora < 12) {
                $saludo = " Buenos días ";
            } else if ($hora <= 18) {
                $saludo = "Buenas tardes ";
            } else {
                $saludo = "Buenas noches ";
            }

            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->CharSet = "UTF-8";
            $mail->SMTPDebug = 2;
            // Configuración del servidor en modo seguro
            $mail->SMTPAuth = 'true';
            $mail->SMTPSecure = "STARTTLS";
            $mail->Host = "SMTP.Office365.com";
            $mail->Port = 587;
            $mail->Debugoutput = 'error_log';
            // Datos de autenticación
            $mail->Username = "soporte-imc@confipetrol.pe";
            $mail->Password = "Si22052018*";
            $mail->SetFrom ("soporte-imc@confipetrol.pe", "Soporte IMC");
            $mail->Subject = "Inventario - Credenciales Usuario";
            $mail->ContentType = "text/plain";
            //contenido de Mensaje
            $mail->IsHTML(true);

            $courp = '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="m_-59907513642489685mott_formulario">';
            $courp .='<tbody>';
            $courp .='<tr>';
            $courp .='<td align="center" valign="top" style="padding:0;margin:0;background:#efeeed">';
            $courp .=' <table width="100%" border="0" cellpadding="0" cellspacing="0" style="width:100%;max-width:600px">';
            $courp .='      <tbody>';
            $courp .='         <tr>';
            $courp .='            <td align="center" valign="top" style="padding:0;margin:0;background:#002b4d;color:#fff">';
            $courp .='               <table border="0" align="center" cellpadding="0" cellspacing="0" style="width:90%;max-width:600px">';
            $courp .='                 <tbody>';
            $courp .='                     <tr>';
            $courp .='                      <td width="471" align="left" style="padding:7px;padding-left:10px;font-family:Helvetica,Arial,sans-serif;font-size:24px;font-weight:100;color:#FFFFFF;text-align:left; vertical-align: middle">';
            $courp .='							<span>Asistencia Técnica</span>';
            $courp .='						 </td>';
            $courp .='						 <td width="118" align="right" style="padding:7px;padding-right:10px;font-family:Helvetica,Arial,sans-serif;font-size:24px;font-weight:100;color:#FFFFFF;text-align:right; vertical-align: middle">';
            $courp .='							<span>IMC</span>';
            $courp .='						 </td>';
            $courp .='                     </tr>';
            $courp .='                  </tbody>';
            $courp .='               </table>';
            $courp .='            </td>';
            $courp .='         </tr>';
            $courp .='         <tr>';
            $courp .='            <td>';
            $courp .='               <table border="0" align="center" cellpadding="0" cellspacing="0" style="width:100%;max-width:600px">';
            $courp .='                  <tbody>';
            $courp .='                     <tr>';
            $courp .='                        <td align="center" style="background:#fff;padding-top:30px;padding-bottom:10px;line-height:30px;font-family:Helvetica,Arial,sans-serif;text-align:center;font-size:30px;font-family:Helvetica,Arial,sans-serif;font-weight:100;color:#515151">';
            $courp .='                           <span style="font-size:30px;font-family:Helvetica,Arial,sans-serif;font-weight:100;color:#515151"><b>Control de Inventario</b></span>';
            $courp .='                        </td>';
            $courp .='                     </tr>';
            $courp .='                   </tbody>';
            $courp .='                </table>';
            $courp .='             </td>';
            $courp .='          </tr>';
            $courp .='          <tr>';
            $courp .='            <td>';
            $courp .='               <table border="0" align="center" cellpadding="0" cellspacing="0" style="background:#fff;width:100%;max-width:600px">';
            $courp .='                  <tbody>';
            $courp .='                     <tr>';
            $courp .='                        <td>';
            $courp .='                           <table width="100%" border="0">';
            $courp .='                              <tbody><tr>';
            $courp .='                                <td align="center" style="padding-top:20px;padding-bottom:25px;line-height:24px;font-family:Helvetica,Arial,sans-serif;text-align:center;font-size:24px;font-family:Helvetica,Arial,sans-serif;font-weight:100;color:#515151">';
            $courp .='                                    <span style="font-size:24px;font-family:Helvetica,Arial,sans-serif;font-weight:100;color:#797979">';
            $courp .=                                        $saludo;
            $courp .='                                    </span><br><br>';
            $courp .='									 <span style="font-size:24px;font-family:Helvetica,Arial,sans-serif;font-weight:100;color:#002b4d">';
            $courp .=                                        ucwords(strtolower($nombres)) .' ' .ucwords(strtolower($ape_pa)).' ' .ucwords(strtolower($ape_ma));
            $courp .='                                     </span>';
            $courp .='                                 </td>';
            $courp .='                              </tr>';
            $courp .='                           </tbody></table>';
            $courp .='                        </td>';
            $courp .='                    </tr>';
            $courp .='                     <tr>';
            $courp .='                        <td>';
            $courp .='                           <table width="100%" border="0">';
            $courp .='                             <tbody>';
            $courp .='                                 <tr>';
            $courp .='                                    <td align="center" style="padding-bottom:10px;line-height:21px;font-family:Helvetica,Arial,sans-serif;font-size:14px;text-align:center;color:#797979">';
            $courp .='                                       <span style="font-size:14px;font-family:Helvetica,Arial,sans-serif;color:#797979">';
            $courp .='                                        Se remite tus credenciales para el acceso a la <b>Plataforma de Control de Inventario</b>, la misma que se detalla a continuación:</span></td>';
            $courp .='                                 </tr>';
            $courp .='                                  <tr>';
            $courp .='                                   <td style="background:#f7f7f7;padding-top:10px;padding-bottom:10px ">';
            $courp .='									   <table border="0" align="center" style="background:#f7f7f7;width:100%;max-width:600px;font-size:14px;font-family:Helvetica,Arial,sans-serif;color:#797979">';
            $courp .='                                     <tbody>';
            if(trim($ndoc) == trim($pass)){
                $courp .= '                                       <tr>';
                $courp .= '                                         <td height="36" align="right" style="width:40%">Usuario y contraseña: </td>';
                $courp .= '                                        <td style="width:60%" align="left">';
                $courp .= '											 <b style="font-size:20px;font-family:Helvetica,Arial,sans-serif;font-weight:100;color:#002b4d">' . $ndoc . '</b>';
                $courp .= '										 </td>';
                $courp .= '                                       </tr>';
            }
            else {
                $courp .= '                                       <tr>';
                $courp .= '                                         <td height="36" align="right" style="width:40%">Usuario : </td>';
                $courp .= '                                        <td style="width:60%" align="left">';
                $courp .= '											 <b style="font-size:20px;font-family:Helvetica,Arial,sans-serif;font-weight:100;color:#002b4d">' . $ndoc . '</b>';
                $courp .= '										 </td>';
                $courp .= '                                       </tr>';
                $courp .= '                                       <tr>';
                $courp .= '                                          <td height="36" align="right" style="width:40%">Clave : </td>';
                $courp .= '                                        <td style="width:60%" align="left">';
                $courp .= '											 <b style="font-size:20px;font-family:Helvetica,Arial,sans-serif;font-weight:100;color:#002b4d">' . $pass . '</b>';
                $courp .= '										 </td>';
                $courp .= '                                       </tr>';
            }
            $courp .='                                     </tbody>';
            $courp .='                                   </table></td>';
            $courp .='                                 </tr>';
            $courp .='                                <tr>';
            $courp .='                                    <td align="center" style="padding:0 0 20px 0;line-height:12px;font-family:Helvetica,Arial,sans-serif;font-size:14px;text-align:center;color:#797979">';
            $courp .='                                       <span style="font-size:12px;font-family:Helvetica,Arial,sans-serif;color:#797979">';
            $courp .='                                            Las credenciales remitidas son temporales y se deben modificar al primer ingreso a la plataforma.';
            $courp .='                                       </span>';
            $courp .='                                   </td>';
            $courp .='                                 </tr>';
            $courp .='                              </tbody>';
            $courp .='                           </table>';
            $courp .='                        </td>';
            $courp .='                     </tr>';
            $courp .='                  </tbody>';
            $courp .='               </table>';
            $courp .='			  </td>';
            $courp .='        </tr>';

            $courp  .='         <tr>';
            $courp  .='            <td>';
            $courp  .='               <table border="0" align="center" cellpadding="0" cellspacing="0" style="padding-bottom:10px;padding-top:5px;background-color:#33cbe2;color:#fff;width:100%;max-width:600px">';
            $courp  .='                  <tbody><tr>';
            $courp  .='                     <td>';
            $courp  .='                        <table width="100%" border="0">';
            $courp  .='                           <tbody><tr>';
            $courp  .='                              <td align="center" style="line-height:19px;font-family:Helvetica,Arial,sans-serif;text-align:center;font-weight:100;color:#515151;padding: 7px">';
            $courp  .='                                 <span style="font-size:20px;font-family:Helvetica,Arial,sans-serif;font-weight:100;color:#fff">Link de acceso:</span>';
            $courp  .='                              </td>';
            $courp  .='                           </tr>';
            $courp  .='                          <tr>';
            $courp  .='                              <td align="center" valign="middle">';
            $courp  .='                                 <table width="100%" border="0">';
            $courp  .='                                     <tbody>';
            $courp  .='                                        <tr style="padding-left:20px;padding-right:20px;line-height:19px;font-family:Helvetica,Arial,sans-serif;font-size:14px;text-align:center;background-color:#FFFFFF;">';
            $courp  .='                                          <td align="center" bgcolor="#FFFFFF" style="padding-left:20px;padding:20px;text-align:center;color:#002b4d">';
            $courp  .='                                             <a target="_blank" href="https://inventario.imc-confipetrol.com" style="font-weight:normal;text-decoration:none; font-size:22px;font-family:Helvetica,Arial,sans-serif;color:#002b4d">';
            $courp  .='                                                Click aquí para ingresar';
            $courp  .='                                             </a>';
            $courp  .='                                          </td>';
            $courp  .='                                      </tr>';
            $courp  .='                                    </tbody>';
            $courp  .='                                 </table>';
            $courp  .='                              </td>';
            $courp  .='                           </tr>';
            $courp  .='                        </tbody></table>';
            $courp  .='                     </td>';
            $courp  .='                  </tr>';
            $courp  .='               </tbody></table>';
            $courp  .='            </td>';
            $courp  .='         </tr>';

            $courp  .='         <tr>';
            $courp  .='            <td>';
            $courp  .='               <table border="0" align="center" cellpadding="0" cellspacing="0" style="background:#fff;width:100%;max-width:600px">';
            $courp  .='                  <tbody>';
            $courp  .='                     <tr>';
            $courp  .='                        <td style="padding:10px">&nbsp;</td>';
            $courp  .='                     </tr>';
            $courp  .='                  </tbody>';
            $courp  .='               </table>';
            $courp  .='            </td>';
            $courp  .='         </tr>';

            $courp .='        <tr>';
            $courp .='            <td>';
            $courp .='               <table border="0" align="center" cellpadding="0" cellspacing="0" style="background:#002b4d;width:100%;max-width:600px">';
            $courp .='                  <tbody>';
            $courp .='                     <tr>';
            $courp .='                        <td style="padding-top:25px;padding-bottom:25px">';
            $courp .='                           <table width="100%" border="0">';
            $courp .='                              <tbody>';
            $courp .='                                 <tr>';
            $courp .='                                    <td align="center" valign="middle">';
            $courp .='                                      <span style="line-height:25px;font-family:Helvetica,Arial,sans-serif;font-size:14px;color:#fff">';
            $courp .='                                         Ingenieria de Mantenimiento y Confiabilidad - IMC<br>';
            $courp .='                                          Asistencia Técnica: soporte-imc@confipetrol.pe<br>';
            $courp .='                                          Lima - Perú';
            $courp .='                                          </span>';
            $courp .='                                    </td>';
            $courp .='                                 </tr>';
            $courp .='                              </tbody>';
            $courp .='                           </table>';
            $courp .='                       </td>';
            $courp .='                     </tr>';
            $courp .='                  </tbody>';
            $courp .='               </table>';
            $courp .='            </td>';
            $courp .='         </tr>';
            $courp .='      </tbody>';
            $courp .='   </table>';
            $courp .=' </td>';
            $courp .=' </tr>';
            $courp .=' </tbody>';
            $courp .='</table>';
            /*----------------------------------------------------------------------------------------------------*/
            $mail->msgHTML($courp);// Mensaje a enviar
            $mail->AltBody = "Usted esta viendo este mensaje simple debido a que su servidor de correo no admite formato HTML."; // Texto sin html

            // Destinatario del mensaje
            $mail->AddAddress (strtolower($email), $ape_pa." ".$ape_ma." ".$nombres);
            $mail->AddCC("soporte-imc@confipetrol.pe", "Soporte IMC");
            $exito = $mail->Send();

            $val = 0;
            if($exito) {  $val = 1; }

            return $val;

        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function Update_Estado_Usuario_JSON(){
        try {
            $estado = $_POST['estd'];
            $arrayID = $_POST['id'];
            $val = 0;
            $acierto = 0;
            $obj_tab = new UsuarioModel();
            for($i=0; $i<sizeof($arrayID); $i++){
                $datesTAB[0] = (int)$arrayID[$i];
                $datesTAB[1] = (int)$estado;
                $update = $obj_tab->update_Estado_Usuario($datesTAB);
                if($update){ $acierto++; }
            }
            if(sizeof($arrayID) == (int)$acierto){$val=1;}
            else if((int)$acierto > 1){$val = 2;}
            echo json_encode(array('status'=>$val));
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function loadCampos_EditarUsuario_Ajax(){
        try {
            $obj_fn = new FuncionesModel();
            $id = (int)$_GET['id'];
            $obj_us = new UsuarioModel();
            $dtlleUsuario = $obj_us->detalle_Usuario_xID($id);
            $obj_per = new PersonaModel();
            $dtllePER = $obj_per->detalle_Persona_xID($dtlleUsuario['id_per']);
            $obj_pf = new PerfilModel();
            $lstPerfil = $obj_pf->lst_Perfil_Activos_All();
            $obj_serv = new ServicioModel();
            $lstServiciosNoAsign = $obj_serv->lst_servicios_noAsignados_xUsuario($id);

            $lstServiciosAsig = $obj_serv->lst_servicios_Asignados_Activos_xIDUS($id);?>
            <div class="page-title">
                <h4 class="mb-0 text-warning">
                    Actualizar datos del Usuario
                </h4>
            </div>
            <ul class="nav nav-tabs nav-fill" role="tablist">
                <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#dpersonal" role="tab"><span class="display-7">Datos Personales</span></a> </li>
                <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#credential" role="tab"><span class="display-7">Credenciales</span></a> </li>
                <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#servicios" role="tab"><span class="display-7">Servicios</span></a> </li>
                <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#almacenes" role="tab"><span class="display-7">Almacenes</span></a> </li>
            </ul>
            <div class="tab-content tabcontent-border">
                <div class="tab-pane active" id="dpersonal" role="tabpanel">
                    <div class="card card-shadow">
                        <form id="formEditUsuario" role="form" method="post">
                            <input type="hidden" name="idper" value="<?=$dtllePER['id_per']?>"/>
                            <input type="hidden" name="idus" value="<?=$id?>"/>
                            <div class="card-body">
                                <p class="mb-20">
                                    Todos los campos descritos con <code>(*)</code>, son campos obligatorios.
                                </p>
                                <div class="row">
                                    <div class="col-12" id="mensajes_actions_add"></div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-4 col-md-4 col-sm-12 text-right control-label d-none d-sm-block col-form-label">
                                        Perfil
                                        <span class="text-danger">*</span>
                                    </label>
                                    <label class="col-12 text-left control-label d-block d-sm-none col-form-label pb-0">
                                        Perfil
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-sm-3">
                                        <select id="perfil" name="perfil" class="form-control selectClass" data-placeholder="Seleccione...">
                                            <option></option>
                                            <?php
                                            if(is_array($lstPerfil)){
                                                $i=1;
                                                foreach ($lstPerfil as $perfil) {
                                                    if((int)$perfil['id_perfil'] == (int)$dtlleUsuario['id_perfil']){?>
                                                        <option value="<?= $perfil['id_perfil'] ?>" selected><?= $i . " - " . $perfil['titulo_perfil'] ?></option>
                                                        <?php
                                                    }
                                                    else{?>
                                                        <option value="<?= $perfil['id_perfil'] ?>"><?= $i . " - " . $perfil['titulo_perfil'] ?></option>
                                                        <?php
                                                    }
                                                    $i++;
                                                }
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="ape_pa" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                        Área/Servicio
                                        <span class="text-danger font-weight-bold">*</span>
                                    </label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control input-md text-left"
                                               name="area" maxlength="45" required placeholder="área o servicio"
                                               value="<?=$dtllePER['area_servicio_per']?>">
                                        <small class="form-text text-muted">Máximo 150 carácteres</small>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-4 col-md-4 col-sm-12 text-right control-label d-none d-sm-block col-form-label">
                                        Apellido Paterno
                                        <span class="text-danger">*</span>
                                    </label>
                                    <label class="col-12 text-left control-label d-block d-sm-none col-form-label pb-0">
                                        Apellido Paterno
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control input-md text-left"
                                               name="ape_pa" maxlength="45" required placeholder="apellido paterno"
                                               value="<?=$dtllePER['ape_pa_per']?>">
                                        <small class="form-text text-muted">Máximo 45 carácteres</small>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-4 col-md-4 col-sm-12 text-right control-label d-none d-sm-block col-form-label">
                                        Apellido Materno
                                        <span class="text-danger">*</span>
                                    </label>
                                    <label class="col-12 text-left control-label d-block d-sm-none col-form-label pb-0">
                                        Apellido Materno
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control input-md text-left"
                                               name="ape_ma" maxlength="45" required placeholder="apellido materno"
                                               value="<?=$dtllePER['ape_ma_per']?>">
                                        <small class="form-text text-muted">Máximo 45 carácteres</small>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-4 col-md-4 col-sm-12 text-right control-label d-none d-sm-block col-form-label">
                                        Nombres
                                        <span class="text-danger">*</span>
                                    </label>
                                    <label class="col-12 text-left control-label d-block d-sm-none col-form-label pb-0">
                                        Nombres
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control input-md text-left"
                                               name="nombres" maxlength="45" required placeholder="nombres"
                                               value="<?=$dtllePER['nombres_per']?>">
                                        <small class="form-text text-muted">Máximo 45 carácteres</small>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-4 col-md-4 col-sm-12 text-right control-label d-none d-sm-block col-form-label">
                                        Número de DNI/CE.
                                        <span class="text-danger">*</span>
                                    </label>
                                    <label class="col-12 text-left control-label d-block d-sm-none col-form-label pb-0">
                                        Número de DNI/CE.
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-sm-3">
                                        <input type="number" class="form-control" placeholder="nro. documento" name="ndoc" id="ndoc" required autocomplete="off" maxlength="12"
                                               oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                               step="1" min="1" onkeydown="return event.keyCode !== 69" value="<?=$dtllePER['ndoc_per']?>">
                                        <small class="form-text text-muted">Máximo 12 digitos</small>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-4 col-md-4 col-sm-12 text-right control-label d-none d-sm-block col-form-label">
                                        Cargo/Puesto
                                        <span class="text-danger">*</span>
                                    </label>
                                    <label class="col-12 text-left control-label d-block d-sm-none col-form-label pb-0">
                                        Cargo/Puesto
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control input-md text-left"
                                               name="puesto" maxlength="50" required placeholder="puesto de trabajo"
                                               value="<?=$dtllePER['cargo_per']?>">
                                        <small class="form-text text-muted">Máximo 50 carácteres</small>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-4 col-md-4 col-sm-12 text-right control-label d-none d-sm-block col-form-label">
                                        Email Personal/Corporativo
                                        <span class="text-danger">*</span>
                                    </label>
                                    <label class="col-12 text-left control-label d-block d-sm-none col-form-label pb-0">
                                        Email Personal/Corporativo
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-sm-4">
                                        <input type="email" class="form-control input-md text-left"
                                               name="email" maxlength="45" required placeholder="email" value="<?=$dtllePER['email_per']?>">
                                        <small class="form-text text-muted">Máximo 45 carácteres</small>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-4 col-md-4 col-sm-12 text-right control-label d-none d-sm-block col-form-label">
                                        Avatar
                                        <span class="text-danger">*</span>
                                    </label>
                                    <label class="col-12 text-left control-label d-block d-sm-none col-form-label pb-0">
                                        Avatar
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-sm-2">
                                        <select name="avatar" class="form-control selectClass" data-placeholder="Seleccione...">
                                            <option></option>
                                            <?php
                                            if((int)$dtllePER['avatar_per'] == 1){?>
                                                <option value="1" selected>Hombre</option>
                                                <option value="2">Mujer</option>
                                                <?php
                                            }
                                            else {?>
                                                <option value="1">Hombre</option>
                                                <option value="2" selected>Mujer</option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-4 col-md-4 col-sm-12 text-right control-label d-none d-sm-block col-form-label">
                                        Estado
                                        <span class="text-danger">*</span>
                                    </label>
                                    <label class="col-12 text-left control-label d-block d-sm-none col-form-label pb-0">
                                        Estado
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-sm-2">
                                        <select name="estado" class="form-control selectClass" data-placeholder="Seleccione...">
                                            <option></option>
                                            <?php
                                            if((int)$dtllePER['condicion_per'] == 1){?>
                                                <option value="1" selected>Activo</option>
                                                <option value="0">Suspendido</option>
                                                <?php
                                            }
                                            else {?>
                                                <option value="1">Activo</option>
                                                <option value="0" selected>Suspendido</option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-12 text-center">
                                        <button type="button" id="btnCancel_Tab" class="btn btn-outline-secondary mr-10">
                                            Cancelar
                                        </button>
                                        <button type="submit" class="btn bg-warning btn-hover-transform">
                                            Actualizar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="tab-pane" id="credential" role="tabpanel">
                    <div class="card card-shadow">
                        <form id="formEditCredenciales" role="form" method="post">
                            <input type="hidden" name="idusc" value="<?=$id?>"/>
                            <div class="card-body">
                                <p class="mb-20 text-center">
                                    Todos los campos con <code class="font-weight-bold">( * )</code>, son campos obligatorios.
                                </p>
                                <p class="text-muted mb-20 text-center">
                                    Los datos consignados en los campos siguientes son para el acceso al sistema, este proceso permite modificar la contraseña del usuario.
                                </p>
                                <div class="row">
                                    <div class="col-12" id="mensajes_actions_cred"></div>
                                </div>
                                <div class="row form-group mb-10">
                                    <label class="col-lg-4 col-md-4 col-sm-12 text-right control-label d-none d-sm-block col-form-label">
                                        Usuario
                                        <span class="text-danger">*</span>
                                    </label>
                                    <label class="col-12 text-left control-label d-block d-sm-none col-form-label pb-0">
                                        Usuario
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-3 col-md-3 col-sm-5 col-xs-12">
                                        <input class="form-control" type="text" autocomplete="off" id="txt_usuario_us"
                                               name="txt_usuario_us" placeholder="nombre de usuario" disabled value="<?=$dtllePER['ndoc_per']?>">
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <label class="col-lg-4 col-md-4 col-sm-12 text-right control-label d-none d-sm-block col-form-label">
                                        Contraseña
                                        <span class="text-danger">*</span>
                                    </label>
                                    <label class="col-12 text-left control-label d-block d-sm-none col-form-label pb-0">
                                        Contraseña
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="col-lg-3 col-md-3 col-sm-5 col-xs-12 mb-10">
                                        <input class="form-control" type="text" autocomplete="off" name="txt_password_us"
                                               placeholder="contraseña" required maxlength="35" value="<?="Cf*".date("dmY")?>">
                                    </div>
                                </div>
                                <hr>
                                <div class="row form-group mb-10">
                                    <div class="col-12 text-center">
                                        <h4 class="text-bold">Opciones de inicio de sesión:<br></h4>
                                        <span class="text-muted">
                                            Marque los accesos con el que contara el usuario al ingresar al sistema.
                                        </span>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 offset-lg-3 offset-md-3 text-center">
                                        <div class="form-group pt-15">
                                            <div class="radio">
                                                <input name="rdb_clave" type="radio" id="customRadio3" class="radio-col-grey" checked value="3"/>
                                                <label for="customRadio3">Solicitar cambio de contraseña cada 90 días <code>[Default]</code>.</label>
                                            </div>
                                            <div class="radio">
                                                <input name="rdb_clave" type="radio" id="customRadio1" class="radio-col-grey" value="1"/>
                                                <label for="customRadio1">Solicitar cambio de contraseña por única vez al siguiente inicio.</label>
                                            </div>
                                            <div class="radio">
                                                <input name="rdb_clave" type="radio" id="customRadio2" class="radio-col-grey" value="2"/>
                                                <label for="customRadio2">La Contraseña no caduca nunca.</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-12 text-center mb-10">
                                        <h4 class="text-bold">Envio de Credenciales:<br></h4>
                                        <span class="text-muted">
                                       Marque si requiere que se le envie las credenciales creadas al usuario ingresado.
                                    </span>
                                    </div>

                                    <div class="col-12 text-center">
                                        <div class="form-check">
                                            <label class="form-check-label cursor-pointer">
                                                <input class="form-check-input" type="checkbox" name="chk_senCredential" value="1"> Enviar credenciales
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-12 text-center">
                                        <button type="button" id="btnCancel_Tab" class="btn btn-outline-secondary mr-10">
                                            Cancelar
                                        </button>
                                        <button type="submit" class="btn bg-warning btn-hover-transform">
                                            Actualizar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="tab-pane" id="servicios" role="tabpanel">
                    <div class="card card-shadow">
                        <div class="card-body">
                            <p class="text-muted mb-20 text-center">
                                Elija los servicios en la cual el usuario podrá acceder.
                            </p>
                            <form id="addService_Usuario" role="form">
                                <input type="hidden" id="iduserv" name="iduserv" value="<?=$id?>">
                                <div class="row">
                                    <div class="col-12" id="mensaje_servicio"></div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-xl-8 col-lg-8 col-md-4 col-sm-12 col-xs-12 mb-10">
                                        <select class="form-control selectClass" id="edit_Servicio" name="sel_Servicio" data-placeholder="Servicio..." required>
                                            <option></option>
                                            <?php
                                            if(is_array($lstServiciosNoAsign)){
                                                foreach ($lstServiciosNoAsign as $servicio){?>
                                                    <option value="<?=$servicio['id_serv']?>"><?=$servicio['des_serv']?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col mb-10">
                                        <button type="submit" class="btn btn-icon btn-primary btn-icon-style-1">
                                        <span class="btn-icon-wrap">
                                            <i class="icon-plus"></i>
                                        </span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <hr class="mb-10 mt-0">
                            <div class="row">
                                <div class="col-12">
                                    <div class="mensaje_actions_userService"></div>
                                    <table id="servicioUsuario" class="table table-sm table-bordered">
                                        <thead>
                                        <th>#</th>
                                        <th>Servicio</th>
                                        <th>F.Registro</th>
                                        <th>Estado</th>
                                        <th></th>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $lstServiciosUsuario = $obj_serv->lista_Servicios_Asignados_All_xIdUsuario($id);
                                        if(is_array($lstServiciosUsuario)){
                                            $i = 1;
                                            foreach ($lstServiciosUsuario as $servicio){
                                                $tituloServicio = "";
                                                $dtlleServicio = $obj_serv->detalle_Servicio_xID($servicio['id_serv']);
                                                if(is_array($dtlleServicio)) {
                                                    $tituloServicio = $dtlleServicio['des_serv'];
                                                }

                                                $estado = '<span class="label label-block text-danger">Baja</span>';
                                                if ((int)$servicio['condicion_su'] == 1) {
                                                    $estado = '<span class="label label-block text-success ">Activo</span>';
                                                }?>
                                                <tr>
                                                    <td class="text-center"><?=$i?></td>
                                                    <td class="text-left"><?=$tituloServicio?></td>
                                                    <td class="text-center"><?=$obj_fn->fechaHora_ENG_ESP($servicio['fechareg_su'])?></td>
                                                    <td class="text-center"><?=$estado?></td>
                                                    <td class="text-center">
                                                        <div class="button-list">
                                                            <?php
                                                            if ((int)$servicio['condicion_su'] == 1) {?>
                                                                <button class="btn btn-icon btn-warning btn-icon-style-1 mt-0 btn-hover-transform mr-10" title="Dar baja" id="btnBajaAlta_userServicio" data-id="<?=$servicio['id_su']?>" data-estd="0">
                                                               <span class="btn-icon-wrap">
                                                                   <span class="material-icons">thumb_down</span>
                                                               </span>
                                                                </button>
                                                                <?php
                                                            }
                                                            else if ((int)$servicio['condicion_su'] == 0) {?>
                                                                <button class="btn btn-icon btn-primary btn-icon-style-1 mt-0 btn-hover-transform mr-10" title="Dar alta" id="btnBajaAlta_userServicio" data-id="<?=$servicio['id_su']?>" data-estd="1">
                                                               <span class="btn-icon-wrap">
                                                                   <span class="material-icons">thumb_up</span>
                                                               </span>
                                                                </button>
                                                                <?php
                                                            }
                                                            ?>
                                                            <button class="btn btn-icon btn-danger btn-icon-style-1 mt-0  btn-hover-transform" title="Eliminar" id="btnDelete_userServicio" data-id="<?=$servicio['id_su']?>">
                                                               <span class="btn-icon-wrap">
                                                                   <span class="material-icons">delete</span>
                                                               </span>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php
                                                $i++;
                                            }
                                        }
                                        else{?>
                                            <tr><td colspan="5" class="text-center">No se encontraron registros asociados al usuario.</td></tr>
                                            <?php
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="almacenes" role="tabpanel">
                    <div class="card card-shadow">
                        <div class="card-body">
                            <p class="mb-20">
                                Agregue o elimine los almaneces que tendra acceso el usuario.
                            </p>
                            <form id="addServiceUsuario_Almacen" role="form">
                                <input type="hidden" name="iduserv" value="<?=$id?>">
                                <div class="row">
                                    <div class="col-12" id="mensaje_servicio"></div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-10">
                                        <select class="form-control" id="edit_ServicioAlm" name="sel_ServicioAlm" data-placeholder="Servicio..." required>
                                            <option></option>
                                            <?php
                                            if(is_array($lstServiciosAsig)){
                                                foreach ($lstServiciosAsig as $servicioa){
                                                    $dtlleServicio = $obj_serv->detalle_Servicio_xID($servicioa['id_serv']);?>
                                                    <option value="<?=$servicioa['id_su']?>"><?=$dtlleServicio['des_serv']?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12 mb-10">
                                        <select class="form-control" id="almacenSelect" name="almacen" data-placeholder="Almacén..." required>
                                            <option></option>
                                        </select>
                                    </div>
                                    <div class="col mb-10">
                                        <button type="submit" class="btn btn-icon btn-primary btn-icon-style-1">
                                        <span class="btn-icon-wrap">
                                            <i class="icon-plus"></i>
                                        </span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <hr class="no-padding no-margin">
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-12">
                                    <div class="mensaje_actions_userAlmacen"></div>
                                    <table id="usuarioAlmacen" class="table table-sm table-bordered">
                                        <thead>
                                        <th>#</th>
                                        <th>Servicio</th>
                                        <th>Almacén</th>
                                        <th>Estado</th>
                                        <th></th>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $obj_alm = new AlmacenModel();
                                        $lstAlmacenUsuario = $obj_alm->lst_Almacenes_All_Asignados_xUsuario($id);
                                        if(is_array($lstAlmacenUsuario)){
                                            $i = 1;
                                            $obj_serv = new ServicioModel();
                                            foreach ($lstAlmacenUsuario as $almacen){
                                                $tituloServicio = "";
                                                $dtlleServicio = $obj_serv->detalle_Servicio_xID($almacen['id_serv']);
                                                if(is_array($dtlleServicio)) {
                                                    $tituloServicio = $dtlleServicio['des_serv'];
                                                }

                                                $estado = '<span class="label label-block text-danger">Baja</span>';
                                                if ((int)$almacen['condicion_ual'] == 1) {
                                                    $estado = '<span class="label label-block text-success ">Activo</span>';
                                                }?>
                                                <tr>
                                                    <td class="text-center"><?=$i?></td>
                                                    <td class="text-left"><?=$tituloServicio?></td>
                                                    <td class="text-left"><?=$almacen['titulo_alm']?></td>
                                                    <td class="text-center"><?=$estado?></td>
                                                    <td class="text-center">
                                                        <div class="button-list">
                                                            <button class="btn btn-icon btn-danger btn-icon-style-1 mt-0  btn-hover-transform" title="Eliminar" id="btnDelete_userServicioAlm"
                                                                    data-idual="<?=$almacen['id_ual']?>" data-idus="<?=$id?>">
                                                               <span class="btn-icon-wrap">
                                                                   <span class="material-icons">delete</span>
                                                               </span>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php
                                                $i++;
                                            }
                                        }
                                        else{?>
                                            <tr><td colspan="5" class="text-center">No se encontraron registros asociados al usuario.</td></tr>
                                            <?php
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <br>
            <?php
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function update_Usuario_JSON(){
        try {
            $obj_fn = new FuncionesModel();
            $val = 0;
            $nDocumento = $_POST['ndoc'];
            $datesPERS[0] = (int)$_POST['idper'];
            $datesPERS[1] = $obj_fn->quitar_caracteresEspeciales($_POST['ape_pa']);
            $datesPERS[2] = $obj_fn->quitar_caracteresEspeciales($_POST['ape_ma']);
            $datesPERS[3] = $obj_fn->quitar_caracteresEspeciales($_POST['nombres']);
            $datesPERS[4] = $nDocumento;
            $datesPERS[5] = $_POST['email'];
            $datesPERS[6] = $obj_fn->quitar_caracteresEspeciales($_POST['puesto']);
            $datesPERS[7] = (int)$_POST['avatar'];
            $datesPERS[8] = $obj_fn->quitar_caracteresEspeciales($_POST['area']);

            $obj_per = new PersonaModel();
            $updatePER = $obj_per->actualizar_Persona($datesPERS);

            $datesUSERS[0] = (int)$_POST['idus'];
            $datesUSERS[1] = (int)$_POST['perfil'];
            $datesUSERS[2]= (int)$_POST['estado'];
            $obj_us = new UsuarioModel();
            $updateUS = $obj_us->actualizar_Usuario($datesUSERS);
            if($updatePER && $updateUS){
                $val = 1;
            }
            echo json_encode(array('status'=>$val));

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function update_Credenciales_JSON(){
        try {
            date_default_timezone_set("America/Lima");
            setlocale(LC_TIME, 'es_PE.UTF-8');
            $chkCredential = 0;
            if(isset($_POST['chk_senCredential'])){ $chkCredential = 1; }
            $IdUsuario = $_POST['idusc'];
            $textPassword = trim($_POST['txt_password_us']);
            $val = 0;
            $salt = '$bgr$/@.*¡¿]&';
            $datesUSERS[0] = (int)$_POST['idusc'];
            $datesUSERS[1] = md5($salt.$textPassword);
            $datesUSERS[2] = 0;
            $datesUSERS[3] = (int)$_POST['rdb_clave'];
            $datesUSERS[4] = date("Y-m-d");
            $obj_us = new UsuarioModel();
            $updateUS = $obj_us->actualizar_Credenciales($datesUSERS);
            if($updateUS){
                $val = 1;
                if((int)$chkCredential == 1){
                    $dtlleUsuario = $obj_us->detalle_Usuario_xID($IdUsuario);
                    if(is_array($dtlleUsuario)){
                        $obj_per = new PersonaModel();
                        $dtllePersona = $obj_per->detalle_Persona_xID($dtlleUsuario['id_per']);
                        if(is_array($dtllePersona)){
                            $this->enviarEmail_credencialesUsuario($dtllePersona['ape_pa_per'],$dtllePersona['ape_ma_per'],$dtllePersona['nombres_per'],$dtllePersona['email_per'],$dtllePersona['ndoc_per'],$textPassword);
                        }
                    }

                }
            }
            echo json_encode(array('status'=>$val));

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function list_View_Rows_File(){
        try {
            $filename = $_FILES['filedata_import']['tmp_name'];
            $tipo = 1;

            $reader = IOFactory::createReader('Xlsx');
            $spreadsheet = $reader->load($filename);

            $sheetCount = $spreadsheet->getSheetCount();
            $datosArray = array();

            if((int)$sheetCount == 1){
                $data = $spreadsheet->getActiveSheet()->toArray();

                $arreglo = array();
                $fila_vacio = 0;
                for ($row = 3; $row <= sizeof($data); $row++){
                    unset($arreglo);
                    $column_vacio = 0;
                    /********************** Persona **********************/
                    if($tipo == 1) {
                        for($t=0; $t<=6; $t++){
                            if($data[$row][$t]!= null || !empty($data[$row][$t])){
                                $arreglo[] = trim($data[$row][$t]);
                            }
                            else{
                                $arreglo[] = "";
                                $column_vacio++;
                            }
                        }

                        //verificamos cuantos valores nulos tiene
                        if((int)$column_vacio < 4){
                            array_push($datosArray, $arreglo);
                        }
                        else {
                            $fila_vacio++;
                        }
                    }
                }

                if($fila_vacio < sizeof($data)){
                    $type = 1; //correcto
                    $countSheets = (int)$sheetCount; //cantidad de hojas
                }
                else{
                    $type = 0; //archivo vacio
                    $countSheets = (int)$sheetCount; //cantidad de hojas
                }
            }
            else{
                $type = 2; // archivo con muchas hojas
                $countSheets = (int)$sheetCount; //cantidad de hojas
            }

            $response = array(
                'status'=> $type,
                'countSheets'=>$countSheets,
                'data'=> $datosArray
            );

            echo json_encode($response);

        }
        catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_usuario_Import_JSON(){
        try {
            date_default_timezone_set("America/Lima");
            setlocale(LC_TIME, 'es_PE.UTF-8');
            $obj_fn = new FuncionesModel();
            $nDocumento = $_POST['ndoc'];
            $idPerfil = (int)$_POST['perfil'];
            $apema = "";
            if(!empty($_POST['apema']) && !is_null($_POST['apema'])){
                $apema = $obj_fn->quitar_caracteresEspeciales($_POST['apema']);
            }
            $email= "";
            if(!empty($_POST['email']) && !is_null($_POST['email'])){
                $email = $_POST['email'];
            }
            $puesto= "";
            if(!empty($_POST['puesto']) && !is_null($_POST['puesto'])){
                $puesto = $obj_fn->quitar_caracteresEspeciales($_POST['puesto']);
            }

            $datesREG[0] = $obj_fn->quitar_caracteresEspeciales($_POST['apepa']);
            $datesREG[1] = $apema;
            $datesREG[2] = $obj_fn->quitar_caracteresEspeciales($_POST['nombres']);
            $datesREG[3] = $nDocumento;
            $datesREG[4] = $email;
            $datesREG[5] = $puesto;
            $datesREG[6] = 1;
            $datesREG[7] = $obj_fn->quitar_caracteresEspeciales($_POST['area']);

            $val = 0;
            $obj_per = new PersonaModel();
            //buscamos si la persona existe por nro doc
            $buscaPersona = $obj_per->buscar_Persona_xnDoc($nDocumento);
            if(is_null($buscaPersona)){
                $inserIDPER = $obj_per->registrar_Persona($datesREG);
                if((int)$inserIDPER > 0){
                    $salt = '$bgr$/@.*¡¿]&';
                    //Registramos datos Usuario Sistema
                    $userDetail[0] = $idPerfil;
                    $userDetail[1] = (int)$inserIDPER;
                    $userDetail[2] = md5($salt.$nDocumento);
                    $userDetail[3] = 0;
                    $userDetail[4] = 3;
                    $userDetail[5] = date("Y-m-d");
                    $obj_us = new UsuarioModel();
                    $insertUsuario = $obj_us->Registrar_Usuario($userDetail);
                    if($insertUsuario){$val = 1;}
                }
            }
            else{ $val = 2;}
            echo json_encode(array('status'=>$val));
        }
        catch (PDOException $e) {
            throw $e;
        }
    }

    public function Change_Password_Default_Token_JSON(){
        try {
            date_default_timezone_set("America/Lima");
            setlocale(LC_TIME, 'es_PE.UTF-8');

            $idtk = $_POST['idtkus_chps'];
            $salt = '$bgr$/@.*¡¿]&';
            $passActual = md5($salt.$_POST['iclave_actual']);
            $clave = $_POST['iclave_new'];
            $val = 0;
            $message = "Se encontro problemas al actualizar la contraseña, verique que se haya ingresado la contraseña correctamente.";
            $obj_fn = new FuncionesModel();
            $IdUsuario = $obj_fn->encrypt_decrypt('decrypt',$idtk);

            $obj_us = new UsuarioModel();
            $detalleUsuario = $obj_us->detalle_Usuario_xID($IdUsuario);

            if(is_array($detalleUsuario)){
                if($detalleUsuario['pass_us'] == $passActual){
                    //captura de Datos
                    $datesUSERS[0] = $obj_fn->encrypt_decrypt('decrypt', $idtk);
                    $datesUSERS[1] = md5($salt.$clave);
                    $datesUSERS[2] = 1;
                    $datesUSERS[3] = date("Y-m-d");
                    $changePASS = $obj_us->Change_Password_Usuario_Default($datesUSERS);
                    if($changePASS){
                        $val = 1;
                        $message = "Contraseña modificada satisfactoriamente.";
                    }
                }
                else{
                    $val = 2;
                    $message = "La Contraseña Actual no es la correcta, ingrese nuevamente su contraseña Actual.";
                }
            }

            echo json_encode(array('status'=>$val, 'message'=>$message));

        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function verifica_password_Usuario_Ajax(){
        try {
            $idus = (int)$_GET['idus'];
            $clave = $_GET['pass_ing'];

            $salt = '$bgr$/@.*¡¿]&';
            $pass = md5($salt . $clave);

            $obj_u = new UsuarioModel();
            $dtlleUsuario = $obj_u->detalle_Usuario_xID($idus);
            $val = 0;
            if ($pass == $dtlleUsuario['pass_us']) { $val =  1; }
            echo json_encode(array('status'=>$val));
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function Change_Password_Usuario_Ajax(){
        try {
            //captura de Datos
            $clave = $_POST['clave_new'];
            $salt = '$bgr$/@.*¡¿]&';
            $datesUSERS[0] = (int)$_POST['us_pss'];
            $datesUSERS[1] = md5($salt . $clave);
            $datesUSERS[2] = 1;
            $datesUSERS[3] = date("Y-m-d");
            $val = 0;
            $obj_m = new UsuarioModel();
            $changePS = $obj_m->Change_Password_Usuario_Default($datesUSERS);
            if($changePS){$val = 1;}
            echo json_encode(array('status'=>$val));
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function altaBaja_UsuarioServicio_JSON(){
        try {
            $idUserService = $_POST['id'];
            $obj_us = new UsuarioModel();
            $dtlleUserService = $obj_us->detalle_UsuarioServicio_xID($idUserService);
            $idUsuario = 0;
            if(is_array($dtlleUserService)){
                $idUsuario = $dtlleUserService['id_us'];
            }
            $datesUserService[0] = $idUserService;
            $datesUserService[1] = $_POST['estado'];
            $val = 0;
            $updateEstado = $obj_us->update_Estado_UsuarioServicio($datesUserService);
            if($updateEstado) { $val = 1; }

            echo json_encode(array('status'=>$val,'id'=>(int)$idUsuario));

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function delete_UsuarioServicio_JSON(){
        try {
            $idUserService = $_POST['id'];
            $obj_us = new UsuarioModel();
            $dtlleUserService = $obj_us->detalle_UsuarioServicio_xID($idUserService);
            $idUsuario = 0;
            if(is_array($dtlleUserService)){
                $idUsuario = $dtlleUserService['id_us'];
            }
            $val = 0;
            $delete = $obj_us->delete_UsuarioServicio_xID($idUserService);
            if($delete) { $val = 1; }

            echo json_encode(array('status'=>$val,'id'=>$idUsuario));

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_Servicios_Asignados_xUsuario_JSON(){
        try {
            $IdUsuario= (int)$_GET['id'];

            $obj_fn = new FuncionesModel();
            $obj_serv = new ServicioModel();
            $lstServiciosUsuario = $obj_serv->lista_Servicios_Asignados_All_xIdUsuario($IdUsuario);
            $datos = array();
            if(is_array($lstServiciosUsuario)){
                foreach ($lstServiciosUsuario as $servicio){
                    $tituloServicio = "";
                    $dtlleServicio = $obj_serv->detalle_Servicio_xID($servicio['id_serv']);
                    if(is_array($dtlleServicio)) {
                        $tituloServicio = $dtlleServicio['des_serv'];
                    }
                    $row = array(
                        'id'=>$servicio['id_su'],
                        'servicio'=> $tituloServicio,
                        'estado'  => $servicio['condicion_su'],
                        'fecha'=> $obj_fn->fechaHora_ENG_ESP($servicio['fechareg_su'])
                    );
                    array_push($datos, $row);

                }
            }

            echo json_encode($datos);


        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function add_UsuarioServicio_JSON(){
        try {
            date_default_timezone_set("America/Lima");
            setlocale(LC_TIME, 'es_PE.UTF-8');
            $IdUsuario = $_POST['iduserv'];
            $datesUserService[0] = $_POST['sel_Servicio'];
            $datesUserService[1] = $IdUsuario;
            $datesUserService[2] = date("Y-m-d H:i:s");
            $obj_us = new UsuarioModel();
            $insertServicioUsuario = $obj_us->add_UsuarioServicio($datesUserService);
            $val = 0;
            if($insertServicioUsuario) {$val = 1;}
            echo json_encode(array('status'=>$val,'id'=>(int)$IdUsuario));

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_Servicios_All_Activas_xUsuario_JSON(){
        try {
            $IdUsuario = $_GET['id'];
            $obj_serv = new ServicioModel();
            $lstServicios = $obj_serv->lst_servicios_noAsignados_xUsuario($IdUsuario);

            $datos = array();
            if(count($lstServicios)>0){
                foreach ($lstServicios as $servicio){
                    $row = array(
                        'id'=>$servicio['id_serv'],
                        'texto'=>$servicio['des_serv']

                    );
                    array_push($datos, $row);
                }
            }
            echo json_encode($datos);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_Servicios_Asinados_Activas_xUsuario_JSON(){
        try {
            $IdUsuario = $_GET['id'];
            $obj_serv = new ServicioModel();
            $lstServicios = $obj_serv->lst_servicios_Asignados_Activos_xIDUS($IdUsuario);

            $datos = array();
            if(count($lstServicios)>0){
                foreach ($lstServicios as $servicio){
                    $dtlleServicio = $obj_serv->detalle_Servicio_xID($servicio['id_serv']);
                    $row = array(
                        'id'=>$servicio['id_su'],
                        'texto'=>$dtlleServicio['des_serv']

                    );
                    array_push($datos, $row);
                }
            }
            echo json_encode($datos);
        } catch (PDOException $e) {
            throw $e;
        }
    }

}

