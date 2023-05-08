<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);
require_once '../model/AlmacenModel.php';
require_once '../model/FuncionesModel.php';
require_once '../model/UsuarioModel.php';
require_once '../model/PersonaModel.php';
require_once '../model/ServicioModel.php';
require_once '../model/ValeModel.php';
require_once '../model/UbigeoModel.php';

$action = $_REQUEST["action"];
$controller = new AlmacenController();
call_user_func(array($controller,$action));

class AlmacenController {

    public function detail_carga_Almacen_xIDAlm(){
        try {
            $idAlm = (int)$_GET['id'];
            $obj_alm = new AlmacenModel();
            $obj_fn = new FuncionesModel();
            $detalleLog = $obj_alm->details_ultimo_carga_Almacen_xIDAlm($idAlm);

            $numberCant = 0;
            $updFecha = "";
            if(is_array($detalleLog)) {
                $numberCant = (int)$detalleLog['cantidad'];
                $updFecha = $detalleLog['dianumber'] . " " . $obj_fn->texto_mes($detalleLog['mes']) . " " . $detalleLog['anio'] . " " . $detalleLog['hora'];
            }
            $datos = array(
                'cant' =>$numberCant,
                'fecha' => $updFecha
            );

            echo json_encode($datos);
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function loadSelect_Almacen_ServicioAll_JSON(){
        try {
            $idServicio = (int)$_GET['idserv'];
            $obj_alm = new AlmacenModel();
            $lstAlmacen = $obj_alm->lst_almacenes_Activos_All_xServicio($idServicio);

            $datos = array();
            if(is_array($lstAlmacen)){
                foreach($lstAlmacen as $almacen){

                    $row = array(
                        'id' => trim($almacen['id_alm']),
                        'texto' => trim($almacen['titulo_alm']),
                        'vista' => (int)$almacen['vista_alm']
                    );
                    array_push($datos, $row);
                }
            }

            echo json_encode($datos);
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function loadSelect_Almacen_ServicioUsuario_JSON(){
        try {
            $idsu = (int)$_GET['idsu'];
            $obj_alm = new AlmacenModel();
            $lstAlmacen = $obj_alm->lst_Almacenes_Asignados_xUsuario($idsu);

            $datos = array();
            if(is_array($lstAlmacen)){
                foreach($lstAlmacen as $almacen){

                    $row = array(
                        'id' => trim($almacen['id_alm']),
                        'texto' => trim($almacen['titulo_alm'])
                    );
                    array_push($datos, $row);
                }
            }

            echo json_encode($datos);
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function registrar_log_Upload_Almacen(){
        try {
            date_default_timezone_set("America/Lima");
            setlocale(LC_TIME, 'es_PE.UTF-8');
            $obj_fn = new FuncionesModel();
            $idAlm = (int)$_POST['id'];
            $total = (int)$_POST['total'];
            $idUSER = $obj_fn->encrypt_decrypt('decrypt',$_POST['idus']);
            $fechaActual = date("Y-m-d");
            $horaActual =  date("H:i:s");

            $obj_us = new UsuarioModel();
            $dtlleUsuario = $obj_us->detalle_Usuario_xID($idUSER);
            $datesPerson = "";
            if(is_array($dtlleUsuario)){
                $obj_per = new PersonaModel();
                $dtllePersona = $obj_per->detalle_Persona_xID($dtlleUsuario['id_per']);
                $datesPerson = $dtllePersona['nombres_per']." ".$dtllePersona['ape_pa_per'];
            }
            $val = 0;
            $datesLOG[0] = $idAlm;
            $datesLOG[1] = $fechaActual;
            $datesLOG[2] = $horaActual;
            $datesLOG[3] = $total;
            $datesLOG[4] = $idUSER;
            $datesLOG[5] = $datesPerson;

            $obj_alm = new AlmacenModel();
            $registerLog = $obj_alm->registrar_log_upload_Almacen($datesLOG);

            $cantSubidaFile = 0;
            $formatFecha = "";
            if($registerLog) {
                $val = 1;
                $cantSubidaFile = $total;
                $formatFecha = date('j') . " " . $obj_fn->texto_mes(date('n')) . " " . date("Y") . " " . date( 'H:i A');
            }

            $datos = array(
                'status'=> $val,
                'cant'  => $cantSubidaFile,
                'fecha' => $formatFecha
            );
            echo json_encode($datos);

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lst_Almacen_xServicio_All_JSON(){
        try {
            $obj_fn = new FuncionesModel();
            $idAlmacen = $_GET['idserv'];

            $obj_alm = new AlmacenModel();
            $lstAlmacenes = $obj_alm->lst_almacenes_All_xServicio($idAlmacen);

            $datos = array();
            if(is_array($lstAlmacenes)){
                foreach($lstAlmacenes as $almacen){

                    $estado = '<span class="text-danger-700">Suspendido</span>';
                    if((int)$almacen['condicion_alm']== 1){
                        $estado = '<span class="text-green-700">Activo</span>';
                    }

                    $semaforo = "";
                    if((int)$almacen['semaforo_alm']== 1){
                        $semaforo = '<i class="ti-check fz-20 text-success-600"></i>';
                    }

                    $row = array(
                        0 => "",
                        1 => $almacen['id_alm'],
                        2 => $almacen['titulo_alm'],
                        3 => $almacen['creadopor_alm'],
                        4 => $obj_fn->fechaHora_ENG_ESP($almacen['fechareg_alm']),
                        5 => $estado
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

    public function Update_Estado_Almacen_JSON(){
        try {
            $estado = $_POST['estd'];
            $arrayID = $_POST['id'];
            $val = 0;
            $acierto = 0;
            for($i=0; $i<sizeof($arrayID); $i++){
                $datesALM[0] = (int)$arrayID[$i];
                $datesALM[1] = (int)$estado;
                $obj_alm = new AlmacenModel();
                $update = $obj_alm->update_Estado_Almacen($datesALM);
                if($update){ $acierto++; }
            }
            if(sizeof($arrayID) == (int)$acierto){$val=1;}
            else if((int)$acierto > 1){$val = 2;}
            echo json_encode(array('status'=>$val));
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function delete_Almacen_JSON(){
        try {
            $arrayID = $_POST['id'];
            $obj_alm = new AlmacenModel();
            $val = 0;
            $acierto = 0;
            for($t=0; $t<sizeof($arrayID);$t++){
                $delete = $obj_alm->delete_Almacen_xID($arrayID[$t]);
                if($delete){$acierto++;}
            }

            if(sizeof($arrayID) == (int)$acierto){$val=1;}
            else if((int)$acierto > 1){$val = 2;}

            echo json_encode(array('status'=>$val));

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function loadCampos_NuevoAlmacen_Ajax(){
        try {
            $idus = $_GET['idus'];
            $idserv = (int)$_GET['idserv'];
            $obj_vl = new ValeModel();
            $lstVales = $obj_vl->lst_Vale_All_Activas();
            $obj_ubi = new UbigeoModel();
            $lstaUbigeo = $obj_ubi->listar_departamentos_all();?>
            <form id="formNewAlmacen" role="form" method="post">
                <input type="hidden" name="idus_alm" value="<?=$idus?>">
                <input type="hidden" name="idserv_alm" value="<?=$idserv?>">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title">
                                <h4 class="mb-0 text-info">
                                    Nuevo Almacén
                                </h4>
                                <ol class="breadcrumb mb-0 pl-0 pt-1 pb-0">
                                    <li class="breadcrumb-item text-muted">Registre un nuevo almacén.</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-25 card-shadow">
                        <div class="card-body">
                            <p>
                                Todos los campos descritos con (<code class="font-weight-bold text-danger-800">*</code>), son campos obligatorios.
                            </p>
                            <div class="row">
                                <div class="col-12" id="mensajes_actions_au"></div>
                            </div>
                            <div class="form-group row">
                                <label for="titulo_alm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Título almacén
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control input-md text-left"
                                           name="titulo_alm" maxlength="45" required placeholder="nombre dle almacén">
                                    <small class="help-block text-muted">Máximo 45 caracteres</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="des_alm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Descripción
                                </label>
                                <div class="col-sm-5">
                            <textarea class="form-control" name="des_alm" rows="4" cols="1" maxlength="300"
                                      placeholder="ingrese un detalle del almacén"></textarea>
                                    <small class="help-block text-muted">Máximo 300 caracteres</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="direccion_alm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Dirección
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control input-md text-left"
                                           name="direccion_alm" maxlength="120" required placeholder="digite una dirección" autocomplete="off" onkeyup="sga.funcion.mayus(this);">
                                    <small class="help-block text-muted">Máximo 120 caracteres</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="depa_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Departamento
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-4">
                                    <select name="depa_itm" id="depa_itm"  class="form-control selectedClassSearch" data-placeholder="Seleccione...">
                                        <option></option>
                                        <?php
                                        if(!is_null($lstaUbigeo)){
                                            foreach ($lstaUbigeo as $depa){?>
                                                <option value="<?=$depa['id_ubigeo']?>"><?=$depa['nombre_ubigeo']?></option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="prov_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Provincia
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-4">
                                    <select name="prov_itm" id="prov_itm" class="form-control selectedClassSearch" data-placeholder="Seleccione...">
                                        <option></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="dist_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Distrito
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-4">
                                    <select name="dist_itm" id="dist_itm" class="form-control selectedClassSearch" data-placeholder="Seleccione...">
                                        <option></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="chkNumValeGen" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Activar vale de entrega Retiro Correlativo Autogenerado
                                </label>
                                <div class="col-sm-5">
                                    <label class="control control-outline control--checkbox mb-7">
                                        <input type="checkbox" name="chkNumValeGen" value="1">
                                        <span class="control__indicator"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="chkValeCampo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Activar vale de campo Transferencia Interna
                                </label>
                                <div class="col-sm-5">
                                    <label class="control control-outline control--checkbox mb-7">
                                        <input type="checkbox" name="chkValeCampo" value="1">
                                        <span class="control__indicator"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="idowlcarousel" name="idowlcarousel" value="3"/>
                    <div class="row">
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-info btn-hover-transform mr-20">
                                <i class="ti-save position-left"></i>
                                Registrar
                            </button>
                            <button type="button" id="btnCancel" class="btn btn-light">
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            <br><br>
            <?php
        } catch (PDOException $e) {
           throw $e;
        }
    }

    public function registrar_Almacen_JSON(){
        try {
            date_default_timezone_set("America/Lima");
            setlocale(LC_TIME, 'es_PE.UTF-8');
            $obj_fn = new FuncionesModel();
            $IdServicio = (int)$_POST['idserv_alm'];
            $IdUsuario = (int)$obj_fn->encrypt_decrypt('decrypt',$_POST['idus_alm']);
            $obj_us = new UsuarioModel();
            $dtlleUsuario = $obj_us->detalle_Usuario_xID($IdUsuario);
            $datesPerson = "";
            if(is_array($dtlleUsuario)){
                $obj_per = new PersonaModel();
                $dtllePersona = $obj_per->detalle_Persona_xID($dtlleUsuario['id_per']);
                $datesPerson = $dtllePersona['nombres_per']." ".$dtllePersona['ape_pa_per'];
            }
            $trasnfInternaValeActive = 0;
            if(isset($_POST['chkValeCampo'])){ $trasnfInternaValeActive = 1; }
            $retiroNumeroValeAutoGen = 0;
            if(isset($_POST['chkNumValeGen'])){ $retiroNumeroValeAutoGen = 1; }
            $val = 0;
            $datesAlmach[0] = $IdServicio;
            $datesAlmach[1] = $obj_fn->quitar_caracteresEspeciales($_POST['titulo_alm']);
            $datesAlmach[2] = $obj_fn->quitar_caracteresEspeciales($_POST['des_alm']);
            $datesAlmach[3] = $IdUsuario;
            $datesAlmach[4] = $datesPerson;
            $datesAlmach[5] = date("Y-m-d H:i:s");
            $datesAlmach[6] = 3;//Formato vale 3
            $datesAlmach[7] = $retiroNumeroValeAutoGen;
            $datesAlmach[8] = $_POST['direccion_alm'];
            $datesAlmach[9]= (int)$_POST['depa_itm'];
            $datesAlmach[10]= (int)$_POST['prov_itm'];
            $datesAlmach[11]= (int)$_POST['dist_itm'];
            $datesAlmach[12]= $trasnfInternaValeActive;
            $obj_alm = new AlmacenModel();
            $insertID = $obj_alm->registrar_Almacen($datesAlmach);
            if((int)$insertID > 0 ) {
                $insertAC = $obj_alm->registrar_Almacen_Correlativo($insertID);
                if ($insertAC) {
                    $val = 1;
                }
            }
            echo json_encode(array('status'=>$val));
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function loadCampos_EditarAlmacen_Ajax(){
        try {
            $idAlm = (int)$_GET['id'];
            $obj_alm = new AlmacenModel();
            $dtlleAlmacen = $obj_alm->detalle_Almacen_xID($idAlm);
            $obj_vl = new ValeModel();
            $lstVales = $obj_vl->lst_Vale_All_Activas_exceptionActual($dtlleAlmacen['id_vale']);
            $dtlleVale = $obj_vl->detalle_Vale_xID($dtlleAlmacen['id_vale']);
            $obj_ubi = new UbigeoModel();
            $lstaUbigeo = $obj_ubi->listar_departamentos_all();
            $lstaProvincia = $obj_ubi->listar_hijos_IdPadre($dtlleAlmacen['departamento_alm']);
            $lstaDistrito = $obj_ubi->listar_hijos_IdPadre($dtlleAlmacen['provincia_alm']);
            ?>
            <form id="formEditAlmacen" role="form" method="post">
                <input type="hidden" name="idalm_updt" value="<?=$idAlm?>">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title">
                                <h4 class="mb-0 text-warning-600">
                                    Actualizar Almacén
                                </h4>
                                <ol class="breadcrumb mb-0 pl-0 pt-1 pb-0">
                                    <li class="breadcrumb-item text-muted">Actualice los camos consignados en el almacén.</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-25 card-shadow">
                        <div class="card-body">
                            <p>
                                Todos los campos descritos con (<code class="font-weight-bold text-danger-800">*</code>), son campos obligatorios.
                            </p>
                            <div class="row">
                                <div class="col-12" id="mensajes_actions_au"></div>
                            </div>
                            <div class="form-group row">
                                <label for="titulo_alm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Título almacén
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control input-md text-left"
                                           name="titulo_alm" maxlength="45" required placeholder="nombre dle almacén"
                                           value="<?=$dtlleAlmacen['titulo_alm']?>">
                                    <small class="help-block text-muted">Máximo 45 caracteres</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="des_alm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Descripción
                                </label>
                                <div class="col-sm-5">
                                    <textarea class="form-control" name="des_alm" rows="4" cols="1" maxlength="300"
                                      placeholder="ingrese un detalle del almacén"><?=$dtlleAlmacen['des_alm']?></textarea>
                                    <small class="help-block text-muted">Máximo 300 caracteres</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="direccion_alm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Dirección
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control input-md text-left"
                                           name="direccion_alm" maxlength="120" required placeholder="digite una dirección"
                                           autocomplete="off" onkeyup="sga.funcion.mayus(this);" value="<?=$dtlleAlmacen['direccion_alm']?>">
                                    <small class="help-block text-muted">Máximo 120 caracteres</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="depa_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Departamento
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-4">
                                    <select name="depa_itm" id="depa_itm"  class="form-control selectedClassSearch" data-placeholder="Seleccione...">
                                        <option></option>
                                        <?php
                                        if(!is_null($lstaUbigeo)){
                                            foreach ($lstaUbigeo as $depa){
                                                if((int)$depa['id_ubigeo'] == $dtlleAlmacen['departamento_alm']){?>
                                                    <option value="<?=$depa['id_ubigeo']?>" selected><?=$depa['nombre_ubigeo']?></option>
                                                <?php
                                                }
                                                else{?>
                                                    <option value="<?=$depa['id_ubigeo']?>"><?=$depa['nombre_ubigeo']?></option>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="prov_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Provincia
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-4">
                                    <select name="prov_itm" id="prov_itm" class="form-control selectedClassSearch" data-placeholder="Seleccione...">
                                        <option></option>
                                        <?php
                                        if(!is_null($lstaProvincia)){
                                            foreach ($lstaProvincia as $prov){
                                                if((int)$prov['id_ubigeo'] == $dtlleAlmacen['provincia_alm']){?>
                                                    <option value="<?=$prov['id_ubigeo']?>" selected><?=$prov['nombre_ubigeo']?></option>
                                                    <?php
                                                }
                                                else{?>
                                                    <option value="<?=$prov['id_ubigeo']?>"><?=$prov['nombre_ubigeo']?></option>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="dist_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Distrito
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-4">
                                    <select name="dist_itm" id="dist_itm" class="form-control selectedClassSearch" data-placeholder="Seleccione...">
                                        <option></option>
                                        <?php
                                        if(!is_null($lstaDistrito)){
                                            foreach ($lstaDistrito as $distri){
                                                if((int)$distri['id_ubigeo'] == $dtlleAlmacen['distrito_alm']){?>
                                                    <option value="<?=$distri['id_ubigeo']?>" selected><?=$distri['nombre_ubigeo']?></option>
                                                    <?php
                                                }
                                                else{?>
                                                    <option value="<?=$distri['id_ubigeo']?>"><?=$distri['nombre_ubigeo']?></option>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="chkNumValeGen" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Activar vale de entrega Retiro Correlativo Autogenerado
                                </label>
                                <div class="col-sm-5">
                                    <?php
                                    $checkedAutogen = "";
                                    if((int)$dtlleAlmacen['autogen_alm'] == 1){ $checkedAutogen = "checked"; }?>
                                    <label class="control control-outline control--checkbox mb-7">
                                        <input type="checkbox" name="chkNumValeGen" value="1" <?=$checkedAutogen?>>
                                        <span class="control__indicator"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="chkValeCampo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Activar vale de campo Transferencia Interna
                                </label>
                                <div class="col-sm-5">
                                    <?php
                                    $checkedValeCampo = "";
                                    if((int)$dtlleAlmacen['valecampo_alm'] == 1){ $checkedValeCampo = "checked"; }?>
                                    <label class="control control-outline control--checkbox mb-7">
                                        <input type="checkbox" name="chkValeCampo" value="1" <?=$checkedValeCampo?>>
                                        <span class="control__indicator"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="abrev_um" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Vale Entrega utilizado
                                </label>
                                <div class="col-sm-5">
                                    <img src="../assets/vales/<?=$dtlleVale['img_vale']?>" class="center-block img-circle" alt="vale" width="450">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 text-center">
                            <button type="submit" class="btn bg-warning-600 btn-hover-transform mr-20">
                                <i class="ti-save position-left"></i>
                                Actualizar
                            </button>
                            <button type="button" id="btnCancel" class="btn btn-light">
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            <br><br>
            <?php
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function update_Almacen_JSON(){
        try {
            $trasnfInternaValeActive = 0;
            if(isset($_POST['chkValeCampo'])){ $trasnfInternaValeActive = 1; }
            $retiroNumeroValeAutoGen = 0;
            if(isset($_POST['chkNumValeGen'])){ $retiroNumeroValeAutoGen = 1; }

            $obj_fn = new FuncionesModel();
            $val = 0;
            $datesAlmach[0] = (int)$_POST['idalm_updt'];
            $datesAlmach[1] = $obj_fn->quitar_caracteresEspeciales($_POST['titulo_alm']);
            $datesAlmach[2] = $obj_fn->quitar_caracteresEspeciales($_POST['des_alm']);
            $datesAlmach[3]= $retiroNumeroValeAutoGen;
            $datesAlmach[4]= $_POST['direccion_alm'];
            $datesAlmach[5]= (int)$_POST['depa_itm'];
            $datesAlmach[6]= (int)$_POST['prov_itm'];
            $datesAlmach[7]= (int)$_POST['dist_itm'];
            $datesAlmach[8]= $trasnfInternaValeActive;

            $obj_alm = new AlmacenModel();
            $updateALM = $obj_alm->update_Almacen($datesAlmach);
            if($updateALM) {
                $val = 1;
            }

            echo json_encode(array('status'=>$val));
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_Almacenes_Activos_JSON(){
        try {
            $id = (int)$_GET['idserv'];
            $obj_alm = new AlmacenModel();
            $lstAlmacen = $obj_alm->lst_almacenes_Activos_All_xServicio($id);

            $datos = array();
            if(is_array($lstAlmacen)){
                foreach($lstAlmacen as $almacen){
                    $row = array(
                        'id' => trim($almacen['id_alm']),
                        'texto' => trim($almacen['titulo_alm'])
                    );
                    array_push($datos, $row);
                }
            }

            echo json_encode($datos);
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function lista_Almacenes_Activos_xuserServicio_JSON(){
        try {
            $id = (int)$_GET['idsu'];
            $obj_us = new UsuarioModel();
            $dtlleUsuarioServicio = $obj_us->detalle_UsuarioServicio_xID($id);
            $obj_alm = new AlmacenModel();
            $lstAlmacen = $obj_alm->lst_almacenes_Activos_All_xServicio($dtlleUsuarioServicio['id_serv']);

            $datos = array();
            if(is_array($lstAlmacen)){
                foreach($lstAlmacen as $almacen){
                    $row = array(
                        'id' => trim($almacen['id_alm']),
                        'texto' => trim($almacen['titulo_alm'])
                    );
                    array_push($datos, $row);
                }
            }

            echo json_encode($datos);
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function delete_UsuarioAlmacen_JSON(){
        try {
            $idual = $_POST['idual'];
            $IdUsuario = $_POST['idus'];
            $obj_alm = new AlmacenModel();
            $val = 0;
            $delete = $obj_alm->delete_AlmacenUsuario_xID($idual);
            if($delete){$val = 1;}

            echo json_encode(array('status'=>$val,'id'=>$IdUsuario));

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_Almacen_xUsuario_JSON(){
        try {
            $idusuario = (int)$_GET['id'];
            $obj_alm = new AlmacenModel();
            $lstAlmacen = $obj_alm->lst_Almacenes_All_Asignados_xUsuario($idusuario);

            $datos = array();
            if(is_array($lstAlmacen)){
                $obj_serv = new ServicioModel();
                foreach($lstAlmacen as $almacen){
                    $tituloServicio = "";
                    $dtlleServicio = $obj_serv->detalle_Servicio_xID($almacen['id_serv']);
                    if(is_array($dtlleServicio)) {
                        $tituloServicio = $dtlleServicio['des_serv'];
                    }
                    $row = array(
                        'idual'=>$almacen['id_ual'],
                        'idus'=>$idusuario,
                        'servicio'=> $tituloServicio,
                        'estado'  => $almacen['condicion_ual'],
                        'titulo' => $almacen['titulo_alm']
                    );
                    array_push($datos, $row);
                }
            }

            echo json_encode($datos);
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function add_AlmacenUsuario_JSON(){
        try {
            date_default_timezone_set("America/Lima");
            setlocale(LC_TIME, 'es_PE.UTF-8');
            $IdUsuario = $_POST['iduserv'];
            $idServicioAlm = $_POST['sel_ServicioAlm'];
            $idAlmacen = $_POST['almacen'];
            $obj_alm = new AlmacenModel();
            $searchAlmacen = $obj_alm->busca_almacen_asignado($idServicioAlm,$idAlmacen);
            $val = 0;
            if(is_null($searchAlmacen)){
                $datesUserService[0] = $idServicioAlm;
                $datesUserService[1] = $_POST['almacen'];
                $datesUserService[2] = date("Y-m-d H:i:s");
                $insert = $obj_alm->add_UsuarioAlmacen($datesUserService);
                if($insert) {$val = 1;}
            }
            else if(is_array($searchAlmacen)){
                $val = 2;
            }
            echo json_encode(array('status'=>$val,'id'=>(int)$IdUsuario));

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function loadSelect_Almacen_Servicio_JSON(){
        try {
            $IdServicio = (int)$_GET['IdServicio'];
            $IdAlmacen = (int)$_GET['IdAlmacen'];
            $obj_alm = new AlmacenModel();
            $lstAlmacServicio = $obj_alm->lista_almacenes_Activos_xServicio_menosAlmActual($IdServicio,$IdAlmacen);
            $datos = array();
            if(!is_null($lstAlmacServicio)){
                foreach($lstAlmacServicio as $almacen){
                    $row = array(
                        'id' => trim($almacen['id_alm']),
                        'texto' => trim($almacen['titulo_alm'])
                    );
                    array_push($datos, $row);
                }
            }

            echo json_encode($datos);
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function loadSelect_Almacen_ServicioGerencia_JSON(){
        try {
            $IdServicio = (int)$_GET['IdServicio'];
            $IdAlmacen = (int)$_GET['IdAlmacen'];
            $obj_alm = new AlmacenModel();
            $lstAlmacServicio = $obj_alm->lista_almacenes_Activos_xServicio_menosAlmActual($IdServicio,$IdAlmacen);
            $datos = array();
            if(!is_null($lstAlmacServicio)){
                foreach($lstAlmacServicio as $almacen){
                    $row = array(
                        'id' => trim($almacen['id_alm']),
                        'texto' => trim($almacen['titulo_alm'])
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




