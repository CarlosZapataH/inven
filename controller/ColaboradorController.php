<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);
require_once '../model/ColaboradorModel.php';
require_once '../model/GerenciaModel.php';
require_once '../model/ServicioModel.php';
require_once '../model/FuncionesModel.php';
require_once '../assets/plugins/phpspreadsheet-1.17.1.0/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$action = $_REQUEST["action"];
$controller = new ColaboradorController();
call_user_func(array($controller,$action));

class ColaboradorController {

    public function lista_Colaborador_xServicio_JSON(){
        try {
            $IdServicioUsuario = (int)$_GET['IdServicioUsuario'];
            $obj_serv = new ServicioModel();
            $dtlleSerUsuario = $obj_serv->detalle_ServicioUsuario_xIDSU($IdServicioUsuario);
            $idServicio = 0;
            if(!is_null($dtlleSerUsuario)){  $idServicio = $dtlleSerUsuario['id_serv']; }

            $obj_col = new ColaboradorModel();
            $lstColab = $obj_col->lst_Colaborador_xServicio($idServicio);

            $datos = array();
            if (is_array($lstColab)) {
                foreach ($lstColab as $colab) {

                    $btnBaja= '<a class="cursor-pointer text-hover-primary" id="bajaAltaCol" data-id="'.$colab['id_col'].'" data-opc="0" title="Baja"><i class="f30 opacity-7 ti-thumb-down"></i></a>';
                    $btnAlta= '<a class="cursor-pointer text-hover-primary" id="bajaAltaCol" data-id="'.$colab['id_col'].'" data-opc="1" title="Alta"><i class="f30 opacity-7 ti-thumb-up"></i></a>';
                    $btnEdit= '<a class="cursor-pointer text-hover-primary ml-10" id="editPersonal" data-id="'.$colab['id_col'].'" title="Editar"><i class="f30 opacity-7 ti-pencil"></i></a>';
                    $btnDel = '<a class="cursor-pointer text-hover-danger ml-10" id="deleteCol" data-id="'.$colab['id_col'].'" title="Eliminar"><i class="f30 opacity-7 ti-trash"></i></a>';

                    $optBajaAlta = "";
                    $optEdit = "";
                    $optDel = "";
                    if ((int)$colab['condicion_col'] == 1) {
                        $estado = '<span class="label label-block text-success-600">ACTIVO</span>';
                        $optBajaAlta = $btnBaja;
                        $optEdit = $btnEdit;
                        $optDel = $btnDel;
                    }
                    else if ((int)$colab['condicion_col'] == 0) {
                        $estado = '<span class="label label-block text-danger">CESADO</span>';
                        $optBajaAlta = $btnAlta;
                    }

                    $nameApel = $colab['apa_col'].", ".$colab['nombres_col'];
                    if(!empty(trim($colab['ama_col']))){
                        $nameApel = $colab['apa_col']." ".$colab['ama_col'].", ".$colab['nombres_col'];
                    }

                    $biometric = "";
                    if((int)$colab['biometria_col'] == 1){
                        $biometric = '<i class="fas fa-fingerprint fz-30"></i>';
                    }


                    $row = array(
                        0 => $colab['servicio_col'],
                        1 => $colab['areaop_col'],
                        2 => $nameApel,
                        3 => $colab['ndoc_col'],
                        4 => $biometric,
                        5 => $estado,
                        6 => $optBajaAlta.$optDel
                    );

                    array_push($datos, $row);
                }
            }

            $tabla = array('data' => $datos);
            echo json_encode($tabla);
            unset($datos);

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function searching_Colaborador_JSON(){
        try {
            $ndoc = trim($_POST['ndoc_col']);
            $val = 0;
            $mesage = 'El número ingresado no pertenece a ningún colaborador registrado, verifique nuevamente el número de documento <code>(DNI/CEX)</code> y vuelva a intentarlo';
            $idntify = null;
            $obj_col = new ColaboradorModel();
            $obj_fn = new FuncionesModel();
            $dtlleCol = $obj_col->buscar_colaborador_xnDoc($ndoc);
            if(!is_null($dtlleCol)){
                $val = 1;
                $mesage = 'Colaborador validado';
                $idntify = $obj_fn->encrypt_decrypt("encrypt",$dtlleCol['id_col']);
            }

            echo json_encode(array('status'=>$val,'idntify'=>$idntify,'message'=>$mesage));

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function searching_Colaborador_Autocomplete_JSON(){
        try {
            $ndoc = trim($_GET['ndoc']);
            $val = 0;
            $mesage = 'El número ingresado no pertenece a ningún colaborador registrado, verifique nuevamente el número de documento <code>(DNI/CEX)</code> y vuelva a intentarlo';
            $name = null;
            $obj_col = new ColaboradorModel();
            $dtlleCol = $obj_col->buscar_colaborador_xnDoc($ndoc);
            if(!is_null($dtlleCol)){
                $val = 1;
                $name = trim($dtlleCol['nombres_col'])." ".trim($dtlleCol['apa_col'])." ".trim($dtlleCol['ama_col']);
                $mesage = "Personal identificado.";
            }

            echo json_encode(array('status'=>$val,'idntify'=>$name,'message'=>$mesage));

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function validacionBiometrica_JSON(){
        try {

            echo json_encode(array('status'=>1));

        } catch (PDOException $e) {
            throw $e;
        }
    } //En revision

    public function altaBaja_Colaborador_JSON(){
        try {
            $datesUpdate[0] = (int)$_POST['id'];
            $datesUpdate[1] = (int)$_POST['estado'];
            $val = 0;
            $message = "Se produjo un error al intentar actualizar el estado del Colaborador.";
            $obj_col = new ColaboradorModel();
            $updateEstado = $obj_col->update_Colaborador_Estado_xID($datesUpdate);
            if($updateEstado) {
                $val = 1;
                $message = "Estado actualizado satisfactoriamente.";
            }

            echo json_encode(array('status'=>$val,'message'=>$message));

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function delete_Colaborador_JSON(){
        try {
            $idColaborador = (int)$_POST['id'];
            $val = 0;
            $message = "Se produjo un error al intentar eliminar al Colaborador.";
            $obj_col = new ColaboradorModel();
            $delete = $obj_col->eliminar_Colaborador_xID($idColaborador);
            if ($delete) {
                $val = 1;
                $message = "Colaborador eliminado satisfactoriamente.";
            }

            echo json_encode(array('status'=>$val,'message'=>$message));

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function loadCampos_addPersonal(){
        try {
            $IdServicioUsuario = (int)$_GET['id'];
            $obj_serv = new ServicioModel();
            $dtlleSerUsuario = $obj_serv->detalle_ServicioUsuario_xIDSU($IdServicioUsuario);
            $idServicio = 0;
            if(!is_null($dtlleSerUsuario)){  $idServicio = $dtlleSerUsuario['id_serv']; }
            $obj_fn = new FuncionesModel();?>
            <div class="container">
                <h4 class="display-4 mb-5 font-weight-bold pt-20 text-center">
                    Registro de Personal
                </h4>
                <p class="mb-10 text-center">
                    Complete los campos descritos a continuación para seguir con el proceso:
                </p>
                <form id="guardarDatosIniciales_Personal" role="form">
                    <input type="hidden" name="idservtk" value="<?=$obj_fn->encrypt_decrypt('encrypt',$idServicio)?>">
                    <div class="card shadow">
                        <div class="card-header pb-0">
                            <a class="d-flex align-items-left justify-content-left nav-item nav-link active border-0">
                                <div class="d-flex">
                                    <div class="pl-xl-100 pl-lg-100">
                                <span class="d-block mb-5">
                                    <span class="display-4 text-primary">Sección 1</span>
                                </span>
                                        <span class="d-block">Datos generales</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-10 text-xl-left text-lg-left text-md-left text-sm-center">
                                Todos los campos descritos con <code>(*)</code>, son campos obligatorios.
                            </p>
                            <div id="divError"></div>
                            <div class="row">
                                <div class="col-xl-4 col-lg-4 col-md-5 col-sm-12 mb-10">
                                    <label for="ndoc_p" class="col-form-label mb-0 pb-0">
                                        Número de documento (DNI/CE) <span class="text-danger-400">*</span>
                                    </label>
                                    <input type="number" class="form-control border-input text-left" placeholder="nro. documento"
                                           name="ndoc_p" id="ndoc_p" required="required" autocomplete="off" maxlength="12"
                                           oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                           step="1" min="1" onkeydown="return event.keyCode !== 69">

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-4 col-lg-4 col-md-5 col-sm-12 mb-10">
                                    <label for="ndoc_p" class="col-form-label mb-0 pb-0">
                                       Apellido Paterno <span class="text-danger-400">*</span>
                                    </label>
                                    <input type="text" class="form-control border-input text-left" placeholder="ingrese valor"
                                           name="apa_p" required="required" autocomplete="off" maxlength="50">
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-5 col-sm-12 mb-0">
                                    <label for="ama_p" class="col-form-label mb-0 pb-0">
                                        Apellido Materno <span class="text-danger-400">*</span>
                                    </label>
                                    <input type="text" class="form-control border-input text-left" placeholder="ingrese valor"
                                           name="ama_p" required="required" autocomplete="off" maxlength="50">
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-5 col-sm-12 mb-0">
                                    <label for="name_p" class="col-form-label mb-0 pb-0">
                                        Nombres <span class="text-danger-400">*</span>
                                    </label>
                                    <input type="text" class="form-control border-input text-left" placeholder="ingrese valor"
                                           name="name_p" required="required" autocomplete="off" maxlength="50">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-10">
                                    <label for="cargo_p" class="col-form-label mb-0 pb-0">
                                        Cargo <span class="text-danger-400">*</span>
                                    </label>
                                    <input type="text" class="form-control border-input text-left" placeholder="ingrese valor"
                                           name="cargo_p" required="required" autocomplete="off" maxlength="50">
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-10">
                                    <label for="areaop_p" class="col-form-label mb-0 pb-0">
                                        Area operativa <span class="text-danger-400">*</span>
                                    </label>
                                    <input type="text" class="form-control border-input text-left" placeholder="ingrese valor"
                                           name="areaop_p" required="required" autocomplete="off" maxlength="50">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12" id="divFecha">
                                    <label for="fechacontrato_p" class="col-form-label mb-0 pb-0">
                                        Fecha inicio contrato<span class="text-danger-400">*</span>
                                    </label>
                                    <input type="text" class="form-control border-input text-left inputFecha" placeholder="--/--/----"
                                           name="fechacontrato_p" required="required" autocomplete="off" maxlength="10">
                                </div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-10">
                                    <label for="legajo_p" class="col-form-label mb-0 pb-0">
                                        Número de legajo <span class="text-danger-400">*</span>
                                    </label>
                                    <input type="number" class="form-control border-input text-left" placeholder="ingrese valor"
                                           name="legajo_p" required="required" autocomplete="off" maxlength="8"
                                           oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                           step="1" min="1" onkeydown="return event.keyCode !== 69">

                                </div>
                                <div class="col-xl-5 col-lg-5 col-md-5 col-sm-12 mb-10">
                                    <label for="email_p" class="col-form-label mb-0 pb-0">
                                        Correo Corporativo/Personal <span class="text-danger-400">*</span>
                                    </label>
                                    <input type="email" class="form-control border-input text-left" placeholder="---------@--------"
                                           name="email_p" required="required" autocomplete="off">

                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-xl-right text-lg-right text-md-right text-sm-center">
                            <a id="btnCancelPer" class="btn btn-outline-secondary btn-rounded btn-lg btn-hover-transform text-hover-white mr-10">
                                Cancelar
                            </a>
                            <button type="submit" class="btn bg-warning-400 text-white btn-lg btn-wth-icon icon-wthot-bg btn-rounded icon-right ">
                                <span class="icon-label position-left">
                                    <i class="ti-save"></i>
                                </span>
                                Grabar
                            </button>
                            <!--
                            <button type="submit" class="btn btn-outline-primary btn-wth-icon icon-wthot-bg btn-rounded icon-right btn-lg btn-hover-transform ml-15">
                                <span class="btn-text">Continuar</span>
                                <span class="icon-label">
                                    <i class="ti-arrow-right"></i>
                                </span>
                            </button>-->
                        </div>
                    </div>
                </form>
            </div>
            <?php
        }
        catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function registrar_datosInitial_JSON(){
        try {
            $obj_fn = new FuncionesModel();
            $nrodDocumento =  trim($_POST['ndoc_p']);
            $idServicio = $obj_fn->encrypt_decrypt("decrypt",$_POST['idservtk']);
            $val = 0;
            $message = "Se produjo un error al intentar registrar los datos del Personal.";
            $obj_col = new ColaboradorModel();
            $existeDoc = $obj_col->buscar_colaborador_xnDoc($nrodDocumento);
            $idColabTocken = null;
            if(is_null($existeDoc)){
                $obj_serv = new ServicioModel();
                $dtlleServicio = $obj_serv->detalle_Servicio_xID($idServicio);
                $nameService = "";
                if(!is_null($dtlleServicio)){ $nameService = $dtlleServicio['des_serv']; }

                $datesUpdate[0] = $idServicio;
                $datesUpdate[1] = trim($_POST['ndoc_p']);
                $datesUpdate[2] = trim($_POST['apa_p']);
                $datesUpdate[3] = trim($_POST['ama_p']);
                $datesUpdate[4] = trim($_POST['name_p']);
                $datesUpdate[5] = trim($_POST['cargo_p']);
                $datesUpdate[6] = $nameService;
                $datesUpdate[7] = trim($_POST['areaop_p']);
                $datesUpdate[8] = $obj_fn->fecha_ESP_ENG($_POST['fechacontrato_p']);
                $datesUpdate[9] = trim($_POST['legajo_p']);
                $datesUpdate[10]= trim($_POST['email_p']);
                $insertPersonal = $obj_col->insert_Colaborador_StepOne($datesUpdate);
                if($insertPersonal > 0) {
                    $val = 1;
                    $message = "Datos inciales registrados satisfactoriamente.";
                    $idColabTocken = $obj_fn->encrypt_decrypt("encrypt",$insertPersonal);
                }
            }
            else {
                $val = 2;
                $message = "El Personal ya se encuentra registrado con el documento <<".$nrodDocumento.">>";
            }

            echo json_encode(array('status'=>$val,'message'=>$message,'idcoltk'=>$idColabTocken));

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_datosPersonal_JSON(){
        try {
            $obj_fn = new FuncionesModel();
            $idColaborador =  (int)$_POST['idColaborador'];

            $nameService = "";
            if(isset($_POST['idserv_p'])) {
                $obj_serv = new ServicioModel();
                $dtlleServicio = $obj_serv->detalle_Servicio_xID($_POST['idserv_p']);

                if (!is_null($dtlleServicio)) {
                    $nameService = $dtlleServicio['des_serv'];
                }
            }

            $datesUpdate[0] = (int)$_POST['idserv_p'];
            $datesUpdate[1] = trim($_POST['ndoc_p']);
            $datesUpdate[2] = trim($_POST['apa_p']);
            $datesUpdate[3] = trim($_POST['ama_p']);
            $datesUpdate[4] = trim($_POST['name_p']);
            $datesUpdate[5] = trim($_POST['cargo_p']);
            $datesUpdate[6] = $nameService;
            $datesUpdate[7] = trim($_POST['areaop_p']);
            $datesUpdate[8] = $obj_fn->fecha_ESP_ENG($_POST['fechacontrato_p']);
            $datesUpdate[9] = trim($_POST['legajo_p']);
            $datesUpdate[10]= trim($_POST['email_p']);

            $val = 0;
            $message = "Error al Registrar/Actualizar al Personal ingresado";
            $obj_col = new ColaboradorModel();
            if($idColaborador == 0){
                //Nuevo registro
                $insert = $obj_col->insert_Colaborador($datesUpdate);
                if($insert) {
                    $val = 1;
                    $message = "Personal registrado satisfactoriamente.";
                }
            }
            else{//Actualizamos registro existente
                $dtlleCol = $obj_col->buscar_colaborador_xId($idColaborador);
                $datesUpdate[1] = $dtlleCol['ndoc_col'];
                $datesUpdate[11] = $idColaborador;
                $insert = $obj_col->update_Colaborador($datesUpdate);
                if($insert) {
                    $val = 2;
                    $message = "Personal actualizado satisfactoriamente.";
                }
            }

            echo json_encode(array('status'=>$val,'message'=>$message));

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function load_Seccion2_Personal(){
        try {
            $obj_fn = new FuncionesModel();
            $idColaborador = $obj_fn->encrypt_decrypt('decrypt',$_GET['id']);
            $obj_col = new ColaboradorModel();
            $dtlleColaborador = $obj_col->detalle_Colaborador_xId($idColaborador);?>
            <h4 class="display-4 mb-5 font-weight-bold pt-20">
                <a class="icon-label mr-10 cursor-pointer" id="btnCancelPer"><i class="fa fa-arrow-left" aria-hidden="true"></i> </a>
                Registro de Personal
            </h4>
            <P class="mb-10">
                Complete los campos descritos a continuación para seguir con el proceso:
            </P>
            <div class="card hk-dash-type-1 overflow-hide">
                <div class="card-header pa-0">
                    <div class="nav nav-tabs nav-light nav-justified" id="dash-tab" role="tablist">
                        <a class="d-flex align-items-center justify-content-center nav-item nav-link" id="dash-tab-1" data-toggle="tab" href="#nav-dash-1" role="tab" aria-selected="false">
                            <div class="d-flex">
                                <div>
                                    <span class="d-block mb-5">
                                        <span class="display-4 counter-anim">Sección 1</span>
                                    </span>
                                    <span class="d-block">Datos generales</span>
                                </div>
                            </div>
                        </a>
                        <a class="d-flex align-items-center justify-content-center nav-item nav-link active" id="dash-tab-2" data-toggle="tab" href="#nav-dash-2" role="tab" aria-selected="true">
                            <div class="d-flex">
                                <div>
                                    <span class="d-block mb-5">
                                        <span class="display-4 counter-anim">Sección 2</span>
                                    </span>
                                    <span class="d-block">Firma Electrónica</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade " id="nav-dash-1" role="tabpanel" aria-labelledby="dash-tab-1">
                    <div class="card card-body shadow">
                        <p class="text-muted mb-10 text-xl-left text-lg-left text-md-left text-sm-center">
                            Todos los campos descritos con <code>(*)</code>, son campos obligatorios.
                        </p>
                        <div class="divError"></div>
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-5 col-sm-12 mb-10">
                                <label for="ndoc_p" class="col-form-label mb-0 pb-0">
                                    Número de documento (DNI/CE) <span class="text-danger-400">*</span>
                                </label>
                                <input type="text" class="form-control border-input text-left" value="<?=$dtlleColaborador['ndoc_col']?>" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-5 col-sm-12 mb-10">
                                <label for="ndoc_p" class="col-form-label mb-0 pb-0">
                                    Apellido Paterno <span class="text-danger-400">*</span>
                                </label>
                                <input type="text" class="form-control border-input text-left" value="<?=$dtlleColaborador['apa_col']?>" readonly>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-5 col-sm-12 mb-0">
                                <label for="ama_p" class="col-form-label mb-0 pb-0">
                                    Apellido Materno <span class="text-danger-400">*</span>
                                </label>
                                <input type="text" class="form-control border-input text-left" value="<?=$dtlleColaborador['ama_col']?>" readonly>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-5 col-sm-12 mb-0">
                                <label for="name_p" class="col-form-label mb-0 pb-0">
                                    Nombres <span class="text-danger-400">*</span>
                                </label>
                                <input type="text" class="form-control border-input text-left" value="<?=$dtlleColaborador['nombres_col']?>" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-10">
                                <label for="cargo_p" class="col-form-label mb-0 pb-0">
                                    Cargo <span class="text-danger-400">*</span>
                                </label>
                                <input type="text" class="form-control border-input text-left" value="<?=$dtlleColaborador['cargo_col']?>" readonly>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-10">
                                <label for="areaop_p" class="col-form-label mb-0 pb-0">
                                    Area operativa <span class="text-danger-400">*</span>
                                </label>
                                <input type="text" class="form-control border-input text-left" value="<?=$dtlleColaborador['areaop_col']?>" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12" id="divFecha">
                                <label for="fechacontrato_p" class="col-form-label mb-0 pb-0">
                                    Fecha inicio contrato<span class="text-danger-400">*</span>
                                </label>
                                <input type="text" class="form-control border-input text-left" value="<?=$obj_fn->fecha_ENG_ESP($dtlleColaborador['fcontrato_col'])?>" readonly>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-10">
                                <label for="legajo_p" class="col-form-label mb-0 pb-0">
                                    Número de legajo <span class="text-danger-400">*</span>
                                </label>
                                <input type="text" class="form-control border-input text-left" value="<?=$dtlleColaborador['legajo_col']?>" readonly>
                            </div>
                            <div class="col-xl-5 col-lg-5 col-md-5 col-sm-12 mb-10">
                                <label for="email_p" class="col-form-label mb-0 pb-0">
                                    Correo Corporativo/Personal <span class="text-danger-400">*</span>
                                </label>
                                <input type="text" class="form-control border-input text-left" value="<?=$dtlleColaborador['email_col']?>" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade show active" id="nav-dash-2" role="tabpanel" aria-labelledby="dash-tab-2">
                    <form id="saveSignature_Personal" enctype="multipart/form-data">
                        <div class="card shadow">
                            <div class="card-body">
                                <p class="text-muted mb-10 text-xl-left text-lg-left text-md-left text-sm-center">
                                    Debe adjuntar la firma creada por el software <code>Evolis SIg10 Lite</code>, esta firma se encriptará para obtener mayor seguridad.
                                </p>
                                <input type="hidden" id="remoteserver" value="<?=$obj_fn->encrypt_decrypt('encrypt',$_SERVER['REMOTE_ADDR'])?>"/>
                                <input type="hidden" id="idcol_tk" value="<?=$_GET['id']?>"/>
                                <input type="file" class="file" id="file_signature" name="file_signature" required
                                       data-show-preview="true" data-show-upload="false"
                                       data-show-caption="true" data-show-remove="true"
                                       data-show-cancel="true"
                                       data-msg-placeholder="Seleccione un archivo..."
                                       data-allowed-file-extensions='["jpeg", "jpg"]'
                                       data-browse-Label='Examinar'
                                       data-remove-Label='Eliminar'
                                       data-upload-Label='Grabar y continuar'
                                       data-browse-class="btn btn-outline-secondary cursor-pointer"
                                       data-upload-class="btn btn-outline-primary cursor-pointer"
                                       data-remove-class="btn btn-outline-danger cursor-pointer">
                            </div>
                            <div class="card-footer text-xl-right text-lg-right text-md-right text-sm-center p-20">
                                <button type="submit" class="btn bg-warning-400 btn-hover-transform text-white btn-lg">Finalizar</button>
                                <!--
                                <button type="submit" class="btn btn-outline-primary btn-wth-icon icon-wthot-bg btn-lg btn-rounded icon-right btn-md btn-hover-transform">
                                    <span class="btn-text">Grabar y continuar</span>
                                    <span class="icon-label">
                                        <i class="ti-arrow-right"></i>
                                    </span>
                                </button>
                                -->
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <br>
            <br>
            <br>
            <?php
        }
        catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_datosFirmaElectronica_JSON(){
        try {
            date_default_timezone_set("America/Lima");
            setlocale(LC_TIME, 'es_PE.UTF-8');
            $val = 0;
            $message = "Error al realizar el registro de la firma digital";
            $idColaToken = 0;
            $datesUpdate = array();
            if(!empty($_FILES["file_signature"]['tmp_name'])) {
                $bin_string = file_get_contents($_FILES["file_signature"]["tmp_name"]);
                $hex_string = base64_encode($bin_string);
                $base64 = 'data:image/jpeg;base64,' . $hex_string;

                $obj_fn = new FuncionesModel();
                $datesUpdate[0] = $obj_fn->encrypt_decrypt("decrypt", $_POST['idcoltk']);
                $datesUpdate[1] = 1; // Action Firma
                $datesUpdate[2] = $obj_fn->encrypt_decrypt("decrypt", $_POST['remoteserver']);
                $datesUpdate[3] = trim($base64);
                $datesUpdate[4] = date("Y-m-d H:i:s");

                $obj_col = new ColaboradorModel();
                $updatePersonal = $obj_col->insert_Colaborador_StepTwo($datesUpdate);
                if ($updatePersonal) {
                    $val = 1;
                    $message = "Firma Digital registrada satisfactoriamente.";
                    $idColaToken = $_POST['idcol_tk'];
                }
            }
            echo json_encode(array('status'=>$val,'message'=>$message,'idtk'=>$idColaToken));

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function load_Seccion3_Personal(){
        try {
            $obj_fn = new FuncionesModel();
            $idColaborador = $obj_fn->encrypt_decrypt('decrypt',$_GET['id']);
            $obj_col = new ColaboradorModel();
            $dtlleColaborador = $obj_col->detalle_Colaborador_xId($idColaborador);?>
            <h4 class="display-4 mb-5 font-weight-bold pt-20">
                <a class="icon-label mr-10 cursor-pointer" id="btnCancelPer"><i class="fa fa-arrow-left" aria-hidden="true"></i> </a>
                Registro de Personal
            </h4>
            <P class="mb-10">
                Complete los campos descritos a continuación para seguir con el proceso:
            </P>
            <div class="card hk-dash-type-1 overflow-hide">
                <div class="card-header pa-0">
                    <div class="nav nav-tabs nav-light nav-justified" id="dash-tab" role="tablist">
                        <a class="d-flex align-items-center justify-content-center nav-item nav-link" id="dash-tab-1" data-toggle="tab" href="#nav-dash-1" role="tab" aria-selected="false">
                            <div class="d-flex">
                                <div>
                                    <span class="d-block mb-5">
                                        <span class="display-4 counter-anim">Sección 1</span>
                                    </span>
                                    <span class="d-block">Datos generales</span>
                                </div>
                            </div>
                        </a>
                        <a class="d-flex align-items-center justify-content-center nav-item nav-link" id="dash-tab-2" data-toggle="tab" href="#nav-dash-2" role="tab" aria-selected="true">
                            <div class="d-flex">
                                <div>
                                    <span class="d-block mb-5">
                                        <span class="display-4 counter-anim">Sección 2</span>
                                    </span>
                                    <span class="d-block">Firma Electrónica</span>
                                </div>
                            </div>
                        </a>
                        <a class="d-flex align-items-center justify-content-center nav-item nav-link active" id="dash-tab-3" data-toggle="tab" href="#nav-dash-3" role="tab" aria-controls="nav-dash-3" aria-selected="false">
                            <div class="d-flex">
                                <div>
                                    <span class="d-block mb-5">
                                        <span class="display-4 counter-anim">Sección 3</span>
                                    </span>
                                    <span class="d-block">Registro Biométrico</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade" id="nav-dash-1" role="tabpanel" aria-labelledby="dash-tab-1">
                    <input type="hidden" id="completeSeccion1" value="1"/>
                    <div class="card card-body shadow">
                        <p class="text-muted mb-10 text-xl-left text-lg-left text-md-left text-sm-center">
                            Todos los campos descritos con <code>(*)</code>, son campos obligatorios.
                        </p>
                        <div class="divError"></div>
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-5 col-sm-12 mb-10">
                                <label for="ndoc_p" class="col-form-label mb-0 pb-0">
                                    Número de documento (DNI/CE) <span class="text-danger-400">*</span>
                                </label>
                                <input type="text" class="form-control border-input text-left" value="<?=$dtlleColaborador['ndoc_col']?>" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-5 col-sm-12 mb-10">
                                <label for="ndoc_p" class="col-form-label mb-0 pb-0">
                                    Apellido Paterno <span class="text-danger-400">*</span>
                                </label>
                                <input type="text" class="form-control border-input text-left" value="<?=$dtlleColaborador['apa_col']?>" readonly>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-5 col-sm-12 mb-0">
                                <label for="ama_p" class="col-form-label mb-0 pb-0">
                                    Apellido Materno <span class="text-danger-400">*</span>
                                </label>
                                <input type="text" class="form-control border-input text-left" value="<?=$dtlleColaborador['ama_col']?>" readonly>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-5 col-sm-12 mb-0">
                                <label for="name_p" class="col-form-label mb-0 pb-0">
                                    Nombres <span class="text-danger-400">*</span>
                                </label>
                                <input type="text" class="form-control border-input text-left" value="<?=$dtlleColaborador['nombres_col']?>" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-10">
                                <label for="cargo_p" class="col-form-label mb-0 pb-0">
                                    Cargo <span class="text-danger-400">*</span>
                                </label>
                                <input type="text" class="form-control border-input text-left" value="<?=$dtlleColaborador['cargo_col']?>" readonly>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-10">
                                <label for="areaop_p" class="col-form-label mb-0 pb-0">
                                    Area operativa <span class="text-danger-400">*</span>
                                </label>
                                <input type="text" class="form-control border-input text-left" value="<?=$dtlleColaborador['areaop_col']?>" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12" id="divFecha">
                                <label for="fechacontrato_p" class="col-form-label mb-0 pb-0">
                                    Fecha inicio contrato<span class="text-danger-400">*</span>
                                </label>
                                <input type="text" class="form-control border-input text-left" value="<?=$obj_fn->fecha_ENG_ESP($dtlleColaborador['fcontrato_col'])?>" readonly>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-10">
                                <label for="legajo_p" class="col-form-label mb-0 pb-0">
                                    Número de legajo <span class="text-danger-400">*</span>
                                </label>
                                <input type="text" class="form-control border-input text-left" value="<?=$dtlleColaborador['legajo_col']?>" readonly>
                            </div>
                            <div class="col-xl-5 col-lg-5 col-md-5 col-sm-12 mb-10">
                                <label for="email_p" class="col-form-label mb-0 pb-0">
                                    Correo Corporativo/Personal <span class="text-danger-400">*</span>
                                </label>
                                <input type="text" class="form-control border-input text-left" value="<?=$dtlleColaborador['email_col']?>" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="nav-dash-2" role="tabpanel" aria-labelledby="dash-tab-2">
                    <input type="hidden" id="completeSeccion2" value="1"/>
                    <div id="signature-pad" class="signature-pad" style="height: 500px">
                        <div class="signature-pad--body">
                            <img style="display:block; width:100%;height:100%;"
                                 src="<?=$dtlleColaborador['imgsign_col']?>" />
                        </div>
                        <div class="signature-pad--footer">
                            <div class="description text-muted">firmar registrada</div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade show active" id="nav-dash-3" role="tabpanel" aria-labelledby="dash-tab-3">
                    <div class="card card-shadow">
                        <div class="card-body">
                            <?php
                            $completeBiometria = 0;
                            ?>
                            <input type="hidden" id="completeSeccion3" value="<?=$completeBiometria?>"/>

                            <form id="actualizarDatosComplementarios_Tarjeta" role="form">
                                <input type="hidden" name="remoteserver" value="<?=$obj_fn->encrypt_decrypt('encrypt',$_SERVER['REMOTE_ADDR'])?>"/>
                                <input type="hidden" name="idcol_tk" value="<?=$_GET['id']?>"/>

                                <p class="mb-20 text-justify">
                                    Todos los campos descritos con <code>(*)</code>, son campos obligatorios.
                                </p>
                            </form>
                        </div>
                        <div class="card-footer p-20">
                            <div class="row">
                                <div class="col-12 text-xlg-right text-lg-right text-md-right text-sm-center">
                                    <button type="button" class="btn btn-link mr-25" id="btnCancelPer">Cancelar</button>
                                    <button type="submit" class="btn bg-warning-400 btn-hover-transform text-white btn-lg" id="btnSave-seccion3">Grabar cambios</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <br>
            <br>
            <?php
        }
        catch (PDOException $e) {
            throw $e;
        }
    }

    public function loadCampos_editPersonal(){
        try {
            $idColaborador = $_GET['id'];
            $obj_col = new ColaboradorModel();
            $dtlleColaborador = $obj_col->detalle_Colaborador_xId($idColaborador);?>
            <h4 class="display-4 mb-5 font-weight-bold pt-20 text-center">
                Actualizar datos del Personal
            </h4>
            <P class="mb-10 text-center">
                Modifique los campos descritos a continuación para el personal:
            </P>
             <form id="guardarDatosPersonal" role="form">
                 <input type="hidden" name="idcol" value="<?=$idColaborador?>">
                 <div class="card shadow">
                     <div class="card-header pb-0">
                         <a class="d-flex align-items-left justify-content-left nav-item nav-link active border-0">
                             <div class="d-flex">
                                 <div class="pl-xl-100 pl-lg-100">
                                <span class="d-block mb-5">
                                    <span class="display-4 text-primary">Sección 1</span>
                                </span>
                                     <span class="d-block">Datos generales</span>
                                 </div>
                             </div>
                         </a>
                     </div>
                     <div class="card-body">
                         <p class="text-muted mb-10 text-xl-left text-lg-left text-md-left text-sm-center">
                             Todos los campos descritos con <code>(*)</code>, son campos obligatorios.
                         </p>
                         <div id="divError"></div>
                         <div class="row">
                             <div class="col-xl-4 col-lg-4 col-md-5 col-sm-12 mb-10">
                                 <label for="ndoc_p" class="col-form-label mb-0 pb-0">
                                     Número de documento (DNI/CE) <span class="text-danger-400">*</span>
                                 </label>
                                 <input type="number" class="form-control border-input text-left" placeholder="nro. documento"
                                        name="ndoc_p" id="ndoc_p" required="required" autocomplete="off" maxlength="12"
                                        oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                        step="1" min="1" onkeydown="return event.keyCode !== 69" value="<?=$dtlleColaborador['ndoc_col']?>">
                             </div>
                         </div>
                         <div class="row">
                             <div class="col-xl-4 col-lg-4 col-md-5 col-sm-12 mb-10">
                                 <label for="apa_p" class="col-form-label mb-0 pb-0">
                                     Apellido Paterno <span class="text-danger-400">*</span>
                                 </label>
                                 <input type="text" class="form-control border-input text-left" placeholder="ingrese valor"
                                        name="apa_p" required="required" autocomplete="off" maxlength="50" value="<?=$dtlleColaborador['apa_col']?>">
                             </div>
                             <div class="col-xl-4 col-lg-4 col-md-5 col-sm-12 mb-0">
                                 <label for="ama_p" class="col-form-label mb-0 pb-0">
                                     Apellido Materno <span class="text-danger-400">*</span>
                                 </label>
                                 <input type="text" class="form-control border-input text-left" placeholder="ingrese valor"
                                        name="ama_p" required="required" autocomplete="off" maxlength="50" value="<?=$dtlleColaborador['ama_col']?>">
                             </div>
                             <div class="col-xl-4 col-lg-4 col-md-5 col-sm-12 mb-0">
                                 <label for="name_p" class="col-form-label mb-0 pb-0">
                                     Nombres <span class="text-danger-400">*</span>
                                 </label>
                                 <input type="text" class="form-control border-input text-left" placeholder="ingrese valor"
                                        name="name_p" required="required" autocomplete="off" maxlength="50" value="<?=$dtlleColaborador['nombres_col']?>">
                             </div>
                         </div>
                         <div class="row">
                             <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-10">
                                 <label for="cargo_p" class="col-form-label mb-0 pb-0">
                                     Cargo <span class="text-danger-400">*</span>
                                 </label>
                                 <input type="text" class="form-control border-input text-left" placeholder="ingrese valor"
                                        name="cargo_p" required="required" autocomplete="off" maxlength="50" value="<?=$dtlleColaborador['cargo_col']?>">
                             </div>
                             <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-10">
                                 <label for="areaop_p" class="col-form-label mb-0 pb-0">
                                     Area operativa <span class="text-danger-400">*</span>
                                 </label>
                                 <input type="text" class="form-control border-input text-left" placeholder="ingrese valor"
                                        name="areaop_p" required="required" autocomplete="off" maxlength="50" value="<?=$dtlleColaborador['areaop_col']?>">
                             </div>
                         </div>
                         <div class="row">
                             <div class="col-xl-5 col-lg-5 col-md-5 col-sm-12 mb-10">
                                 <label for="email_p" class="col-form-label mb-0 pb-0">
                                     Correo Corporativo/Personal <span class="text-danger-400">*</span>
                                 </label>
                                 <input type="email" class="form-control border-input text-left" placeholder="---------@--------"
                                        name="email_p" required="required" autocomplete="off" value="<?=$dtlleColaborador['email_col']?>">
                             </div>
                         </div>
                     </div>
                     <div class="card-footer text-xl-right text-lg-right text-md-right text-sm-center">
                         <a id="btnCancelPer" class="btn btn-outline-secondary btn-rounded btn-lg btn-hover-transform text-hover-white mr-10">
                             Cancelar
                         </a>
                         <button type="submit" class="btn bg-warning-400 text-white btn-lg btn-wth-icon icon-wthot-bg btn-rounded icon-right ">
                                <span class="icon-label position-left">
                                    <i class="ti-save"></i>
                                </span>
                             Actualizar
                         </button>
                     </div>
                 </div>
             </form>
            <br>
            <br>
            <?php
        }
        catch (PDOException $e) {
            throw $e;
        }
    }

    public function actualizar_datosPersonal_JSON(){
        try {
            $idColaborador =  trim($_POST['idcol']);
            $val = 0;
            $message = "Se produjo un error al intentar actualizar los datos del Personal.";
            $obj_col = new ColaboradorModel();
            $datesUpdate[0] = $idColaborador;
            $datesUpdate[1] = trim($_POST['ndoc_p']);
            $datesUpdate[2] = trim($_POST['apa_p']);
            $datesUpdate[3] = trim($_POST['ama_p']);
            $datesUpdate[4] = trim($_POST['name_p']);
            $datesUpdate[5] = trim($_POST['cargo_p']);
            $datesUpdate[6] = trim($_POST['areaop_p']);
            $datesUpdate[7] = trim($_POST['email_p']);
            $updatePersonal = $obj_col->actualizar_Colaborador($datesUpdate);
            if($updatePersonal) {
                $val = 1;
                $message = "Datos del personal actualizado satisfactoriamente.";
            }

            echo json_encode(array('status'=>$val,'message'=>$message));

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function loadCampos_importColaborador(){
        try {
            $obj_ge = new GerenciaModel();
            $lstGerencias = $obj_ge->lst_Gerencia_Activas();
            ?>
            <div class="container">
                <div class="page-title">
                    <h4 class="mb-0 text-info text-center">
                        Cargar/Actualizar Nuevo Personal
                    </h4>
                    <ol class="breadcrumb mb-0 pl-0 pt-1 pb-0 text-center" style="display: block">
                        <li class="breadcrumb-item text-muted">Registre uno o varios colaboradores del almacén seleccionado, primero descargue la plantilla, copie los datos indicas en la plantilla a cargar y listo.</li>
                    </ol>
                </div>
                <div class="card shadow">
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <form id="fmrLoad_DataPersonal" enctype="multipart/form-data">
                                    <div class="form-group row">
                                        <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12">
                                            <select name="idServicio" id="idServicio"  class="form-control input-md selectSearch" data-placeholder="Servicio/Sede..." required>
                                                <option></option>
                                                <?php
                                                if (!is_null($lstGerencias)) {
                                                    $obj_serv = new ServicioModel();
                                                    foreach ($lstGerencias as $gerencia) {
                                                        $lstServicios = $obj_serv->lst_Servicio_Activos_xGerencia_All($gerencia['id_ge']);
                                                        if (!is_null($lstServicios)) {?>
                                                            <optgroup label="<?= $gerencia['des_ge'] ?>">
                                                                <?php
                                                                foreach ($lstServicios as $servicio) {?>
                                                                        <option value="<?= $servicio['id_serv'] ?>">
                                                                            <?= $servicio['des_serv'] ?>
                                                                        </option>
                                                                        <?php
                                                                } ?>
                                                            </optgroup>
                                                            <?php
                                                        }
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <input type="file" class="file" id="file_datacol" name="file_datacol" required
                                           data-show-preview="false" data-show-upload="true"
                                           data-show-caption="true" data-show-remove="true"
                                           data-show-cancel="false"
                                           data-msg-placeholder="Seleccione un archivo..."
                                           data-allowed-file-extensions='["xls", "xlsx"]'
                                           data-browse-Label='Examinar'
                                           data-remove-Label='Eliminar'
                                           data-upload-Label='Enviar'
                                           data-browse-class="btn waves-effect waves-light btn-outline-secondary cursor-pointer"
                                           data-upload-class="btn waves-effect waves-light btn-outline-info cursor-pointer"
                                           data-remove-class="btn waves-effect waves-light btn-outline-danger cursor-pointer">
                                </form>
                                <span class="help-block mb-0">
                                Formatos permitidos [<code>xls, xlsx</code>]
                            </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <button type="button" id="btnCancelCol_Load" class="btn btn-light mr-10 btnDisabledc">
                            Cancelar
                        </button>
                        <a type="button" class="btn bg-green-600 btn-md btn-hover-transform text-hover-white btnDisabledc"
                           href="../assets/formato/Plantilla-personal.xlsx">
                            <b><i class="icon-download4"></i></b>
                            Descargar Plantilla
                        </a>
                    </div>
                </div>
                <br><br><br>
            </div>
            <?php
        }
        catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function load_File_Personal(){
        try {
            date_default_timezone_set("America/Lima");
            setlocale(LC_TIME, 'es_PE.UTF-8');
            $idServicio = (int)$_POST['idServicio'];
            $obj_serv = new ServicioModel();
            $detailService = $obj_serv->detalle_Servicio_xID($idServicio);

            $readerType = null;
            $type = 0; // error definido
            $mensaje = "El archivo adjunto no tiene un formato valido.";
            $datosError = array();
            $successLoad = 0;
            $datosArray = array();
            $datosRegister = array();
            $archivo = "";
            
            // CUSTOM
            $exist = [];
            // END CUSTOM

            if(!is_null($detailService)) {
                $desServicio = mb_strtoupper(trim($detailService['des_serv']), "UTF-8");

                $filename = $_FILES['file_datacol']['tmp_name'];
                $array = explode('.', $_FILES['file_datacol']['name']);
                $extension = end($array);

                if (trim($extension) == 'xlsx') {$readerType = 'Xlsx'; }
                else if (trim($extension) == 'xls') { $readerType = 'Xls';}


                if (!is_null($readerType)) {
                    $reader = IOFactory::createReader($readerType);
                    $spreadsheet = $reader->load($filename);
                    $sheetCount = $spreadsheet->getSheetCount();
                    date_default_timezone_set('America/New_York');

                    if ($sheetCount == 1 || $sheetCount == 2) {
                        $worksheet = $spreadsheet->setActiveSheetIndex(0);
                        $data = $worksheet->toArray();
                        $arreglo = array();
                        for ($row = 1; $row <= sizeof($data); $row++) {
                            unset($arreglo);
                            $columnEmpty = 0;
                            for ($t = 0; $t <= 6; $t++) {
                                if (trim($data[$row][$t]) != null || trim($data[$row][$t]) != "") {
                                    $arreglo[] = trim($data[$row][$t]);
                                } else {
                                    $arreglo[] = "";
                                    $columnEmpty++;
                                }
                            }

                            //verificamos cuantos valores nulos tiene
                            if ($columnEmpty == 0) {
                                array_push($datosArray, $arreglo);
                            }
                        }
                    }
                    else if ($sheetCount > 2) {
                        $type = 0; // archivo con muchas hojas
                        $mensaje = "El archivo adjunto contiene varias hojas adjuntas, solo se admite el formato según la plantilla requerida.";
                    }
                }

                if (sizeof($datosArray) > 0) {
                    $obj_col = new ColaboradorModel();
                    for ($i = 0; $i < sizeof($datosArray); $i++) {

                        $datesSearching[0] = $idServicio;
                        $datesSearching[1] = $datosArray[$i][0];
                        // CUSTOM
                        $detailPersonal = $obj_col->buscar_colaborador_xnDoc($datesSearching[1]);
                        // END CUSTOM
                        // $detailPersonal = $obj_col->buscar_colaborador_xServicio($datesSearching);
                        if (is_null($detailPersonal)) {
                            $datesReg[0] = $idServicio;
                            $datesReg[1] = trim($datosArray[$i][0]);
                            $datesReg[2] = trim($datosArray[$i][1]);
                            $datesReg[3] = trim($datosArray[$i][2]);
                            $datesReg[4] = trim($datosArray[$i][3]);
                            $datesReg[5] = trim($datosArray[$i][4]);
                            $datesReg[6] = trim($desServicio);
                            $datesReg[7] = trim($datosArray[$i][5]);
                            $datesReg[8] = trim($datosArray[$i][6]);
                            array_push($datosRegister, $datesReg);
                            $insertCol = $obj_col->registrar_Colaborador($datesReg);
                            if ($insertCol) {
                                $successLoad++;
                            }
                        } else {
                            // CUSTOM
                            array_push($exist, $datesReg);
                            // END CUSTOM
                            array_push($datosError, $datosArray[$i]);
                        }
                    }

                    if ($successLoad > 0 && sizeof($datosError) == 0) {
                        $type = 1; //correcto
                        $mensaje = "Personal cargado correctamente.";
                    } else if ($successLoad > 0 && sizeof($datosError) > 0) {
                        $type = 2; //archivo vacio
                        $mensaje = " Personal cargado correctamente, pero con algunos errores/registro existentes.";
                    } else if ($successLoad == 0 && sizeof($datosError) > 0) {
                        $type = 3; //archivo vacio
                        $mensaje = "Error al cargar el Personal, verifique el contenido de los datos duplicados.";
                    }
                }

                if (sizeof($datosError) > 0) {
                    //generamos el excel a descargar de errores

                    $sty_title = [
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER
                        ],
                        'font' => [
                            'bold' => true,
                            'italic' => false,
                            'underline' => false,
                            'strikethrough' => false,
                            'color' => ['argb' => 'FFFFFF'],
                            'name' => "calibri",
                            'size' => 11
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'color' => ['argb' => '548235']
                        ],
                        'borders' => [
                            'top' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => '000000']
                            ],
                            'bottom' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => '000000']
                            ],
                            'left' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => '000000']
                            ],
                            'right' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => '000000']
                            ]
                        ]
                    ];
                    $sty_text_center = [
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER
                        ],
                        'font' => [
                            'bold' => false,
                            'italic' => false,
                            'underline' => false,
                            'strikethrough' => false,
                            'color' => ['argb' => '000000'],
                            'name' => "calibri",
                            'size' => 11
                        ],
                        'borders' => [
                            'top' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => '000000']
                            ],
                            'bottom' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => '000000']
                            ],
                            'left' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => '000000']
                            ],
                            'right' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => '000000']
                            ]
                        ]
                    ];
                    $sty_text_left = [
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_LEFT,
                            'vertical' => Alignment::VERTICAL_CENTER
                        ],
                        'font' => [
                            'bold' => false,
                            'italic' => false,
                            'underline' => false,
                            'strikethrough' => false,
                            'color' => ['argb' => '000000'],
                            'name' => "calibri",
                            'size' => 11
                        ],
                        'borders' => [
                            'top' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => '000000']
                            ],
                            'bottom' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => '000000']
                            ],
                            'left' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => '000000']
                            ],
                            'right' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => '000000']
                            ]
                        ]
                    ];

                    $letras = array(
                        65 => 'A', 66 => 'B', 67 => 'C', 68 => 'D', 69 => 'E', 70 => 'F', 71 => 'G', 72 => 'H',
                        73 => 'I', 74 => 'J', 75 => 'K', 76 => 'L', 77 => 'M', 78 => 'N', 79 => 'O', 80 => 'P',
                        81 => 'Q', 82 => 'R', 83 => 'S', 84 => 'T', 85 => 'U', 86 => 'V', 87 => 'W', 88 => 'X',
                        89 => 'Y', 90 => 'Z',
                    );
                    $titulos = array(
                        0 => "NRO DOCUMENTO\n(DNI/CEX)",
                        1 => "APELLIDO PATERNO",
                        2 => "APELLIDO MATERNO",
                        3 => "NOMBRES",
                        4 => "CARGO",
                        5 => "AREA OPERATIVA",
                        6 => "CORREO CORPORATIVO/PERSONAL"
                    );

                    //creamos el libro de trabajo
                    $spreadsheet = new Spreadsheet();


                    $spreadsheet->createSheet(0);
                    $hoja1 = new Worksheet($spreadsheet, 'Personal');
                    $spreadsheet->addSheet($hoja1, 0);
                    $sheet0 = $spreadsheet->setActiveSheetIndex(0);

                    for ($col = 0; $col < sizeof($titulos); $col++) {
                        $sheet0->setCellValue($letras[$col + 65] . '1', $titulos[$col]);
                        $sheet0->getStyle($letras[$col + 65] . '1')->applyFromArray($sty_title);
                        $sheet0->getStyle($letras[$col + 65] . '1')->getAlignment()->setWrapText(true);
                    }

                    //Writer error item
                    $lineError = 2;
                    for ($i = 0; $i < sizeof($datosError); $i++) {
                        for ($j = 0; $j <= sizeof($datosError[$i]) - 1; $j++) {
                            $sheet0->setCellValue($letras[65 + $j] . $lineError, trim($datosError[$i][$j]));
                            $sheet0->getStyle($letras[65 + $j] . $lineError)->applyFromArray($sty_text_center);
                        }
                        $sheet0->getRowDimension($lineError)->setRowHeight(15);
                        $lineError++;
                    }

                    $sheet0->getColumnDimension('A')->setWidth(21);
                    $sheet0->getColumnDimension('B')->setWidth(18);
                    $sheet0->getColumnDimension('C')->setWidth(18);
                    $sheet0->getColumnDimension('D')->setWidth(18);
                    $sheet0->getColumnDimension('E')->setWidth(18);
                    $sheet0->getColumnDimension('F')->setWidth(16);
                    $sheet0->getColumnDimension('g')->setWidth(30);
                    $sheet0->getSheetView()->setZoomScale(100);


                    $spreadsheet->setActiveSheetIndex(0);
                    $sheetIndex1 = $spreadsheet->getIndex($spreadsheet->getSheetByName('Worksheet 1'));
                    $spreadsheet->removeSheetByIndex($sheetIndex1);
                    $sheetIndex = $spreadsheet->getIndex($spreadsheet->getSheetByName('Worksheet'));
                    $spreadsheet->removeSheetByIndex($sheetIndex);
                    $ruta = "../assets/error-file/";
                    $archivo = md5("fileError-" . $idServicio . date("d-m-Y H:i:s")) . ".xlsx";

                    $writer = new Xlsx($spreadsheet);
                    $writer->save($ruta . $archivo);
                }
            }

            $response = array(
                'status'=> $type,
                'message'=> $mensaje,
                'dataError'=> $datosError,
                'succesLoad'=>$successLoad,
                'file'=>$archivo,
                'readerType'=>$readerType,
                'datarray'=>$datosArray,
                'exist' => $exist
            );

            echo json_encode($response);

        }
        catch (PDOException $e) {
            throw $e;
        }
        catch (Exception $e) {
        }
    }

    public function load_Seccion2_Personal_Anterior(){
        try {
            $obj_fn = new FuncionesModel();
            $idColaborador = $obj_fn->encrypt_decrypt('decrypt',$_GET['id']);
            $obj_col = new ColaboradorModel();
            $dtlleColaborador = $obj_col->detalle_Colaborador_xId($idColaborador);?>
            <h4 class="display-4 mb-5 font-weight-bold pt-20">
                <a class="icon-label mr-10 cursor-pointer" id="btnCancelPer"><i class="fa fa-arrow-left" aria-hidden="true"></i> </a>
                Registro de Personal
            </h4>
            <P class="mb-10">
                Complete los campos descritos a continuación para seguir con el proceso:
            </P>
            <div class="card hk-dash-type-1 overflow-hide">
                <div class="card-header pa-0">
                    <div class="nav nav-tabs nav-light nav-justified" id="dash-tab" role="tablist">
                        <a class="d-flex align-items-center justify-content-center nav-item nav-link" id="dash-tab-1" data-toggle="tab" href="#nav-dash-1" role="tab" aria-selected="false">
                            <div class="d-flex">
                                <div>
                                    <span class="d-block mb-5">
                                        <span class="display-4 counter-anim">Sección 1</span>
                                    </span>
                                    <span class="d-block">Datos generales</span>
                                </div>
                            </div>
                        </a>
                        <a class="d-flex align-items-center justify-content-center nav-item nav-link active" id="dash-tab-2" data-toggle="tab" href="#nav-dash-2" role="tab" aria-selected="true">
                            <div class="d-flex">
                                <div>
                                    <span class="d-block mb-5">
                                        <span class="display-4 counter-anim">Sección 2</span>
                                    </span>
                                    <span class="d-block">Firma Electrónica</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade " id="nav-dash-1" role="tabpanel" aria-labelledby="dash-tab-1">
                    <div class="card card-body shadow">
                        <p class="text-muted mb-10 text-xl-left text-lg-left text-md-left text-sm-center">
                            Todos los campos descritos con <code>(*)</code>, son campos obligatorios.
                        </p>
                        <div class="divError"></div>
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-5 col-sm-12 mb-10">
                                <label for="ndoc_p" class="col-form-label mb-0 pb-0">
                                    Número de documento (DNI/CE) <span class="text-danger-400">*</span>
                                </label>
                                <input type="text" class="form-control border-input text-left" value="<?=$dtlleColaborador['ndoc_col']?>" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-5 col-sm-12 mb-10">
                                <label for="ndoc_p" class="col-form-label mb-0 pb-0">
                                    Apellido Paterno <span class="text-danger-400">*</span>
                                </label>
                                <input type="text" class="form-control border-input text-left" value="<?=$dtlleColaborador['apa_col']?>" readonly>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-5 col-sm-12 mb-0">
                                <label for="ama_p" class="col-form-label mb-0 pb-0">
                                    Apellido Materno <span class="text-danger-400">*</span>
                                </label>
                                <input type="text" class="form-control border-input text-left" value="<?=$dtlleColaborador['ama_col']?>" readonly>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-5 col-sm-12 mb-0">
                                <label for="name_p" class="col-form-label mb-0 pb-0">
                                    Nombres <span class="text-danger-400">*</span>
                                </label>
                                <input type="text" class="form-control border-input text-left" value="<?=$dtlleColaborador['nombres_col']?>" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-10">
                                <label for="cargo_p" class="col-form-label mb-0 pb-0">
                                    Cargo <span class="text-danger-400">*</span>
                                </label>
                                <input type="text" class="form-control border-input text-left" value="<?=$dtlleColaborador['cargo_col']?>" readonly>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-10">
                                <label for="areaop_p" class="col-form-label mb-0 pb-0">
                                    Area operativa <span class="text-danger-400">*</span>
                                </label>
                                <input type="text" class="form-control border-input text-left" value="<?=$dtlleColaborador['areaop_col']?>" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12" id="divFecha">
                                <label for="fechacontrato_p" class="col-form-label mb-0 pb-0">
                                    Fecha inicio contrato<span class="text-danger-400">*</span>
                                </label>
                                <input type="text" class="form-control border-input text-left" value="<?=$obj_fn->fecha_ENG_ESP($dtlleColaborador['fcontrato_col'])?>" readonly>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-10">
                                <label for="legajo_p" class="col-form-label mb-0 pb-0">
                                    Número de legajo <span class="text-danger-400">*</span>
                                </label>
                                <input type="text" class="form-control border-input text-left" value="<?=$dtlleColaborador['legajo_col']?>" readonly>
                            </div>
                            <div class="col-xl-5 col-lg-5 col-md-5 col-sm-12 mb-10">
                                <label for="email_p" class="col-form-label mb-0 pb-0">
                                    Correo Corporativo/Personal <span class="text-danger-400">*</span>
                                </label>
                                <input type="text" class="form-control border-input text-left" value="<?=$dtlleColaborador['email_col']?>" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade show active" id="nav-dash-2" role="tabpanel" aria-labelledby="dash-tab-2">
                    <div class="card card-body shadow">
                        <p class="text-muted mb-10 text-xl-left text-lg-left text-md-left text-sm-center">
                            Debe adjuntar la firma creada por el software <code>Evolis SIg10 Lite</code>, esta firma se encriptará para obtener mayor seguridad.
                        </p>
                        <div class="divError"></div>
                        <form id="saveSignature_Personal" enctype="multipart/form-data">
                            <input type="file" class="file" id="file_signature" name="file_signature" required
                                   data-show-preview="true" data-show-upload="false"
                                   data-show-caption="true" data-show-remove="true"
                                   data-show-cancel="true"
                                   data-msg-placeholder="Seleccione un archivo..."
                                   data-allowed-file-extensions='["jpeg", "jpg"]'
                                   data-browse-Label='Examinar'
                                   data-remove-Label='Eliminar'
                                   data-upload-Label='Enviar'
                                   data-browse-class="btn waves-effect waves-light btn-outline-secondary cursor-pointer"
                                   data-upload-class="btn waves-effect waves-light btn-outline-info cursor-pointer"
                                   data-remove-class="btn waves-effect waves-light btn-outline-danger cursor-pointer">
                        </form>

                    </div>
                    <form id="saveSignature_Personal" role="form">
                        <input type="hidden" name="remoteserver" value="<?=$obj_fn->encrypt_decrypt('encrypt',$_SERVER['REMOTE_ADDR'])?>"/>
                        <input type="hidden" name="idcol_tk" value="<?=$_GET['id']?>"/>
                        <div id="signature-pad" class="signature-pad" style="height: 500px">
                            <div class="signature-pad--body" id="sig"></div>
                            <textarea id="signature64" name="signed" style="display:none"></textarea>
                            <div class="signature-pad--footer">
                                <div class="description text-muted">firmar arriba</div>
                                <div class="signature-pad--actions">
                                    <div>
                                        <button type="button" class="btn btn-outline-secondary" id="btnClearSignature">Borrar</button>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-outline-primary btn-wth-icon icon-wthot-bg btn-rounded icon-right btn-md btn-hover-transform">
                                            <span class="btn-text">Grabar y continuar</span>
                                            <span class="icon-label">
                                                    <i class="ti-arrow-right"></i>
                                                </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <br>
                    <br>
                    <br>
                </div>
            </div>
            <?php
        }
        catch (PDOException $e) {
            throw $e;
        }
    }//Ya no se usa

    public function loadCampos_editPersonal_Next(){
        try {
            $obj_fn = new FuncionesModel();
            $idColaborador = $_GET['id'];
            $obj_col = new ColaboradorModel();
            $dtlleColaborador = $obj_col->detalle_Colaborador_xId($idColaborador);?>
            <h4 class="display-4 mb-5 font-weight-bold pt-20">
                <a class="icon-label mr-10 cursor-pointer" id="btnCancelPer"><i class="fa fa-arrow-left" aria-hidden="true"></i> </a>
                Actualizar datos del Personal
            </h4>
            <P class="mb-10">
                Complete los campos descritos a continuación para seguir con el proceso:
            </P>
            <div class="card hk-dash-type-1 overflow-hide">
                <div class="card-header pa-0">
                    <div class="nav nav-tabs nav-light nav-justified" id="dash-tab" role="tablist">
                        <a class="d-flex align-items-center justify-content-center nav-item nav-link active" id="dash-tab-1" data-toggle="tab" href="#nav-dash-1" role="tab" aria-selected="false">
                            <div class="d-flex">
                                <div>
                                    <span class="d-block mb-5">
                                        <span class="display-4 counter-anim">Sección 1</span>
                                    </span>
                                    <span class="d-block">Datos generales</span>
                                </div>
                            </div>
                        </a>
                        <a class="d-flex align-items-center justify-content-center nav-item nav-link" id="dash-tab-2" data-toggle="tab" href="#nav-dash-2" role="tab" aria-selected="true">
                            <div class="d-flex">
                                <div>
                                    <span class="d-block mb-5">
                                        <span class="display-4 counter-anim">Sección 2</span>
                                    </span>
                                    <span class="d-block">Firma Electrónica</span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-dash-1" role="tabpanel" aria-labelledby="dash-tab-1">
                    <input type="hidden" id="completeSeccion1" value="1"/>
                    <div class="card card-body shadow">
                        <p class="text-muted mb-10 text-xl-left text-lg-left text-md-left text-sm-center">
                            Todos los campos descritos con <code>(*)</code>, son campos obligatorios.
                        </p>
                        <div class="divError"></div>
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-5 col-sm-12 mb-10">
                                <label for="ndoc_p" class="col-form-label mb-0 pb-0">
                                    Número de documento (DNI/CE) <span class="text-danger-400">*</span>
                                </label>
                                <input type="text" class="form-control border-input text-left" value="<?=$dtlleColaborador['ndoc_col']?>" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-4 col-lg-4 col-md-5 col-sm-12 mb-10">
                                <label for="ndoc_p" class="col-form-label mb-0 pb-0">
                                    Apellido Paterno <span class="text-danger-400">*</span>
                                </label>
                                <input type="text" class="form-control border-input text-left" value="<?=$dtlleColaborador['apa_col']?>" readonly>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-5 col-sm-12 mb-0">
                                <label for="ama_p" class="col-form-label mb-0 pb-0">
                                    Apellido Materno <span class="text-danger-400">*</span>
                                </label>
                                <input type="text" class="form-control border-input text-left" value="<?=$dtlleColaborador['ama_col']?>" readonly>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-5 col-sm-12 mb-0">
                                <label for="name_p" class="col-form-label mb-0 pb-0">
                                    Nombres <span class="text-danger-400">*</span>
                                </label>
                                <input type="text" class="form-control border-input text-left" value="<?=$dtlleColaborador['nombres_col']?>" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-10">
                                <label for="cargo_p" class="col-form-label mb-0 pb-0">
                                    Cargo <span class="text-danger-400">*</span>
                                </label>
                                <input type="text" class="form-control border-input text-left" value="<?=$dtlleColaborador['cargo_col']?>" readonly>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-10">
                                <label for="areaop_p" class="col-form-label mb-0 pb-0">
                                    Area operativa <span class="text-danger-400">*</span>
                                </label>
                                <input type="text" class="form-control border-input text-left" value="<?=$dtlleColaborador['areaop_col']?>" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12" id="divFecha">
                                <label for="fechacontrato_p" class="col-form-label mb-0 pb-0">
                                    Fecha inicio contrato<span class="text-danger-400">*</span>
                                </label>
                                <input type="text" class="form-control border-input text-left" value="<?=$obj_fn->fecha_ENG_ESP($dtlleColaborador['fcontrato_col'])?>" readonly>
                            </div>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 mb-10">
                                <label for="legajo_p" class="col-form-label mb-0 pb-0">
                                    Número de legajo <span class="text-danger-400">*</span>
                                </label>
                                <input type="text" class="form-control border-input text-left" value="<?=$dtlleColaborador['legajo_col']?>" readonly>
                            </div>
                            <div class="col-xl-5 col-lg-5 col-md-5 col-sm-12 mb-10">
                                <label for="email_p" class="col-form-label mb-0 pb-0">
                                    Correo Corporativo/Personal <span class="text-danger-400">*</span>
                                </label>
                                <input type="text" class="form-control border-input text-left" value="<?=$dtlleColaborador['email_col']?>" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="nav-dash-2" role="tabpanel" aria-labelledby="dash-tab-2">
                    <input type="hidden" id="completeSeccion2" value="1"/>
                    <div id="signature-pad" class="signature-pad" style="height: 500px">
                        <div class="signature-pad--body">
                            <img style="display:block; width:100%;height:100%;"
                                 src="<?=$dtlleColaborador['imgsign_col']?>" />
                        </div>
                        <div class="signature-pad--footer">
                            <div class="description text-muted">firmar registrada</div>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <br>
            <br>
            <?php
        }
        catch (PDOException $e) {
            throw $e;
        }
    } //COn firma

    public function search_Colaborador_IN(){
        try {
            $ndoc = trim($_POST['ndoc']);
            $actEdit = (int)$_POST['edit'];
            $actDelete = (int)$_POST['del'];
            $obj_fn = new FuncionesModel();
            $obj_col = new ColaboradorModel();
            $dtlleCol = $obj_col->buscar_colaborador_xnDoc($ndoc);
            $disabled = "";
            $idColaborador = 0;
            $idServicio = 0;
            $tFiniContrato = "**/**/****";
            $tLegajo = "";
            $tApePaterno = "";
            $tApeMaterno = "";
            $tNombres = "";
            $tCargo = "";
            $tCorreo = "";
            $tAreaOP = "";
            if(!is_null($dtlleCol)){
                $disabled = " disabled ";
                $idColaborador = (int)$dtlleCol['id_col'];
                $idServicio = (int)$dtlleCol['id_serv'];
                $tDocumento = $dtlleCol['ndoc_col'];
                $tApePaterno = $dtlleCol['apa_col'];
                $tApeMaterno = $dtlleCol['ama_col'];
                $tCargo = $dtlleCol['cargo_col'];
                $tNombres = $dtlleCol['nombres_col'];
                $tAreaOP = $dtlleCol['areaop_col'];
                $tCorreo = $dtlleCol['email_col'];
                $tFiniContrato = $obj_fn->fecha_ENG_ESP($dtlleCol['fcontrato_col']);
                $tLegajo = $dtlleCol['legajo_col'];
            }

            $obj_ge = new GerenciaModel();
            $lstGerencias = $obj_ge->lst_Gerencia_Activas();
            ?>
            <form id="formNewPersonal" role="form" method="post">
                <input type="hidden" name="idColaborador" value="<?=$idColaborador?>">
                <?php
                if(is_null($dtlleCol)){?>
                    <div class="alert alert-warning alert-wth-icon alert-dismissible fade show text-center" role="alert">
                        <span class="alert-icon-wrap">
                            <i class="icon-question fz-18"></i>
                        </span>
                        El número de documento no se encuentra registrado en el sistema, complete los campos descritos a continuación:
                    </div>
                    <?php
                }
                else{?>
                    <div class="alert alert-success alert-wth-icon alert-dismissible fade show text-center" role="alert">
                        <span class="alert-icon-wrap">
                            <i class="icon-check fz-18"></i>
                        </span>
                        Número de documento verificado, los datos del <span class="font-weight-bold text-uppercase">Personal</span>, se muestran a continuación:
                    </div>
                    <?php
                }
                ?>
                <div class="card border-secondary">
                    <h5 class="card-header">
                        Datos Personales
                        <?php
                        if(!is_null($dtlleCol)){
                            if($actDelete === 1){?>
                                <a class="btn btn-icon btn-icon-circle btn-secondary btn-icon-style-3 btn-icon-style-5 float-right"
                                        id="btnDelete" style="top:-10px;" title="Eliminar" data-id="<?=$idColaborador?>">
                                    <span class="btn-icon-wrap"><i class="icon-trash"></i></span>
                                </a>
                                <?php
                            }

                            if($actEdit === 1){?>
                                <a class="btn btn-icon btn-icon-circle btn-secondary btn-icon-style-3 mr-10 float-right"
                                        id="btnActivatedEdit" style="top:-10px;" title="Editar">
                                    <span class="btn-icon-wrap"><i class="icon-pencil"></i></span>
                                </a>
                                <?php
                            }
                        }
                        ?>
                    </h5>
                    <div class="card-body">
                        <p class="mb-25">
                            Todos los campos descritos con (<code class="font-weight-bold text-danger-800">*</code>), son campos obligatorios.
                        </p>
                        <div class="form-group row">
                            <label for="apepa_ap" class="col-xl-5 col-lg-5 col-md-5 col-sm-12 col-form-label text-xl-right text-lg-right text-md-right text-sm-left">
                                Número documento(DNI/CE)
                                <span class="text-danger">*</span>
                            </label>
                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                                <input type="text" class="form-control" name="ndoc_p" placeholder="Número documento" required maxlength="13" autocomplete="off"
                                       value="<?php if(!is_null($dtlleCol)){ echo $tDocumento;} else{echo $ndoc;}?>" <?=$disabled?>>
                                <small class="form-text text-muted">Máximo 13 carácteres</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="apepa_ap" class="col-xl-5 col-lg-5 col-md-5 col-sm-12 col-form-label text-xl-right text-lg-right text-md-right text-sm-left">
                                Fecha inicio contrato
                                <span class="text-danger">*</span>
                            </label>
                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                                <input type="text" class="form-control border-input text-left inputFecha classEnabled" placeholder="**/**/****"
                                       name="fechacontrato_p" required="required" autocomplete="off" maxlength="10" value="<?=$tFiniContrato?>" <?=$disabled?>>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="apepa_ap" class="col-xl-5 col-lg-5 col-md-5 col-sm-12 col-form-label text-xl-right text-lg-right text-md-right text-sm-left">
                                Número de legajo
                                <span class="text-danger">*</span>
                            </label>
                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                                <input type="number" class="form-control border-input text-left classEnabled" placeholder="ingrese valor"
                                       name="legajo_p" required="required" autocomplete="off" maxlength="8"
                                       oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                       step="1" min="1" onkeydown="return event.keyCode !== 69" value="<?=$tLegajo?>" <?=$disabled?>>
                                <small class="form-text text-muted">Máximo 8 carácteres</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="apepa_ap" class="col-xl-5 col-lg-5 col-md-5 col-sm-12 col-form-label text-xl-right text-lg-right text-md-right text-sm-left">
                                Servicio/Sede
                                <span class="text-danger">*</span>
                            </label>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                <select name="idserv_p" class="form-control input-md selectSearch classEnabled" data-placeholder="Servicio..." <?=$disabled?>>
                                    <option></option>
                                    <?php
                                    if (!is_null($lstGerencias)) {
                                        $obj_serv = new ServicioModel();
                                        foreach ($lstGerencias as $gerencia) {
                                            $lstServicios = $obj_serv->lst_Servicio_Activos_xGerencia_All($gerencia['id_ge']);
                                            if (!is_null($lstServicios)) {?>
                                                <optgroup label="<?= $gerencia['des_ge'] ?>">
                                                    <?php
                                                    foreach ($lstServicios as $servicio) {
                                                        if((int)$servicio['id_serv'] === $idServicio){?>
                                                            <option value="<?= $servicio['id_serv'] ?>" selected>
                                                                <?= $servicio['des_serv'] ?>
                                                            </option>
                                                        <?php
                                                        }
                                                        else {?>
                                                            <option value="<?= $servicio['id_serv'] ?>">
                                                                <?= $servicio['des_serv'] ?>
                                                            </option>
                                                            <?php
                                                        }
                                                    } ?>
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
                            <label for="apepa_ap" class="col-xl-5 col-lg-5 col-md-5 col-sm-12 col-form-label text-xl-right text-lg-right text-md-right text-sm-left">
                                Apellido Paterno
                                <span class="text-danger">*</span>
                            </label>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                <input type="text" class="form-control classEnabled" name="apa_p" placeholder="Ingrese una descripción" required maxlength="45" autocomplete="off"
                                       value="<?=$tApePaterno?>" <?=$disabled?>>
                                <small class="form-text text-muted">Máximo 45 carácteres</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="apema_ap" class="col-xl-5 col-lg-5 col-md-5 col-sm-12 col-form-label text-xl-right text-lg-right text-md-right text-sm-left">
                                Apellido Materno
                                <span class="text-danger">*</span>
                            </label>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                <input type="text" class="form-control classEnabled" name="ama_p" placeholder="Ingrese una descripción" required maxlength="45" autocomplete="off"
                                       value="<?=$tApeMaterno?>" <?=$disabled?>>
                                <small class="form-text text-muted">Máximo 45 carácteres</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nombre_ap" class="col-xl-5 col-lg-5 col-md-5 col-sm-12 col-form-label text-xl-right text-lg-right text-md-right text-sm-left">
                                Nombres
                                <span class="text-danger">*</span>
                            </label>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                <input type="text" class="form-control classEnabled" name="name_p" placeholder="Ingrese una descripción" required maxlength="45" autocomplete="off"
                                       value="<?=$tNombres?>" <?=$disabled?>>
                                <small class="form-text text-muted">Máximo 45 carácteres</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nombre_ap" class="col-xl-5 col-lg-5 col-md-5 col-sm-12 col-form-label text-xl-right text-lg-right text-md-right text-sm-left">
                                Puesto
                                <span class="text-danger">*</span>
                            </label>
                            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12">
                                <input type="text" class="form-control classEnabled" name="cargo_p" placeholder="Ingrese una descripción" required maxlength="45" autocomplete="off"
                                       value="<?=$tCargo?>" <?=$disabled?>>
                                <small class="form-text text-muted">Máximo 45 carácteres</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nombre_ap" class="col-xl-5 col-lg-5 col-md-5 col-sm-12 col-form-label text-xl-right text-lg-right text-md-right text-sm-left">
                                Area operativa
                                <span class="text-danger">*</span>
                            </label>
                            <div class="col-xl-5 col-lg-5 col-md-5 col-sm-12">
                                <input type="text" class="form-control classEnabled" name="areaop_p" placeholder="Ingrese una descripción" required maxlength="50" autocomplete="off"
                                       value="<?=$tAreaOP?>" <?=$disabled?>>
                                <small class="form-text text-muted">Máximo 50 carácteres</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nombre_ap" class="col-xl-5 col-lg-5 col-md-5 col-sm-12 col-form-label text-xl-right text-lg-right text-md-right text-sm-left">
                                Correo Personal/Corporativo
                                <span class="text-danger">*</span>
                            </label>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                <input type="email" class="form-control classEnabled" name="email_p" placeholder="Ingrese un email" required maxlength="45" autocomplete="off"
                                       value="<?=$tCorreo?>" <?=$disabled?>>
                                <small class="form-text text-muted">Máximo 45 carácteres</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 text-center" id="divActions">
                    <?php
                    if(is_null($dtlleCol)){?>
                        <button type="button" id="btnCancelPer" class="btn btn-light btn-lg mr-20">
                            <i class="ti-close position-left"></i>
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary btn-hover-transform btn-lg">
                            <i class="ti-plus position-left"></i>
                            Agregar
                        </button>
                        <?php
                    }
                    ?>
                    </div>
                </div>
            </form>
            <?php
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }
}

