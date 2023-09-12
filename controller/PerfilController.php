<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);
require_once '../model/PerfilModel.php';
require_once '../model/FuncionesModel.php';

$action = $_REQUEST["action"];
$controller = new PerfilController();
call_user_func(array($controller,$action));

class PerfilController {

    public function lst_Perfil_All_JSON(){
        try {
            $obj_pf = new PerfilModel();
            $lstPerfil = $obj_pf->lst_Perfil_All();

            $datos = array();
            if(is_array($lstPerfil)){
                foreach($lstPerfil as $perfil){

                    $estado = '<span class="text-danger-700">Suspendido</span>';
                    if((int)$perfil['condicion_perfil']== 1){
                        $estado = '<span class="text-green-700">Activo</span>';
                    }
                    $optNuevo = "";
                    if((int)$perfil['nuevo_perfil'] == 1){
                        $optNuevo ='<i class="text-success-800 ti-check"></i>';
                    }
                    $optEdit = "";
                    if((int)$perfil['editar_perfil'] == 1){
                        $optEdit ='<i class="text-success-800 ti-check"></i>';
                    }
                    $optDel = "";
                    if((int)$perfil['eliminar_perfil'] == 1){
                        $optDel ='<i class="text-success-800 ti-check"></i>';
                    }
                    $optView = "";
                    if((int)$perfil['visualizar_perfil'] == 1){
                        $optView ='<i class="text-success-800 ti-check"></i>';
                    }
                    $optReport = "";
                    if((int)$perfil['reporte_perfil'] == 1){
                        $optReport ='<i class="text-success-800 ti-check"></i>';
                    }
                    $optImport = "";
                    if((int)$perfil['importar_perfil'] == 1){
                        $optImport ='<i class="text-success-800 ti-check"></i>';
                    }
                    $optActive = "";
                    if((int)$perfil['activasusp_perfil'] == 1){
                        $optActive ='<i class="text-success-800 ti-check"></i>';
                    }
                    $optTransfer = "";
                    if((int)$perfil['transferir_perfil'] == 1){
                        $optTransfer ='<i class="text-success-800 ti-check"></i>';
                    }
                    $optRetiro = "";
                    if((int)$perfil['retirar_perfil'] == 1){
                        $optRetiro ='<i class="text-success-800 ti-check"></i>';
                    }
                    $optDevolver = "";
                    if((int)$perfil['devolver_perfil'] == 1){
                        $optDevolver ='<i class="text-success-800 ti-check"></i>';
                    }

                    // GUIDE
                    $opCreateGuide = "";
                    if((int)$perfil['create_guide'] == 1){
                        $opCreateGuide ='<i class="text-success-800 ti-check"></i>';
                    }

                    $opEditGuide = "";
                    if((int)$perfil['edit_guide'] == 1){
                        $opEditGuide ='<i class="text-success-800 ti-check"></i>';
                    }

                    $opRevertGuide = "";
                    if((int)$perfil['revert_guide'] == 1){
                        $opRevertGuide ='<i class="text-success-800 ti-check"></i>';
                    }

                    $opShowGuide = "";
                    if((int)$perfil['show_guide'] == 1){
                        $opShowGuide ='<i class="text-success-800 ti-check"></i>';
                    }

                    $row = array(
                        0 => "",
                        1 => $perfil['id_perfil'],
                        2 => $perfil['titulo_perfil'],
                        3 => $optNuevo,
                        4 => $optEdit,
                        5 => $optDel,
                        6 => $optView,
                        7 => $optReport,
                        8 => $optImport,
                        9 => $optActive,
                        10=> $optTransfer,
                        11=> $optRetiro,
                        12=> $optDevolver,
                        13=> $estado,
                        14=> $opCreateGuide,
                        15=> $opEditGuide,
                        16=> $opRevertGuide,
                        17=> $opShowGuide,
                    );

                    array_push($datos, $row);
                }
            }

            $tabla = array('data' => $datos);
            echo json_encode($tabla);
            unset($datos);

        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function loadCampos_NuevoPerfil_Ajax(){
        try {?>
            <div class="row">
                <div class="col-12">
                    <div class="page-title">
                        <h4 class="mb-0 text-info">
                            Nuevo Perfil
                        </h4>
                        <ol class="breadcrumb mb-0 pl-0 pt-1 pb-0">
                            <li class="breadcrumb-item text-muted">Registre un nuevo perfil.</li>
                        </ol>
                    </div>
                </div>
            </div>
            <form id="formNewPerfil" role="form" method="post">
                <div class="card mb-25">
                    <div class="card-body">
                        <p>
                            Todos los campos descritos con (<code class="font-weight-bold text-danger-800">*</code>), son campos obligatorios.
                        </p>
                        <div class="row">
                            <div class="col-12" id="mensajes_actions_pf"></div>
                        </div>
                        <div class="form-group row">
                            <label for="abrev_um" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                Titulo
                                <span class="text-danger font-weight-bold">*</span>
                            </label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control input-md text-left"
                                       name="titulo_pf" maxlength="45" required placeholder="nombre del perfil">
                                <small class="help-block text-muted">Máximo 45 caracteres</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="abrev_um" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                Descripción
                            </label>
                            <div class="col-sm-5">
                            <textarea class="form-control" name="des_pf" rows="4" cols="1" maxlength="300"
                                      placeholder="ingrese un detalle del perfil"></textarea>
                                <small class="help-block text-muted">Máximo 300 caracteres</small>
                            </div>
                        </div>
                        <hr>
                        <h4>Opciones de acceso para el perfil a generar:</h4>
                        <hr>
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-xs-6 col-xs-6 offset-lg-2 offset-md-2">
                                <div class="form-group">
                                    <label class="control control-outline control-outline-info control--checkbox">Nuevo
                                        <input type="checkbox" name="chkNuevo" value="1">
                                        <span class="control__indicator"></span>
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label class="control control-outline control-outline-info control--checkbox">Editar
                                        <input type="checkbox" name="chkEditar" value="1">
                                        <span class="control__indicator"></span>
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label class="control control-outline control-outline-info control--checkbox">Eliminar
                                        <input type="checkbox" name="chkEliminar" value="1">
                                        <span class="control__indicator"></span>
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label class="control control-outline control-outline-info control--checkbox">Visualizar
                                        <input type="checkbox" name="chkVisualiza" value="1">
                                        <span class="control__indicator"></span>
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label class="control control-outline control-outline-info control--checkbox">Reporte
                                        <input type="checkbox" name="chkReporte" value="1">
                                        <span class="control__indicator"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-xs-6 col-xs-6">
                                <div class="form-group">
                                    <label class="control control-outline control-outline-info control--checkbox">Importar
                                        <input type="checkbox" name="chkImport" value="1">
                                        <span class="control__indicator"></span>
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label class="control control-outline control-outline-info control--checkbox">Activa / Suspender
                                        <input type="checkbox" name="chkActive" value="1">
                                        <span class="control__indicator"></span>
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label class="control control-outline control-outline-info control--checkbox">Transferir
                                        <input type="checkbox" name="chkTransferir" value="1">
                                        <span class="control__indicator"></span>
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label class="control control-outline control-outline-info control--checkbox">Retirar
                                        <input type="checkbox" name="chkRetirar" value="1">
                                        <span class="control__indicator"></span>
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label class="control control-outline control-outline-info control--checkbox">Devolver
                                        <input type="checkbox" name="chkDevolver" value="1">
                                        <span class="control__indicator"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <h4>Opciones de acceso para el perfil - Guías de Remisión:</h4>
                        <hr>
                        <div class="row">
                            <div class="col-12 col-md-8 mx-auto">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="control control-outline control-outline-info control--checkbox">Crear guía
                                                <input type="checkbox" name="chkCreateGuide" value="1" <?php if((int)$dtllePerfil['create_guide']==1){ echo "checked";}?>>
                                                <span class="control__indicator"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="control control-outline control-outline-info control--checkbox">Editar guía
                                                <input type="checkbox" name="chkEditGuide" value="1" <?php if((int)$dtllePerfil['edit_guide']==1){ echo "checked";}?>>
                                                <span class="control__indicator"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="control control-outline control-outline-info control--checkbox">Revertir guía
                                                <input type="checkbox" name="chkRevertGuide" value="1" <?php if((int)$dtllePerfil['revert_guide']==1){ echo "checked";}?>>
                                                <span class="control__indicator"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="control control-outline control-outline-info control--checkbox">Visualizar guía
                                                <input type="checkbox" name="chkShowGuide" value="1" <?php if((int)$dtllePerfil['show_guide']==1){ echo "checked";}?>>
                                                <span class="control__indicator"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="no-margin no-padding">
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-12 text-center">
                                <button type="button" id="btnCancel" class="btn btn-light mr-10">
                                    Cancelar
                                </button>
                                <button type="submit" class="btn btn-info btn-hover-transform">
                                    Grabar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <?php
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function registrar_Perfil_JSON(){
        try {
            $obj_fn = new FuncionesModel();

            $opNuevo = 0;
            if(!is_null($_POST['chkNuevo'])){ $opNuevo = (int)$_POST['chkNuevo']; }
            $opEditar = 0;
            if(!is_null($_POST['chkEditar'])){ $opEditar = (int)$_POST['chkEditar']; }
            $opEliminar = 0;
            if(!is_null($_POST['chkEliminar'])){ $opEliminar = (int)$_POST['chkEliminar']; }
            $opVisualiza = 0;
            if(!is_null($_POST['chkVisualiza'])){ $opVisualiza = (int)$_POST['chkVisualiza']; }
            $opReporte = 0;
            if(!is_null($_POST['chkReporte'])){ $opReporte = (int)$_POST['chkReporte']; }
            $opImport = 0;
            if(!is_null($_POST['chkImport'])){ $opImport = (int)$_POST['chkImport']; }
            $opActive = 0;
            if(!is_null($_POST['chkActive'])){ $opActive = (int)$_POST['chkActive']; }
            $opTransfer = 0;
            if(!is_null($_POST['chkTransferir'])){ $opTransfer = (int)$_POST['chkTransferir']; }
            $opRetirar = 0;
            if(!is_null($_POST['chkRetirar'])){ $opRetirar = (int)$_POST['chkRetirar']; }
            $opDevolver = 0;
            if(!is_null($_POST['chkDevolver'])){ $opDevolver = (int)$_POST['chkDevolver']; }

            // GUIDE
            $opCreateGuide = 0;
            if(!is_null($_POST['chkCreateGuide'])){ $opCreateGuide = (int)$_POST['chkCreateGuide']; }
            $opEditGuide = 0;
            if(!is_null($_POST['chkEditGuide'])){ $opEditGuide = (int)$_POST['chkEditGuide']; }
            $opRevertGuide = 0;
            if(!is_null($_POST['chkRevertGuide'])){ $opRevertGuide = (int)$_POST['chkRevertGuide']; }
            $opShowGuide = 0;
            if(!is_null($_POST['chkShowGuide'])){ $opShowGuide = (int)$_POST['chkShowGuide']; }

            $datesPF[0] = $obj_fn->quitar_caracteresEspeciales($_POST['titulo_pf']);
            $datesPF[1] = $obj_fn->quitar_caracteresEspeciales($_POST['des_pf']);
            $datesPF[2] = $opNuevo;
            $datesPF[3] = $opEditar;
            $datesPF[4] = $opEliminar;
            $datesPF[5] = $opVisualiza;
            $datesPF[6] = $opReporte;
            $datesPF[7] = $opImport;
            $datesPF[8] = $opActive;
            $datesPF[9] = $opTransfer;
            $datesPF[10] = $opRetirar;
            $datesPF[11] = $opDevolver;
            $datesPF[12]= $opCreateGuide;
            $datesPF[13]= $opEditGuide;
            $datesPF[14]= $opRevertGuide;
            $datesPF[15]= $opShowGuide;
            $val = 0;
            $obj_model = new PerfilModel();
            $insertPF = $obj_model->registrar_Perfil($datesPF);
            if($insertPF){ $val = 1; }
            echo json_encode(array('status'=>$val));
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function Update_Estado_Perfil_JSON(){
        try {
            $estado = $_POST['estd'];
            $arrayID = $_POST['id'];
            $val = 0;
            $acierto = 0;
            for($i=0; $i<sizeof($arrayID); $i++){
                $datesALM[0] = (int)$arrayID[$i];
                $datesALM[1] = (int)$estado;
                $obj_pf = new PerfilModel();
                $update = $obj_pf->update_Estado_Perfil($datesALM);
                if($update){ $acierto++; }
            }
            if(sizeof($arrayID) == (int)$acierto){$val=1;}
            else if((int)$acierto > 1){$val = 2;}
            echo json_encode(array('status'=>$val));
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function delete_Perfil_JSON(){
        try {
            $arrayID = $_POST['id'];
            $obj_pf = new PerfilModel();
            $val = 0;
            $acierto = 0;
            for($t=0; $t<sizeof($arrayID);$t++){
                $delete = $obj_pf->delete_Perfil_xID($arrayID[$t]);
                if($delete){$acierto++;}
            }

            if(sizeof($arrayID) == (int)$acierto){$val=1;}
            else if((int)$acierto > 1){$val = 2;}

            echo json_encode(array('status'=>$val));

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function loadCampos_EditarPerfil_Ajax(){
        try {
            $id = (int)$_GET['id'];
            $obj_pf = new PerfilModel();
            $dtllePerfil = $obj_pf->detalle_Perfil_xID($id);?>
            <div class="row">
                <div class="col-12">
                    <div class="page-title">
                        <h4 class="mb-0 text-warning-0">
                            Actualizar Perfil
                        </h4>
                        <ol class="breadcrumb mb-0 pl-0 pt-1 pb-0">
                            <li class="breadcrumb-item text-muted">Actualice los datos contemplados en el perfil.</li>
                        </ol>
                    </div>
                </div>
            </div>
            <form id="formEditPerfil" role="form" method="post">
                <input type="hidden" name="idpf" value="<?=$id?>">
                <div class="card mb-25">
                    <div class="card-body">
                        <p>
                            Todos los campos descritos con (<code class="font-weight-bold text-danger-800">*</code>), son campos obligatorios.
                        </p>
                        <div class="row">
                            <div class="col-12" id="mensajes_actions_pf"></div>
                        </div>
                        <div class="form-group row">
                            <label for="abrev_um" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                Titulo
                                <span class="text-danger font-weight-bold">*</span>
                            </label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control input-md text-left" value="<?=$dtllePerfil['titulo_perfil']?>"
                                       name="titulo_pf" maxlength="45" required placeholder="nombre del perfil">
                                <small class="help-block text-muted">Máximo 45 caracteres</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="abrev_um" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                Descripción
                            </label>
                            <div class="col-sm-5">
                            <textarea class="form-control" name="des_pf" rows="4" cols="1" maxlength="300"
                                      placeholder="ingrese un detalle del perfil"><?=$dtllePerfil['des_perfil']?></textarea>
                                <small class="help-block text-muted">Máximo 300 caracteres</small>
                            </div>
                        </div>

                        <hr>
                        <h4>Opciones de acceso para el perfil:</h4>
                        <hr>
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-xs-6 col-xs-6 offset-lg-2 offset-md-2">
                                <div class="form-group">
                                    <label class="control control-outline control-outline-info control--checkbox">Nuevo
                                        <input type="checkbox" name="chkNuevo" value="1" <?php if((int)$dtllePerfil['nuevo_perfil']==1){ echo "checked";}?>>
                                        <span class="control__indicator"></span>
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label class="control control-outline control-outline-info control--checkbox">Editar
                                        <input type="checkbox" name="chkEditar" value="1" <?php if((int)$dtllePerfil['editar_perfil']==1){ echo "checked";}?>>
                                        <span class="control__indicator"></span>
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label class="control control-outline control-outline-info control--checkbox">Eliminar
                                        <input type="checkbox" name="chkEliminar" value="1" <?php if((int)$dtllePerfil['eliminar_perfil']==1){ echo "checked";}?>>
                                        <span class="control__indicator"></span>
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label class="control control-outline control-outline-info control--checkbox">Visualizar
                                        <input type="checkbox" name="chkVisualiza" value="1" <?php if((int)$dtllePerfil['visualizar_perfil']==1){ echo "checked";}?>>
                                        <span class="control__indicator"></span>
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label class="control control-outline control-outline-info control--checkbox">Reporte
                                        <input type="checkbox" name="chkReporte" value="1" <?php if((int)$dtllePerfil['reporte_perfil']==1){ echo "checked";}?>>
                                        <span class="control__indicator"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-xs-6 col-xs-6">
                                <div class="form-group">
                                    <label class="control control-outline control-outline-info control--checkbox">Importar/Exportar
                                        <input type="checkbox" name="chkImport" value="1" <?php if((int)$dtllePerfil['importar_perfil']==1){ echo "checked";}?>>
                                        <span class="control__indicator"></span>
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label class="control control-outline control-outline-info control--checkbox">Activa / Suspender
                                        <input type="checkbox" name="chkActive" value="1" <?php if((int)$dtllePerfil['activasusp_perfil']==1){ echo "checked";}?>>
                                        <span class="control__indicator"></span>
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label class="control control-outline control-outline-info control--checkbox">Transferir
                                        <input type="checkbox" name="chkTransferir" value="1" <?php if((int)$dtllePerfil['transferir_perfil']==1){ echo "checked";}?>>
                                        <span class="control__indicator"></span>
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label class="control control-outline control-outline-info control--checkbox">Retirar
                                        <input type="checkbox" name="chkRetirar" value="1" <?php if((int)$dtllePerfil['retirar_perfil']==1){ echo "checked";}?>>
                                        <span class="control__indicator"></span>
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label class="control control-outline control-outline-info control--checkbox">Devolver
                                        <input type="checkbox" name="chkDevolver" value="1" <?php if((int)$dtllePerfil['devolver_perfil']==1){ echo "checked";}?>>
                                        <span class="control__indicator"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h4>Opciones de acceso para el perfil - Guías de Remisión:</h4>
                        <hr>
                        <div class="row">
                            <div class="col-12 col-md-8 mx-auto">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="control control-outline control-outline-info control--checkbox">Crear guía
                                                <input type="checkbox" name="chkCreateGuide" value="1" <?php if((int)$dtllePerfil['create_guide']==1){ echo "checked";}?>>
                                                <span class="control__indicator"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="control control-outline control-outline-info control--checkbox">Editar guía
                                                <input type="checkbox" name="chkEditGuide" value="1" <?php if((int)$dtllePerfil['edit_guide']==1){ echo "checked";}?>>
                                                <span class="control__indicator"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="control control-outline control-outline-info control--checkbox">Revertir guía
                                                <input type="checkbox" name="chkRevertGuide" value="1" <?php if((int)$dtllePerfil['revert_guide']==1){ echo "checked";}?>>
                                                <span class="control__indicator"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="control control-outline control-outline-info control--checkbox">Visualizar guía
                                                <input type="checkbox" name="chkShowGuide" value="1" <?php if((int)$dtllePerfil['show_guide']==1){ echo "checked";}?>>
                                                <span class="control__indicator"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="no-margin no-padding">
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-12 text-center">
                                <button type="button" id="btnCancel" class="btn btn-light mr-10">
                                    Cancelar
                                </button>
                                <button type="submit" class="btn bg-warning-0 btn-hover-transform">
                                    Actualizar
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
            <?php
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function update_Perfil_JSON(){
        try {
            $obj_fn = new FuncionesModel();

            $opNuevo = 0;
            if(!is_null($_POST['chkNuevo'])){ $opNuevo = (int)$_POST['chkNuevo']; }
            $opEditar = 0;
            if(!is_null($_POST['chkEditar'])){ $opEditar = (int)$_POST['chkEditar']; }
            $opEliminar = 0;
            if(!is_null($_POST['chkEliminar'])){ $opEliminar = (int)$_POST['chkEliminar']; }
            $opVisualiza = 0;
            if(!is_null($_POST['chkVisualiza'])){ $opVisualiza = (int)$_POST['chkVisualiza']; }
            $opReporte = 0;
            if(!is_null($_POST['chkReporte'])){ $opReporte = (int)$_POST['chkReporte']; }
            $opImport = 0;
            if(!is_null($_POST['chkImport'])){ $opImport = (int)$_POST['chkImport']; }
            $opActive = 0;
            if(!is_null($_POST['chkActive'])){ $opActive = (int)$_POST['chkActive']; }
            $opTransfer = 0;
            if(!is_null($_POST['chkTransferir'])){ $opTransfer = (int)$_POST['chkTransferir']; }
            $opRetirar = 0;
            if(!is_null($_POST['chkRetirar'])){ $opRetirar = (int)$_POST['chkRetirar']; }
            $opDevolver = 0;
            if(!is_null($_POST['chkDevolver'])){ $opDevolver = (int)$_POST['chkDevolver']; }

            // GUIDE
            $opCreateGuide = 0;
            if(!is_null($_POST['chkCreateGuide'])){ $opCreateGuide = (int)$_POST['chkCreateGuide']; }
            $opEditGuide = 0;
            if(!is_null($_POST['chkEditGuide'])){ $opEditGuide = (int)$_POST['chkEditGuide']; }
            $opRevertGuide = 0;
            if(!is_null($_POST['chkRevertGuide'])){ $opRevertGuide = (int)$_POST['chkRevertGuide']; }
            $opShowGuide = 0;
            if(!is_null($_POST['chkShowGuide'])){ $opShowGuide = (int)$_POST['chkShowGuide']; }

            $datesPF[0] = $obj_fn->quitar_caracteresEspeciales($_POST['titulo_pf']);
            $datesPF[1] = $obj_fn->quitar_caracteresEspeciales($_POST['des_pf']);
            $datesPF[2] = $opNuevo;
            $datesPF[3] = $opEditar;
            $datesPF[4] = $opEliminar;
            $datesPF[5] = $opVisualiza;
            $datesPF[6] = $opReporte;
            $datesPF[7] = $opImport;
            $datesPF[8] = $opActive;
            $datesPF[9] = $opTransfer;
            $datesPF[10]= $opRetirar;
            $datesPF[11]= $opDevolver;
            $datesPF[12]= $opCreateGuide;
            $datesPF[13]= $opEditGuide;
            $datesPF[14]= $opRevertGuide;
            $datesPF[15]= $opShowGuide;
            $datesPF[16]= (int)$_POST['idpf'];
            $val = 0;
            $obj_pf = new PerfilModel();
            $updatePF = $obj_pf->update_Perfil($datesPF);
            if($updatePF){ $val = 1; }
            echo json_encode(array('status'=>$val));
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_Perfiles_Activos_JSON(){
        try {
            $obj_pf = new PerfilModel();
            $lstPerfil = $obj_pf->lst_Perfil_All();
            $datos = array();
            if(is_array($lstPerfil)){
                foreach($lstPerfil as $perfil){
                    $row = array(
                        'id' => $perfil['id_perfil'],
                        'texto' => $perfil['titulo_perfil']
                    );
                    array_push($datos, $row);
                }
            }
            echo json_encode($datos);

        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }


}


