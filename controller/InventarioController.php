<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);
require_once '../model/InventarioModel.php';
require_once '../model/ServicioModel.php';
require_once '../model/AlmacenModel.php';
require_once '../model/MovimientoModel.php';
require_once '../model/UsuarioModel.php';
require_once '../model/PersonaModel.php';
require_once '../model/PerfilModel.php';
require_once '../model/FuncionesModel.php';
require_once '../model/UnidadMModel.php';
require_once '../assets/plugins/phpspreadsheet-1.17.1.0/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

$action = $_REQUEST["action"];
$controller = new InventarioController();
call_user_func(array($controller,$action));

class InventarioController {

    public function lst_Inventario_xServicio_All_JSON(){
        try {
            $obj_fn = new FuncionesModel();
            $idAlmacen = (int)$_GET['almacen'];
            $idUsuario = $obj_fn->encrypt_decrypt('decrypt',$_GET['idustk']);
            $accDelete = 0;
            $accEditar = 0;
            $accBaja = 0;
            $obj_us = new UsuarioModel();
            $dtlleUsuario = $obj_us->detalle_Usuario_xID($idUsuario);
            if (!is_null($dtlleUsuario)){
                $obj_pf = new PerfilModel();
                $dtllePerfil = $obj_pf->detalle_Perfil_xID($dtlleUsuario['id_perfil']);
                if(!is_null($dtllePerfil)){
                    $accDelete = $dtllePerfil['eliminar_perfil'];
                    $accEditar = $dtllePerfil['editar_perfil'];
                    $accBaja = $dtllePerfil['baja_perfil'];
                }
            }

            $obj_inv = new InventarioModel();
            $lstInventario = $obj_inv->listar_Inventario_xIDAlmacen_All($idAlmacen);

            $datos = array();
            if(!is_null($lstInventario)){
                foreach($lstInventario as $inventario){
                    $dtlleClasifica = $obj_inv->detalle_Clasificacion_xID($inventario['id_cla']);

                    $txtClasificacion = "";
                    if(!is_null($dtlleClasifica)){  $txtClasificacion = strtoupper(trim($dtlleClasifica['des_cla'])); }

                    $campoCodigo = "";
                    $campoInfo = '<a id="btnDetailInventary" class="cursor-pointer float-left text-hover-primary mr-7" title="Click para ver detalle" data-id="'.$inventario['id_inv'].'"><i class="ti-info-alt"></i></a>';
                    if(!empty($inventario['cod_inv'])){
                        $campoCodigo = $inventario['cod_inv'];
                    }

                    $btnEditar = "";
                    if($accEditar == 1) {
                        $btnEditar = '<a class="cursor-pointer text-hover-primary" id="editarItem_Btn" data-id="' . $inventario['id_inv'] . '" title="Editar"><i class="f24 opacity-7 ti-pencil-alt"></i></a>';
                    }
                    $btnEliminar = "";
                    if($accDelete == 1 ) {
                        $obj_mov = new MovimientoModel();
                        $exMovItem = $obj_mov->existe_MovimientoDetalle_xIdInventario($inventario['id_inv']);
                        if((int)$exMovItem['nreg'] == 0) {
                            $btnEliminar = '<a class="cursor-pointer ml-15 text-hover-danger"  id="deleteItem_Btn" data-id="' . $inventario['id_inv'] . '" title="Eliminar"><i class="f24 opacity-7 ti-trash"></i></a>';
                        }
                    }

                    $btnBaja = "";
                    if($accBaja == 1) {
                        $btnBaja = '<a class="cursor-pointer ml-15 text-hover-waning"  id="bajaItem_Btn" data-id="'.$inventario['id_inv'].'" title="Baja"><i class="f24 opacity-7  icon-dislike"></i></a>';
                    }


                    $row = array(
                        0 => $inventario['id_inv'],
                        1 => $campoInfo.$campoCodigo,
                        2 => $inventario['des_inv'],
                        3 => $inventario['um_inv'],
                        4 => $inventario['nroparte_inv'],
                        5 => $inventario['cactivo_inv'],
                        6 => $inventario['cinventario_inv'],
                        7 => $inventario['cmapel_inv'],
                        8 => $inventario['conu_inv'],
                        9 => (int)$inventario['cant_inv'],
                        10=> $txtClasificacion,
                        11=> $btnEditar.$btnBaja.$btnEliminar
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

    public function validar_existenciaInventario_JSON(){
        try {
            $idAlmacen = (int)$_GET['IdAlmacen'];
            $obj_inv = new InventarioModel();
            $nInventario = $obj_inv->nRegistros_Inventario_xAlmacen_All($idAlmacen);

            $nRegisters = 0;
            if(!is_null($nInventario)){ $nRegisters = (int)$nInventario['ncount']; }

            echo json_encode(array('valor'=>$nRegisters));

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_ubicacion_Inventario_xIDAlm(){
        try {
            $idAlm = (int)$_GET['id'];
            $obj_alm = new InventarioModel();
            $lstUbicacion = $obj_alm->lst_ubicacion_Inventario_xIdAlm($idAlm);

            $datos = array();
            if(is_array($lstUbicacion)){
                foreach($lstUbicacion as $ubica){

                    $row = array(
                        'id' => trim($ubica['ubic_inv']),
                        'texto' => trim($ubica['ubic_inv'])
                    );
                    array_push($datos, $row);
                }
            }

            echo json_encode($datos);

        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function loadCampos_masCodigoExport_Ajax(){
        try {
            $inputVal = $_GET['inputv'];?>
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h4 class="modal-title text-white">
                            Mas Códigos
                        </h4>
                        <button type="button" class="close text-white" data-dismiss="modal">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12" id="msj_ajax_export"></div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <input type="text" class="form-control" placeholder="Pegar códigos" id="pasteCodigos_exp">
                                <div class="table-responsive" style="height: 300px; overflow-y:scroll" id="tblPaste_codExport">
                                    <table id="TblMasCOD_Export" class="table table-bordered" cellpadding="0" cellspacing="0" width="100%">
                                        <thead>
                                        <tr>
                                            <th class="text-center" width="30"></th>
                                            <th class="text-left">Código</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        if(!empty($inputVal) && strlen($inputVal)>0){
                                            $arayVal = explode(",",$inputVal);
                                            for($t=0; $t<sizeof($arayVal);$t++){?>
                                                <tr>
                                                    <td class="text-center"><?=($t+1)?></td>
                                                    <td class="text-center p-0">
                                                        <input type="text" class="form-control inputmOrd" name="masCodigo_exp[]" value="<?=$arayVal[$t]?>">
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        else{?>
                                            <tr>
                                                <td class="text-center">1</td>
                                                <td class="text-center p-0">
                                                    <input type="text" class="form-control inputmOrd" placeholder="" name="masCodigo_exp[]">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">2</td>
                                                <td class="text-center p-0">
                                                    <input type="text" class="form-control inputmOrd" placeholder="" name="masCodigo_exp[]">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">3</td>
                                                <td class="text-center p-0">
                                                    <input type="text" class="form-control inputmOrd" placeholder="" name="masCodigo_exp[]">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">4</td>
                                                <td class="text-center p-0">
                                                    <input type="text" class="form-control inputmOrd" placeholder="" name="masCodigo_exp[]">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">5</td>
                                                <td class="text-center p-0">
                                                    <input type="text" class="form-control inputmOrd" placeholder="" name="masCodigo_exp[]">
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="no-margin">
                    <div class="modal-footer">
                        <div class="btn-group btn-block" role="group" aria-label="opciones">
                            <button type="button" class="btn btn-outline-secondary btn-block mt-0" title="Ejecutar" id="btnAceptar_exp">
                                <i class="far fa-check-circle"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-block  mt-0" title="Borrar valores" id="btnDelete_exp">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-block  mt-0" data-dismiss="modal" title="Cerrar" id="btnCloseModal_exp">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function loadCampos_NuevoItem_Ajax(){
        try {
            $idus = $_GET['idus'];
            $idsu = (int)$_GET['idsu'];
            $idalm = (int)$_GET['idalm'];
            $obj_serv = new ServicioModel();
            $dtlleServUsuario = $obj_serv->detalle_ServicioUsuario_xIDSU($idsu);
            $IdServicio = $dtlleServUsuario['id_serv'];
            $obj_alm = new AlmacenModel();
            $dtlleAlmacen = $obj_alm->detalle_Almacen_xID($idalm);
            $dtlleCorrelativo = $obj_alm->detalle_correlativo_Almacen($idalm);
            $numCorrelativo = "";
            if(is_array($dtlleCorrelativo)){
                $numCorrelativo = "IN-".$IdServicio."-".str_pad((int)$dtlleCorrelativo['val_alc'] + 1,6,"0",STR_PAD_LEFT);
            }
            $obj_inv = new InventarioModel();
            $lstClasificacion = $obj_inv->lista_Clasificacion_Activos_All();
            $obj_um = new UnidadMModel();
            $lstUM = $obj_um->listar_unidadM_All();
            ?>
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="page-title">
                            <h4 class="mb-0 text-brown-800 font-weight-bold">
                                Ingreso : <code class="text-danger-800"><?=$dtlleAlmacen['titulo_alm']?></code>
                            </h4>
                            <ol class="breadcrumb mb-0 pl-0 pt-1 pb-0">
                                <li class="breadcrumb-item text-muted">Registre un nuevo ítem para el almacén seleccionado.</li>
                            </ol>
                        </div>
                    </div>
                </div>
                <div class="card card-shadow mb-4">
                    <ul class="nav nav-fill mb-4 nav-pills" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#tab-nreg">
                                <i class="ti-file fz-30"></i>
                                <div class="card-title fz-15 mb-0">Nuevo registro</div>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#tab-masivo">
                                <i class="ti-import fz-30"></i>
                                <div class="card-title fz-15 mb-0">Registro masivo</div>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab-nreg" role="tabpanel">
                        <form id="formNewItem" role="form" method="post">
                            <input type="hidden" name="idusitm_tk" value="<?=$idus?>">
                            <input type="hidden" id="idalm_i" name="idalm_i" value="<?=$idalm?>">
                            <input type="hidden" id="fechareg_i" name="fechareg_i" value="<?=date("Y-m-d")?>">
                            <input type="hidden" id="idalc" name="idalc" value="<?=$dtlleCorrelativo['id_alc']?>">
                            <input type="hidden" id="valalc" name="valalc" value="<?=(int)$dtlleCorrelativo['val_alc']?>">
                            <input type="hidden" id="nroingreso_i" name="nroingreso_i" value="<?=$numCorrelativo?>">
                            <br>
                            <div class="row">
                                <div class="col-lg-6 col-md-6 mb-10">
                                    <div class="card mb-4 card-shadow">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-3">
                                                    <span class="bg-primary rounded-circle text-center wb-icon-box">
                                                        <i class="icon-notebook text-light f24"></i>
                                                    </span>
                                                </div>
                                                <div class="col-9">
                                                    <h3 class="mt-1 mb-0" id="infoCorrelativo"><?=$numCorrelativo?></h3>
                                                    <p class="f12 mb-0">Número Transacción</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 mb-10">
                                    <div class="card mb-4 card-shadow">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-3">
                                                    <span class="bg-warning rounded-circle text-center wb-icon-box">
                                                        <i class="icon-calendar text-light f24"></i>
                                                    </span>
                                                </div>
                                                <div class="col-9">
                                                    <h3 class="mt-1 mb-0"><?=date("d/m/Y H:i")?></h3>
                                                    <p class="f12 mb-0">Fecha y hora registro</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card card-shadow mb-20">
                                <div class="card-header bg-secondary-light-5">
                                    <h4 class="card-title font-weight-bold">Datos Generales</h4>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">
                                        Todos los campos descritos con <code class="font-weight-bold">(*)</code>, son campos obligatorios.
                                    </p>
                                    <div class="form-group row">
                                        <label for="clasifica_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                            Clasificación
                                            <span class="text-danger font-weight-bold">*</span>
                                        </label>
                                        <div class="col-sm-3">
                                            <select id="clasifica_itm" name="clasifica_itm" class="form-control" required>
                                                <option value="" selected>Seleccione...</option>
                                                <?php
                                                if(!is_null($lstClasificacion)){
                                                    foreach ($lstClasificacion as $clasif){?>
                                                        <option value="<?=$clasif['id_cla']?>"><?=$clasif['des_cla']?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                            Código material
                                            <span class="text-danger font-weight-bold">*</span>
                                        </label>
                                        <div class="col-sm-3">
                                            <small class="form-text text-danger">Realice la validación del código</small>
                                            <div class="input-group">
                                                <input type="text" class="form-control input-md text-left"
                                                       name="codigo_itm" id="codigo_itm" maxlength="12" required
                                                       placeholder="código" autocomplete="off">
                                                <div class="input-group-append">
                                                    <a class="btn btn-outline-secondary text-hover-white" title="validar material" id="btnValidate_codMat">
                                                        <i class="ti-search"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">Máximo 12 carácteres</small>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                            Cantidad/Ingreso
                                            <span class="text-danger font-weight-bold">*</span>
                                        </label>
                                        <div class="col-sm-3">
                                            <input type="text" class="form-control input-md text-left"
                                                   name="cant_itm" placeholder="ingrese valor" required
                                                   onkeyup="if(!Number(this.value)){this.value = ''; }">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                            Descripción
                                            <span class="text-danger font-weight-bold">*</span>
                                        </label>
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control input-md text-left"
                                                   name="des_itm" maxlength="45" required
                                                   placeholder="describa el ítem" autocomplete="off">
                                            <small class="form-text text-muted">Máximo 45 carácteres</small>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                            Unidad medida
                                            <span class="text-danger font-weight-bold">*</span>
                                        </label>
                                        <div class="col-sm-3">
                                            <select name="um_itm" class="form-control" required>
                                                <option value="">Seleccione...</option>
                                                <?php
                                                if(!is_null($lstUM)){
                                                    foreach ($lstUM as $unidadM){?>
                                                            <option value="<?=$unidadM['cod_um']?>"><?=$unidadM['des_um']?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                            Número de parte/Serie
                                            <span class="text-danger font-weight-bold">*</span>
                                        </label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control input-md text-left"
                                                   name="nparte_itm" maxlength="30" required
                                                   placeholder="ingrese valor" autocomplete="off">
                                            <small class="form-text text-muted">Máximo 30 carácteres</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="camposActivo"></div>

                            <div class="card card-shadow mb-20">
                                <div class="card-header bg-secondary-light-5">
                                    <h4 class="card-title font-weight-bold">Datos Complementarios</h4>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">
                                        Todos los campos a continuación son de carácter opcional.
                                    </p>
                                    <div class="form-group row">
                                        <label for="guia_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                            Número de Guía
                                        </label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control input-md text-left"
                                                   name="guia_itm" id="guia_itm" maxlength="16" autocomplete="off"
                                                   placeholder="ingrese valor">
                                            <small class="form-text text-muted">Máximo 16 carácteres</small>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                            Marca
                                        </label>
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control input-md text-left"
                                                   name="marca_itm" maxlength="45" autocomplete="off"
                                                   placeholder="ingrese valor">
                                            <small class="form-text text-muted">Máximo 45 carácteres</small>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="ocompra_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                            Código Activo
                                        </label>
                                        <div class="col-sm-3">
                                            <input type="text" class="form-control input-md text-left"
                                                   name="cact_itm" maxlength="20" autocomplete="off"
                                                   placeholder="ingrese código">
                                            <small class="form-text text-muted">Máximo 20 carácteres</small>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="ocompra_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                            Código Inventario
                                        </label>
                                        <div class="col-sm-3">
                                            <input type="text" class="form-control input-md text-left"
                                                   name="cinvent_itm" maxlength="20" autocomplete="off"
                                                   placeholder="ingrese código">
                                            <small class="form-text text-muted">Máximo 20 carácteres</small>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="ocompra_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                            MAPEL
                                        </label>
                                        <div class="col-sm-3">
                                            <input type="text" class="form-control input-md text-left"
                                                   name="mapel_itm" maxlength="20" autocomplete="off"
                                                   placeholder="ingrese código">
                                            <small class="form-text text-muted">Máximo 20 carácteres</small>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="ocompra_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                            ONU
                                        </label>
                                        <div class="col-sm-3">
                                            <input type="text" class="form-control input-md text-left"
                                                   name="onu_itm" maxlength="20" autocomplete="off"
                                                   placeholder="ingrese código">
                                            <small class="form-text text-muted">Máximo 20 carácteres</small>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="fins_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                            Fecha última calibración
                                        </label>
                                        <div class="col-sm-3">
                                            <input type="text" class="form-control input-md text-left inputFecha"
                                                   name="fcalibra_itm" maxlength="10" placeholder="**/**/****"
                                                   autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                            Frecuencia de calibración
                                        </label>
                                        <div class="col-sm-3">
                                            <input type="text" class="form-control input-md text-left"
                                                   name="freccal_itm" placeholder="ingrese valor"
                                                   onkeyup="if(!Number(this.value)){this.value = ''; }">
                                            <small class="form-text text-muted">Número en meses</small>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="frecepvale_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                            Fecha recepción por encargado almacén
                                        </label>
                                        <div class="col-sm-3">
                                            <input type="text" class="form-control input-md text-left inputFecha"
                                                   name="frecepvale_itm" maxlength="10" placeholder="**/**/****">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="obs_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                            Observaciones
                                        </label>
                                        <div class="col-sm-8">
                                            <textarea class="form-control" name="obs_itm" maxlength="300"
                                                      rows="4" cols="1" placeholder="detalle alguna observación encontrada"></textarea>
                                            <small class="form-text text-muted">Máximo 300 carácteres</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-12 text-center">
                                    <button type="button" id="btnCancel" class="btn btn-light btn-lg mr-20">
                                        Cancelar
                                    </button>
                                    <button type="submit" class="btn btn-info btn-hover-transform btn-lg" id="btnRegisterView" style="display: none">
                                        <i class="ti-save position-left"></i>
                                        Registrar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane" id="tab-masivo" role="tabpanel">
                        <div class="row">
                            <div class="col-12 mb-20 mt-20" id="div_FileInput" style="display:block">
                                <h5 class="text-center">Importar Ingresos Almacén Masivo</h5>
                                <p class="text-center">Adjunte el archivo con los datos contemplados en la plantilla para la carga masiva de repuestos.</p>
                                <div class="card card-shadow">
                                    <div class="card-body">
                                        <form id="form_Viewtable_listDatos" enctype="multipart/form-data">
                                            <input type="hidden" name="tipoload" id="tipoload" value="2">
                                            <div class="row">
                                                <div class="col-12">
                                                    <input type="file" class="file" id="filedata_import" name="filedata_import" required
                                                           data-show-preview="false" data-show-upload="true"
                                                           data-show-caption="true" data-show-remove="true"
                                                           data-show-cancel="false"
                                                           data-browse-Label="Examinar"
                                                           data-remove-Label="Eliminar"
                                                           data-upload-Label="Visualizar"
                                                           data-browse-class="btn waves-effect waves-light btn-outline-secondary cursor-pointer"
                                                           data-upload-class="btn waves-effect waves-light btn-outline-info cursor-pointer"
                                                           data-remove-class="btn waves-effect waves-light btn-outline-danger cursor-pointer">
                                                    <span class="help-block">
                                                        <small>Formatos permitidos [<code>xls, xlsx</code>].</small>
                                                    </span>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="card-footer text-right">
                                        <button type="button" id="btnCancel" class="btn btn-light mr-20">Cancelar</button>
                                        <a class="btn bg-success-0 btn-hover-transform"
                                           href="../assets/formato/Plantilla-Datos.xlsx"
                                           download="Plantilla-ingreso-masivo.xlsx">
                                            <i class="ti-download position-left"></i>
                                            Descargar Plantilla
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mb-20 mt-20" id="divResponse_All" style="display:none"></div>
                        </div>
                    </div>
                </div>
            </div>
            <br><br><br>

            <?php
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function registrar_Item_JSON(){
        try {
            date_default_timezone_set("America/Lima");
            setlocale(LC_TIME, 'es_PE.UTF-8');
            $obj_fn = new FuncionesModel();
            $idalm = (int)$_POST['idalm_i'];
            $codigoMaterial = trim($_POST['codigo_itm']);
            $idalc = (int)$_POST['idalc'];
            $valalc = (int)$_POST['valalc'];
            $idus = $obj_fn->encrypt_decrypt('decrypt',$_POST['idusitm_tk']);

            $desItem = $obj_fn->quitar_caracteresEspeciales($_POST['des_itm']);
            $nroParte = $_POST['nparte_itm'];
            $cantidad = $_POST['cant_itm'];

            //Datos de Generales
            $fechavale = "0000-00-00";
            if(isset($_POST['frecepvale_itm'])){ $fechavale = $obj_fn->fecha_ESP_ENG($_POST['frecepvale_itm']); }
            $nroGuia = "";
            if(!empty($_POST['guia_itm'])){ $nroGuia = trim($_POST['guia_itm']); }
            $fechaUltcalibra = "0000-00-00";
            if(isset($_POST['fcalibra_itm'])){ $fechaUltcalibra = $obj_fn->fecha_ESP_ENG($_POST['fcalibra_itm']); }
            $frecCalibracion = "";
            if(!empty($_POST['freccal_itm'])){ $frecCalibracion = (int)$_POST['freccal_itm']; }
            $cActivo = "";
            if(!empty($_POST['cact_itm'])){ $cActivo = trim($_POST['cact_itm']); }
            $cInventario = "";
            if(!empty($_POST['cinvent_itm'])){ $cInventario = trim($_POST['cinvent_itm']); }
            $cMapel = "";
            if(!empty($_POST['mapel_itm'])){ $cMapel = trim($_POST['mapel_itm']); }
            $cONU = "";
            if(!empty($_POST['onu_itm'])){ $cONU = trim($_POST['onu_itm']); }
            $marca = "";
            if(!empty($_POST['marca_itm'])){ $marca = trim($_POST['marca_itm']); }

            //Datos de depreciacion
            $fechaDepreciacion= "0000-00-00";
            if(isset($_POST['fInicialDepre_itm'])){ $fechaDepreciacion = $obj_fn->fecha_ESP_ENG($_POST['fInicialDepre_itm']); }
            $costoActivo = 0;
            if(isset($_POST['costoActivo_itm'])){ $costoActivo = (float)$_POST['costoActivo_itm']; }
            $frecDepreciacion = 0;
            if(isset($_POST['frecDepre_itm'])){ $frecDepreciacion = (int)$_POST['frecDepre_itm']; }
            $valDepreciacion = 0;
            if(isset($_POST['valMensual_itm'])){ $valDepreciacion = (float)$_POST['valMensual_itm']; }

            $IdClasificacion = (int)$_POST['clasifica_itm'];
            $unidadMedida = trim($_POST['um_itm']);
            $observaciones = trim($_POST['obs_itm']);

            $datesTAB[0] = $idalm;
            $datesTAB[1] = $codigoMaterial;
            $datesTAB[2] = $_POST['cant_itm'];
            $datesTAB[3] = $desItem;
            $datesTAB[4] = $unidadMedida;
            $datesTAB[5] = $nroParte;
            $datesTAB[6] = $marca;
            $datesTAB[7] = $observaciones;
            $datesTAB[8] = $idus;
            $datesTAB[9] = date("Y-m-d H:i:s");
            $datesTAB[10]= $fechavale;
            $datesTAB[11]= $IdClasificacion;
            $datesTAB[12]= $nroGuia;
            $datesTAB[13]= $fechaUltcalibra;
            $datesTAB[14]= $frecCalibracion;
            $datesTAB[15]= $fechaDepreciacion;
            $datesTAB[16]= $costoActivo;
            $datesTAB[17]= $frecDepreciacion;
            $datesTAB[18]= $valDepreciacion;
            $datesTAB[19]= $cActivo;
            $datesTAB[20]= $cInventario;
            $datesTAB[21]= $cMapel;
            $datesTAB[22]= $cONU;

            $val = 0;
            $message = "Error al realizar el registro.";
            $obj_inv = new InventarioModel();

            $datesSearch[0] = $idalm;
            $datesSearch[1] = trim($codigoMaterial);
            /*$datesSearch[2] = trim($nroParte);
            $datesSearch[3] = trim($cActivo);*/
            $dtlleItem = $obj_inv->busca_existencia_Item_xCodMaterial($datesSearch);
            if(is_null($dtlleItem)){
                $inserInventID = $obj_inv->registrar_Item_lastID($datesTAB);
                if($inserInventID > 0){
                    $val = 1;
                    $message = "Ítem registrado satisfactoriamente";
                    //GENERAMOS EL REGISTRO DEL MOVIMIENTO
                    $obj_us = new UsuarioModel();
                    $dtlleUsuario = $obj_us->detalle_Usuario_xID($idus);
                    $textPersona = "";
                    if (is_array($dtlleUsuario)) {
                        $obj_per = new PersonaModel();
                        $dtllePersona = $obj_per->detalle_Persona_xID($dtlleUsuario['id_per']);
                        if (is_array($dtllePersona)) {
                            $textPersona = $dtllePersona['ape_pa_per'] . " " . $dtllePersona['nombres_per'];
                        }
                    }

                    $datesMOV[0] = "IN";
                    $datesMOV[1] = $idalm; //Almacen inicio
                    $datesMOV[2] = ""; //unidad
                    $datesMOV[3] = ""; //orden Mantto
                    $datesMOV[4] = $idalm;//Almacen destino
                    $datesMOV[5] = $textPersona; //Recibido
                    $datesMOV[6] = $textPersona; //Nro DNI
                    $datesMOV[7] = $textPersona; //Autorizado
                    $datesMOV[8] = $observaciones; //Observaciones
                    $datesMOV[9] = ""; //Documento
                    $datesMOV[10]= ""; //motivo
                    $datesMOV[11]= $_POST['fechareg_i']; //Fecha
                    $datesMOV[12]= $_POST['nroingreso_i']; //Nro Transac
                    $datesMOV[13]= $idus; //Id Usuario
                    $datesMOV[14]= date("Y-m-d H:i:s"); //Fecha sistema
                    $datesMOV[15]= ""; //Entregado
                    $datesMOV[16]= ""; //NRO VALE
                    $datesMOV[17]= 0;  //idMoV ref
                    $datesMOV[18]= ""; //Area Operativa

                    $obj_mov = new MovimientoModel();
                    $insertID = $obj_mov->registrar_Movimiento_Item_lastID($datesMOV);
                    if ((int)$insertID > 0) {
                        $datesDEMOV[0] = $insertID;
                        $datesDEMOV[1] = $inserInventID;
                        $datesDEMOV[2] = $codigoMaterial;
                        $datesDEMOV[3] = $desItem;
                        $datesDEMOV[4] = $nroParte;
                        $datesDEMOV[5] = $cantidad;
                        $datesDEMOV[6] = $cantidad;
                        $datesDEMOV[7] = $IdClasificacion;
                        $datesDEMOV[8] = $unidadMedida;
                        $datesDEMOV[9] = $marca;
                        $datesDEMOV[10]= $cActivo;
                        $datesDEMOV[11]= $cInventario;
                        $datesDEMOV[12]= $cMapel;
                        $datesDEMOV[13]= $cONU;
                        $insertDetalle = $obj_mov->registrar_Movimiento_Item_Detalle($datesDEMOV);
                        if ($insertDetalle) {
                            //Actualizamos incrementador correlativo
                            $datesCorrel[0] = $idalc;
                            $datesCorrel[1] = $valalc + 1;
                            $obj_alm = new AlmacenModel();
                            $obj_alm->actualizar_Correlativo_Almacen($datesCorrel);
                        }
                    }
                }
            }
            else{
                $val = 2;
                $message = "El ïtem a ingresar ya existe en el almacen, No es posible su registro";
            }
            echo json_encode(array('status'=>$val, 'message'=>$message));

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function loadCampos_EditarItem_Ajax(){
        try {
            $idus = $_GET['idus'];
            $idinv = $_GET['idinv'];
            $obj_inv = new InventarioModel();
            $dtlleItem= $obj_inv->detalle_Item_xID($idinv);
            $obj_fn = new FuncionesModel();

            $cantSplit = explode(".",$dtlleItem['cant_inv']);
            $stock = number_format($dtlleItem['cant_inv'], 2);
            if($cantSplit[1] == "00"){
                $stock = (int)$dtlleItem['cant_inv'];
            }

            $lstClasificacion = $obj_inv->lista_Clasificacion_Activos_All();
            $obj_um = new UnidadMModel();
            $lstUM = $obj_um->listar_unidadM_All();
            ?>
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="page-title">
                            <h4 class="mb-0 text-warning-0">
                                Actualizar Ítem
                            </h4>
                            <ol class="breadcrumb mb-0 pl-0 pt-1 pb-0">
                                <li class="breadcrumb-item text-muted">Actualice los datos del ítem.</li>
                            </ol>
                        </div>
                    </div>
                </div>
                <div id="divchangeCodigo"></div>
                <form id="formEditItem" role="form" method="post">
                    <input type="hidden" name="idusitm_tk" value="<?=$idus?>">
                    <input type="hidden" name="idinv_i" value="<?=$idinv?>">
                    <input type="hidden" name="idalm_i" value="<?=$dtlleItem['id_alm']?>">

                    <div class="card card-shadow mb-20">
                        <div class="card-header bg-secondary-light-5">
                            <h4 class="card-title font-weight-bold">Datos Generales</h4>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">
                                Todos los campos descritos con <code class="font-weight-bold">(*)</code>, son campos obligatorios.
                            </p>
                            <div class="form-group row">
                                <label for="clasifica_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Clasificación
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-3">
                                    <select id="clasifica_itm" name="clasifica_itm" class="form-control" required>
                                        <option value="">Seleccione...</option>
                                        <?php
                                        if(!is_null($lstClasificacion)){
                                            foreach ($lstClasificacion as $clasif){
                                                if((int)$clasif['id_cla'] == $dtlleItem['id_cla']){?>
                                                    <option value="<?=$clasif['id_cla']?>" selected><?=$clasif['des_cla']?></option>
                                                    <?php
                                                }
                                                else{?>
                                                    <option value="<?=$clasif['id_cla']?>"><?=$clasif['des_cla']?></option>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <input type="hidden" name="codigo_itm" id="codigo_itm" value="<?=$dtlleItem['cod_inv']?>">
                                <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Código material
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-3">
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control input-md text-left" disabled
                                               placeholder="código" id="cod_temp" value="<?=$dtlleItem['cod_inv']?>">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="btnChangeCodMate"
                                                    data-idalm="<?=$dtlleItem['id_alm']?>" data-codmat="<?=$dtlleItem['cod_inv']?>"
                                                    data-idinv="<?=$idinv?>">
                                                <i class="ti-pencil"></i>
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Cantidad/Ingreso
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-3">
                                    <input type="text" class="form-control input-md text-left"
                                           name="cant_itm" placeholder="ingrese valor" required
                                           onkeyup="if(!Number(this.value)){this.value = ''; }"
                                           value="<?=$stock?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Descripción
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control input-md text-left"
                                           name="des_itm" maxlength="45" required
                                           placeholder="describa el ítem" value="<?=$dtlleItem['des_inv']?>">
                                    <small class="form-text text-muted">Máximo 45 carácteres</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Unidad medida
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-3">
                                    <select name="um_itm" class="form-control" required>
                                        <option value="">Seleccione...</option>
                                        <?php
                                        if(!is_null($lstUM)){
                                            foreach ($lstUM as $unidadM){
                                                if(trim($unidadM['cod_um']) == trim(mb_strtoupper($dtlleItem['um_inv'],"UTF-8"))){?>
                                                    <option value="<?=$unidadM['cod_um']?>" selected><?=$unidadM['des_um']?></option>
                                                    <?php
                                                }
                                                else{?>
                                                    <option value="<?=$unidadM['cod_um']?>"><?=$unidadM['des_um']?></option>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Ubicación
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control input-md text-left"
                                           name="ubic_itm" maxlength="45" required
                                           placeholder="ingrese valor" value="<?=$dtlleItem['ubic_inv']?>">
                                    <small class="form-text text-muted">Máximo 45 carácteres</small>
                                </div>
                            </div>
                            <div class="row">
                                <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Número de parte/Serie
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-md text-left"
                                           name="nparte_itm" maxlength="30" required
                                           placeholder="ingrese valor" value="<?=$dtlleItem['nroparte_inv']?>">
                                    <small class="form-text text-muted">Máximo 30 carácteres</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="camposActivo">
                    <?php
                        if((int)$dtlleItem['id_cla'] == 1){?>
                            <div class="card card-shadow mb-20">
                                <div class="card-header bg-secondary-light-5">
                                    <h4 class="card-title font-weight-bold">Datos Depreciación Activo</h4>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">
                                        Todos los campos descritos con <code class="font-weight-bold">(*)</code>, son campos obligatorios.
                                    </p>
                                    <div class="form-group row">
                                        <label for="fInicialDepre_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                            Fecha Inicial Depreciación
                                            <span class="text-danger font-weight-bold">*</span>
                                        </label>
                                        <div class="col-sm-3">
                                            <input type="text" class="form-control input-md text-left inputFecha" autocomplete="off"
                                                   name="fInicialDepre_itm" id="fInicialDepre_itm" maxlength="10" placeholder="**/**/****" required
                                                   value="<?=$obj_fn->fecha_ENG_ESP($dtlleItem['fechadepre_inv'])?>">
                                            <small class="form-text text-muted">Indique la fecha de inicio para el cálculo de la depreciación</small>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                            Costo del Activo
                                            <span class="text-danger font-weight-bold">*</span>
                                        </label>
                                        <div class="col-sm-3">
                                            <input type="number" class="form-control input-md text-left" min="0" step="0.01"
                                                   name="costoActivo_itm" required placeholder="valor" autocomplete="off"
                                                   value="<?=$dtlleItem['costo_act_inv']?>">
                                            <small class="form-text text-muted">Valor actual del activo</small>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                            Frecuencia Depreciación del Activo
                                            <span class="text-danger font-weight-bold">*</span>
                                        </label>
                                        <div class="col-sm-3">
                                            <input type="number" class="form-control input-md text-left" min="0" step="1"
                                                   name="frecDepre_itm" required placeholder="valor" autocomplete="off"
                                                   value="<?=number_format($dtlleItem['frec_depre_act_inv'])?>">
                                            <small class="form-text text-muted">Número de meses</small>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                            Valor Depreciación Mensual
                                            <span class="text-danger font-weight-bold">*</span>
                                        </label>
                                        <div class="col-sm-3">
                                            <input type="number" class="form-control input-md text-left" min="0" step="0.01"
                                                   name="valMensual_itm" required placeholder="valor" autocomplete="off"
                                                   value="<?=number_format($dtlleItem['val_depre_mensual_inv'],2)?>">
                                            <small class="form-text text-muted">Valor a descontar mes a mes</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    ?>
                    </div>

                    <div class="card card-shadow mb-20">
                        <div class="card-header bg-secondary-light-5">
                            <h4 class="card-title font-weight-bold">Datos Complementarios</h4>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">
                                Todos los campos a continuación son de carácter opcional.
                            </p>
                            <div class="form-group row">
                                <label for="guia_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Número de Guía
                                </label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control input-md text-left"
                                           name="guia_itm" id="guia_itm" maxlength="16" autocomplete="off"
                                           placeholder="ingrese valor" value="<?=$dtlleItem['nguia_inv']?>">
                                    <small class="form-text text-muted">Máximo 16 carácteres</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="ocompra_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Orden de compra
                                </label>
                                <div class="col-sm-3">
                                    <input type="text" class="form-control input-md text-left"
                                           name="ocompra_itm" maxlength="20" autocomplete="off"
                                           placeholder="ingrese valor" value="<?=$dtlleItem['ordencompra_inv']?>">
                                    <small class="form-text text-muted">Máximo 20 carácteres</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Marca
                                </label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control input-md text-left"
                                           name="marca_itm" maxlength="45" autocomplete="off"
                                           placeholder="ingrese valor" value="<?=$dtlleItem['marca_inv']?>">
                                    <small class="form-text text-muted">Máximo 45 carácteres</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="ocompra_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Código Activo
                                </label>
                                <div class="col-sm-3">
                                    <input type="text" class="form-control input-md text-left"
                                           name="cact_itm" maxlength="20" autocomplete="off"
                                           placeholder="ingrese código" value="<?=$dtlleItem['cactivo_inv']?>">
                                    <small class="form-text text-muted">Máximo 20 carácteres</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="ocompra_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Código Inventario
                                </label>
                                <div class="col-sm-3">
                                    <input type="text" class="form-control input-md text-left"
                                           name="cinvent_itm" maxlength="20" autocomplete="off"
                                           placeholder="ingrese código" value="<?=$dtlleItem['cinventario_inv']?>">
                                    <small class="form-text text-muted">Máximo 20 carácteres</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="ocompra_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    MAPEL
                                </label>
                                <div class="col-sm-3">
                                    <input type="text" class="form-control input-md text-left"
                                           name="mapel_itm" maxlength="20" autocomplete="off"
                                           placeholder="ingrese código" value="<?=$dtlleItem['cmapel_inv']?>">
                                    <small class="form-text text-muted">Máximo 20 carácteres</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="ocompra_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    ONU
                                </label>
                                <div class="col-sm-3">
                                    <input type="text" class="form-control input-md text-left"
                                           name="onu_itm" maxlength="20" autocomplete="off"
                                           placeholder="ingrese código" value="<?=$dtlleItem['conu_inv']?>">
                                    <small class="form-text text-muted">Máximo 20 carácteres</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="fins_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Fecha última calibración
                                </label>
                                <div class="col-sm-3">
                                    <input type="text" class="form-control input-md text-left inputFecha"
                                           name="fcalibra_itm" maxlength="10" placeholder="**/**/****" autocomplete="off"
                                           value="<?=$obj_fn->fecha_ENG_ESP($dtlleItem['fechaultcalibra_inv'])?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Frecuencia de calibración
                                </label>
                                <div class="col-sm-3">
                                    <input type="text" class="form-control input-md text-left"
                                           name="freccal_itm" placeholder="ingrese valor" value="<?=$dtlleItem['freccalibra_inv']?>"
                                           onkeyup="if(!Number(this.value)){this.value = ''; }">
                                    <small class="form-text text-muted">Número en meses</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="frecepvale_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Fecha recepción por encargado almacén
                                </label>
                                <div class="col-sm-3">
                                    <input type="text" class="form-control input-md text-left inputFecha"
                                           name="frecepvale_itm" maxlength="10" placeholder="**/**/****"
                                           value="<?=$obj_fn->fecha_ENG_ESP($dtlleItem['fecharecep_inv'])?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Observaciones
                                </label>
                                <div class="col-sm-8">
                                            <textarea class="form-control" name="obs_itm" maxlength="300" autocomplete="off"
                                                      rows="4" cols="1" placeholder="detalle alguna observación encontrada"><?=$dtlleItem['observ_inv']?></textarea>
                                    <small class="form-text text-muted">Máximo 300 carácteres</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 text-center">
                            <button type="button" id="btnCancel" class="btn btn-light mr-20 btn-lg">
                                Cancelar
                            </button>
                            <button type="submit" class="btn bg-warning-0 btn-hover-transform btn-lg">
                                Actualizar
                            </button>
                        </div>
                    </div>
                </form>
                <br><br><br>
            </div>
            <?php
        }
        catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function actualizar_Item_JSON(){
        try {
            date_default_timezone_set("America/Lima");
            setlocale(LC_TIME, 'es_PE.UTF-8');
            $obj_fn = new FuncionesModel();
            $idinv = (int)$_POST['idinv_i'];
            $idUsuario = $obj_fn->encrypt_decrypt('decrypt',$_POST['idusitm_tk']);
            $obj_inv = new InventarioModel();
            $dtlleIvent = $obj_inv->detalle_Item_xID($idinv);

            /**************** datos Sistema *******************/
            $idClasificacion = (int)$dtlleIvent['id_cla'];
            $sis1 = $idClasificacion;
            $sis2 = trim($dtlleIvent['cod_inv']);
            $sis3 = trim($dtlleIvent['cant_inv']);
            $sis4 = trim($dtlleIvent['des_inv']);
            $sis5 = trim($dtlleIvent['um_inv']);
            $sis6 = trim($dtlleIvent['ubic_inv']);
            $sis7 = trim($dtlleIvent['nroparte_inv']);
            $sis8 = $dtlleIvent['fechadepre_inv'];
            $sis9 = (float)$dtlleIvent['costo_act_inv'];
            $sis10 = (int)$dtlleIvent['frec_depre_act_inv'];
            $sis11 = (float)$dtlleIvent['val_depre_mensual_inv'];
            $sis12 = trim($dtlleIvent['nguia_inv']);
            $sis13 = trim($dtlleIvent['ordencompra_inv']);
            $sis14  = trim($dtlleIvent['marca_inv']);
            $sis15  = trim($dtlleIvent['cactivo_inv']);
            $sis16 = trim($dtlleIvent['cinventario_inv']);
            $sis17 = trim($dtlleIvent['cmapel_inv']);
            $sis18 = trim($dtlleIvent['conu_inv']);
            $sis19 = trim($_POST['fechaultcalibra_inv']);
            $sis20 = (int)$dtlleIvent['freccalibra_inv'];
            $sis21 = trim($dtlleIvent['fecharecep_inv']);
            $sis22 = trim($dtlleIvent['observ_inv']);

            /************* datos Formulario ***************/
            $formFechaDepreciacion = "0000-00-00";
            $formCostoActivo = 0;
            $formFrecDepre = 0;
            $formValMensual = 0;
            if($idClasificacion == 1) {
                $formFechaDepreciacion = $obj_fn->fecha_ESP_ENG($_POST['fInicialDepre_itm']);
                $formCostoActivo = (float)$_POST['costoActivo_itm'];
                $formFrecDepre = (int)$_POST['frecDepre_itm'];
                $formValMensual = (float)$_POST['valMensual_itm'];
            }
            $formNroGuia = "";
            if(!empty($_POST['guia_itm'])){ $formNroGuia = trim($_POST['guia_itm']); }
            $formFUltCalibra = "0000-00-00";
            if(isset($_POST['fcalibra_itm'])){ $formFUltCalibra = $obj_fn->fecha_ESP_ENG($_POST['fcalibra_itm']); }
            $formFrecCalibra = 0;
            if(!empty($_POST['freccal_itm'])){ $formFrecCalibra = (int)$_POST['freccal_itm']; }
            $formFRecep = "0000-00-00";
            if(isset($_POST['frecepvale_itm'])){ $formFRecep = $obj_fn->fecha_ESP_ENG($_POST['frecepvale_itm']); }

            $form1 = (int)$_POST['clasifica_itm'];
            $form2 = trim($_POST['codigo_itm']);
            $form3 = trim($_POST['cant_itm']);
            $form4 = trim($_POST['des_itm']);
            $form5 = trim($_POST['um_itm']);
            $form6 = trim($_POST['ubic_itm']);
            $form7 = trim($_POST['nparte_itm']);
            $form8 = $formFechaDepreciacion;
            $form9 = $formCostoActivo;
            $form10 = $formFrecDepre;
            $form11 = $formValMensual;
            $form12 = $formNroGuia;
            $form13 = trim($_POST['ocompra_itm']);
            $form14  = trim($_POST['marca_itm']);
            $form15 = trim($_POST['cact_itm']);
            $form16 = trim($_POST['cinvent_itm']);
            $form17 = trim($_POST['mapel_itm']);
            $form18 = trim($_POST['onu_itm']);
            $form19 = $formFUltCalibra;
            $form20 = $formFrecCalibra;
            $form21 = $formFRecep;
            $form22 = trim($_POST['obs_itm']);


            /************* ************** **************/

            $datos = array();
            if($form1 !== $sis1){
                $row = array('campo'=>'Clasificación','val_anter'=>$sis1,'val_chang'=>$form1);
                array_push($datos,$row);
            }
            if($form2 !== $sis2){
                $row = array('campo'=>'Código','val_anter'=>$sis2,'val_chang'=>$form2);
                array_push($datos,$row);
            }
            if($form3 !== $sis3){
                $row = array('campo'=>'Cantidad','val_anter'=>$sis3,'val_chang'=>$form3);
                array_push($datos,$row);
            }
            if($form4 !== $sis4){
                $row = array('campo'=>'Descripción','val_anter'=>$sis4,'val_chang'=>$form4);
                array_push($datos,$row);
            }
            if($form5 !== $sis5){
                $row = array('campo'=>'Unidad Medida','val_anter'=>$sis5,'val_chang'=>$form5);
                array_push($datos,$row);
            }
            if($form6 !== $sis6){
                $row = array('campo'=>'Ubicación','val_anter'=>$sis6,'val_chang'=>$form6);
                array_push($datos,$row);
            }
            if($form7 !== $sis7){
                $row = array('campo'=>'Número de Parte','val_anter'=>$sis7,'val_chang'=>$form7);
                array_push($datos,$row);
            }
            if($form8 !== $sis8){
                $row = array('campo'=>'Fecha Recepción','val_anter'=>$sis8,'val_chang'=>$form8);
                array_push($datos,$row);
            }
            if($form9 !== $sis9){
                $row = array('campo'=>'Costo Activo','val_anter'=>$sis9,'val_chang'=>$form9);
                array_push($datos,$row);
            }
            if($form10 !== $sis10){
                $row = array('campo'=>'Frecuencia Depreciación','val_anter'=>$sis10,'val_chang'=>$form10);
                array_push($datos,$row);
            }
            if($form11 !== $sis11){
                $row = array('campo'=>'Valor depreciación','val_anter'=>$sis11,'val_chang'=>$form11);
                array_push($datos,$row);
            }
            if($sis12 !== $form12){
                $row = array('campo'=>'Número Guia','val_anter'=>$sis12,'val_chang'=>$form12);
                array_push($datos,$row);
            }
            if($sis13 !== $form13){
                $row = array('campo'=>'orden compra','val_anter'=>$sis13,'val_chang'=>$form13);
                array_push($datos,$row);
            }
            if($form14 !== $sis14){
                $row = array('campo'=>'Marca','val_anter'=>$sis14,'val_chang'=>$form14);
                array_push($datos,$row);
            }
            if($form15 !== $sis15){
                $row = array('campo'=>'Código activo','val_anter'=>$sis15,'val_chang'=>$form15);
                array_push($datos,$row);
            }
            if($form16 !== $sis16){
                $row = array('campo'=>'Código inventario','val_anter'=>$sis16,'val_chang'=>$form16);
                array_push($datos,$row);
            }
            if($form17 !== $sis17){
                $row = array('campo'=>'Código mapel','val_anter'=>$sis17,'val_chang'=>$form17);
                array_push($datos,$row);
            }
            if($form18 !== $sis18){
                $row = array('campo'=>'Código onu','val_anter'=>$sis18,'val_chang'=>$form18);
                array_push($datos,$row);
            }
            if($sis19 !== $form19){
                $row = array('campo'=>'Fecha última calibración','val_anter'=>$sis19,'val_chang'=>$form19);
                array_push($datos,$row);
            }
            if($sis20 !== $form20){
                $row = array('campo'=>'Frecuencia Calibración','val_anter'=>$sis20,'val_chang'=>$form20);
                array_push($datos,$row);
            }
            if($sis21 !== $form21){
                $row = array('campo'=>'Fecha recepción por encargado almacén','val_anter'=>$sis21,'val_chang'=>$form21);
                array_push($datos,$row);
            }
            if($form22 !== $sis22){
                $row = array('campo'=>'Observación','val_anter'=>$sis22,'val_chang'=>$form22);
                array_push($datos,$row);
            }


            if(is_array($datos) && sizeof($datos) > 0){
                $obj_us = new UsuarioModel();
                $dtlleUsuario = $obj_us->detalle_Usuario_xID($idUsuario);
                $textPersona = "";
                if(is_array($dtlleUsuario)){
                    $obj_per = new PersonaModel();
                    $dtllePersona = $obj_per->detalle_Persona_xID($dtlleUsuario['id_per']);
                    if(is_array($dtllePersona)) {
                        $textPersona = $dtllePersona['ape_pa_per']." ".$dtllePersona['nombres_per'];
                    }
                }

                foreach ($datos as $log){
                    $datesRegLog[0] = $idinv;
                    $datesRegLog[1] = $log['campo'];
                    $datesRegLog[2] = $log['val_anter'];
                    $datesRegLog[3] = $log['val_chang'];
                    $datesRegLog[4] = $textPersona;
                    $datesRegLog[5] = $idUsuario;
                    $datesRegLog[6] = date("Y-m-d H:i:s");
                    $obj_inv->registrar_log_Actualizacion_Inventario($datesRegLog);
                }
            }

            $datesChange[0] = $idinv;
            $datesChange[1] = $form1;
            $datesChange[2] = $form2;
            $datesChange[3] = $form3;
            $datesChange[4] = $form4;
            $datesChange[5] = $form5;
            $datesChange[6] = $form6;
            $datesChange[7] = $form7;
            $datesChange[8] = $form8;
            $datesChange[9] = $form9;
            $datesChange[10]= $form10;
            $datesChange[11]= $form11;
            $datesChange[12]= $form12;
            $datesChange[13]= $form13;
            $datesChange[14]= $form14;
            $datesChange[15]= $form15;
            $datesChange[16]= $form16;
            $datesChange[17]= $form17;
            $datesChange[18]= $form18;
            $datesChange[19]= $form19;
            $datesChange[20]= $form20;
            $datesChange[21]= $form21;
            $datesChange[22]= $form22;
            $updateItem = $obj_inv->update_Item($datesChange);
            $val = 0;
            $message = "Error al actualizar el registro.";
            if($updateItem){
                $val = 1;
                $message = "Ítem actualizado satisfactoriamente";
            }

            echo json_encode(array('status'=>$val, 'message'=>$message));

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function delete_Item_JSON(){
        try {
            $obj_fn = new FuncionesModel();
            $idus = $obj_fn->encrypt_decrypt('decrypt',$_POST['idus']);
            $idInventario = (int)$_POST['id'];

            $obj_mov = new MovimientoModel();
            $exMovItem = $obj_mov->existe_MovimientoDetalle_xIdInventario($idInventario);
            //Si existe movimiento no se elimina se cambia de condicion a 2
            $val = 0;
            $obj_inv = new InventarioModel();
            if((int)$exMovItem['nreg'] > 0) {
                $obj_us = new UsuarioModel();
                $dtlleIvent = $obj_inv->detalle_Item_xID($idInventario);
                $dtlleUsuario = $obj_us->detalle_Usuario_xID($idus);
                $textPersona = "";
                if(is_array($dtlleUsuario)){
                    $obj_per = new PersonaModel();
                    $dtllePersona = $obj_per->detalle_Persona_xID($dtlleUsuario['id_per']);
                    if(is_array($dtllePersona)) {
                        $textPersona = $dtllePersona['ape_pa_per']." ".$dtllePersona['nombres_per'];
                    }
                }
                $datesDelInvent[0] = $idInventario;
                $datesDelInvent[1] = $dtlleIvent['id_alm'];
                $datesDelInvent[2] = $dtlleIvent['cod_inv'];
                $datesDelInvent[3] = $dtlleIvent['des_inv'];
                $datesDelInvent[4] = $dtlleIvent['um_inv'];
                $datesDelInvent[5] = $dtlleIvent['om_inv'];
                $datesDelInvent[6] = $idus;
                $datesDelInvent[7] = $textPersona;
                $datesDelInvent[8] = date("Y-m-d H:i:s");
                $obj_inv->registrar_log_delete_Inventario($datesDelInvent);

                $datesUpdateInv[0] = $idInventario;
                $datesUpdateInv[1] = 2;
                $updateInv = $obj_inv->update_Condicion_Item($datesUpdateInv);
                if($updateInv){$val=1;}
            }
            else if((int)$exMovItem['nreg'] == 0) {
                $lstMovDetalle = $obj_mov->lista_MovimientoDetalle_xIdInventario($idInventario);
                if(!is_null($lstMovDetalle) && sizeof($lstMovDetalle) == 1){
                    $obj_mov->delete_Movimiento_xID($lstMovDetalle[0]['id_mov']);
                    $deleteItem = $obj_inv->delete_Inventario_xID($idInventario);
                    if($deleteItem){$val=1;}
                }
            }

            echo json_encode(array('status'=>$val));

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function loadCampos_TranferirItem_Ajax(){
        try {
            $idus = $_GET['idus'];
            $idsu = (int)$_GET['idsu'];
            $idalm = (int)$_GET['idalm'];
            $obj_serv = new ServicioModel();
            $dtlleServUsuario = $obj_serv->detalle_ServicioUsuario_xIDSU($idsu);
            $IdServicio = $dtlleServUsuario['id_serv'];
            $obj_alm = new AlmacenModel();
            $dtlleAlmacen = $obj_alm->detalle_Almacen_xID($idalm);
            $lstAlmacServicio = $obj_alm->lista_almacenes_Activos_xServicio_menosAlmActual($IdServicio,$idalm);
            $dtlleCorrelativo = $obj_alm->detalle_correlativo_Almacen($idalm);
            $numCorrelativo = "";
            if(is_array($dtlleCorrelativo)){
                $numCorrelativo = "TR-".$IdServicio."-".str_pad((int)$dtlleCorrelativo['val_alc'] + 1,6,"0",STR_PAD_LEFT);
            }?>
            <form id="formTransferirItem" role="form" method="post">
                <input type="hidden" id="idService_i" value="<?=$IdServicio?>">
                <input type="hidden" id="idusitm_tk" name="idusitm_tk" value="<?=$idus?>">
                <input type="hidden" id="idalm_i" name="idalm_i" value="<?=$idalm?>">
                <input type="hidden" id="fechareg_i" name="fechareg_i" value="<?=date("Y-m-d")?>">
                <input type="hidden" id="idalc" name="idalc" value="<?=$dtlleCorrelativo['id_alc']?>">
                <input type="hidden" id="valalc" name="valalc" value="<?=(int)$dtlleCorrelativo['val_alc']?>">
                <input type="hidden" id="nrotransf_i" name="nrotransf_i" value="<?=$numCorrelativo?>">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title">
                                <h4 class="mb-0 text-brown-800 font-weight-bold">
                                    Transferencia : <code class="text-primary-800"><?=$dtlleAlmacen['titulo_alm']?></code>
                                </h4>
                                <ol class="breadcrumb mb-0 pl-0 pt-1 pb-0">
                                    <li class="breadcrumb-item text-muted">Realice la trasferencia de uno o más ítems, de un almacén a otro.</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 mb-10">
                            <div class="card mb-4 card-shadow">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-3">
                                            <span class="bg-primary rounded-circle text-center wb-icon-box">
                                                <i class="icon-notebook text-light f24"></i>
                                            </span>
                                        </div>
                                        <div class="col-9">
                                            <h3 class="mt-1 mb-0"><?=$numCorrelativo?></h3>
                                            <p class="f12 mb-0">Número Transacción</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 mb-10">
                            <div class="card mb-4 card-shadow">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-3">
                                            <span class="bg-warning rounded-circle text-center wb-icon-box">
                                                <i class="icon-calendar text-light f24"></i>
                                            </span>
                                        </div>
                                        <div class="col-9">
                                            <h3 class="mt-1 mb-0"><?=date("d/m/Y H:s")?></h3>
                                            <p class="f12 mb-0">Fecha y hora registro</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card card-shadow">
                        <div class="card-body bg-secondary-light-5 text-center">
                            <h4 class="card-title">Tipo Transferencia</h4>
                            <h6 class="card-subtitle text-muted font-weight-normal fz-12">
                                Debe seleccionar un tipo de transferencia descrito a continuación.
                            </h6>
                        </div>
                        <hr class="no-padding no-margin">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 text-center">
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="customRadioT1" name="rdbTipoTransfer" class="custom-control-input tipoTransAlmacen" value="1" checked="checked">
                                        <label class="custom-control-label cursor-pointer" for="customRadioT1">Interna (Almacén interno del servicio)</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="customRadioT2" name="rdbTipoTransfer" class="custom-control-input tipoTransAlmacen" value="2">
                                        <label class="custom-control-label cursor-pointer" for="customRadioT2">Externa (Almacén externo del servicio)</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card card-shadow">
                        <div class="card-header bg-secondary-light-5">
                            <h4 class="card-title font-weight-bold">Datos Generales</h4>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">
                                Todos los campos descritos con <code class="font-weight-bold">(*)</code>, son campos obligatorios.
                            </p>
                            <div class="row">
                                <div class="col-12" id="mensajes_actions_act"></div>
                            </div>
                            <div class="cd" id="contedAlmDestinity">
                                <div class="form-group row">
                                    <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                        Almacén Destino
                                        <span class="text-danger font-weight-bold">*</span>
                                    </label>
                                    <div class="col-xl-6 col-lg-6 col-md-8 col-sm-12">
                                        <select name="IdAlmacen_itm" id="IdAlmacen_itm" class="form-control selectedClass" data-placeholder="Seleccione..." required>
                                            <option></option>
                                            <?php
                                            if(!is_null($lstAlmacServicio)){
                                                foreach ($lstAlmacServicio as $almacen){?>
                                                    <option value="<?=$almacen['id_alm']?>"><?=$almacen['titulo_alm']?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Atención a
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-xl-6 col-lg-6 col-md-8 col-sm-12">
                                    <input type="text" class="form-control input-md text-left"
                                           id="recibido_itm" name="recibido_itm" maxlength="45" required
                                           placeholder="ingrese nombre del receptor" autocomplete="off">
                                    <small class="form-text text-muted">Máximo 45 carácteres</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Autorizado Por
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-xl-6 col-lg-6 col-md-8 col-sm-12">
                                    <input type="text" class="form-control input-md text-left"
                                           id="autorizado_itm" name="autorizado_itm" maxlength="45" required
                                           placeholder="ingrese nombre del autorizante">
                                    <small class="form-text text-muted float-left">Máximo 45 carácteres</small>
                                    <div class="form-check float-right">
                                        <label class="form-check-label cursor-pointer">
                                            <input class="form-check-input" type="checkbox" id="chk_notAutorizado"
                                                   name="chk_notAutorizado" value="1" autocomplete="off">
                                            <span class="font-weight-bold text-danger-800">Marque cuando NO APLICA</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="obs_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Observaciones
                                </label>
                                <div class="col-xl-7 col-lg-7 col-md-8 col-sm-12">
                                    <textarea class="form-control" name="obs_itm" id="obs_itm" maxlength="3000"
                                              rows="5" cols="1" placeholder="obervaciones"></textarea>
                                    <small class="form-text text-muted">Máximo 3000 carácteres</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="cd" id="contedTransito"></div>
                </div>
                <div class="container-fluid">
                    <div class="page-title text-center">
                        <h4 class="mb-0 text-brown-800 font-weight-bold">
                            Seleccione los Ítems a transferir
                        </h4>
                        <p class="text-muted mb-0">Agregue cada ítems e ingrese la cantidad a transferir.</p>
                    </div>
                    <div class="card mb-20 card-shadow">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 text-lg-left text-md-left text-sm-center">
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" placeholder="Ingrese un código a buscar"
                                               name="txtcodSearching" id="txtcodSearching">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="btnSearchItemInventario" data-idalm="<?=$idalm?>">
                                                <i class="ti-search position-left"></i>
                                                Buscar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 text-lg-right text-md-right text-sm-center">
                                    <button type="button" class="btn btn-outline-secondary btn-hover-transform float-right"
                                            id="btnLoad_ItemAlmacen" data-idalm="<?=$idalm?>">
                                        <i class="fa fa-plus mr-10"></i>
                                        Agregar Ítems
                                    </button>
                                </div>
                            </div>
                        </div>
                        <hr class="no-padding no-margin">
                        <div class="table-responsive">
                            <table id="Tbl_DetalleItem" class="table mb-0 table-bordered table-striped table-sm">
                                <thead class="">
                                <tr>
                                    <th width="30">#</th>
                                    <th class="text-center">Código</th>
                                    <th class="text-left">Descripción</th>
                                    <th class="text-center">Unid.Med.</th>
                                    <th class="text-center">Nro.Parte/Serie</th>
                                    <th class="text-center" width="150">Cantidad</th>
                                    <th class="text-center" width="100">Stock</th>
                                    <th width="50"></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td colspan="8" class="text-center">No se encontraron ítems agregados.</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-info btn-hover-transform btn-lg mr-20">
                            <i class="ti-save position-left"></i>
                            Registrar Tranferencia
                        </button>
                        <button type="button" id="btnCancel" class="btn btn-light btn-lg">
                            Cancelar
                        </button>
                    </div>
                </div>
            </form>
            <br><br><br>
            <?php
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function tranferir_Item_JSON(){
        try {
            date_default_timezone_set("America/Lima");
            setlocale(LC_TIME, 'es_PE.UTF-8');
            $obj_fn = new FuncionesModel();
            $idalm = (int)$_POST['idalm_i'];
            $detalleItems = $_POST['detalle'];
            $idAlm_destino = (int)$_POST['IdAlmacen_itm'];
            $idUSER = $obj_fn->encrypt_decrypt('decrypt',$_POST['idusitm_tk']);
            $idalc = (int)$_POST['idalc'];
            $valalc = (int)$_POST['valalc'];

            $txtEntregado = "";
            $nroVale = "";
            $areaOperativa = "";

            $chkNotAutorizadoPor = 0;
            if(isset($_POST['chk_notAutorizado'])){ $chkNotAutorizadoPor = 1; }

            $textAutorizadoPor = "NO APLICA AUTORIZACIÓN";
            if($chkNotAutorizadoPor == 0){
                $textAutorizadoPor = trim($_POST['autorizado_itm']);
            }

            $insertID = 0;
            //Transferencia Interna Inmediata
            if((int)$_POST['tipoTransfer'] == 1){
                $datesTAB[0] = "TRA";
                $datesTAB[1] = $idalm;
                $datesTAB[2] = "";
                $datesTAB[3] = "";//Orden Mantto
                $datesTAB[4] = $idAlm_destino;
                $datesTAB[5] = ""; //Solicitado por
                $datesTAB[6] = trim($_POST['recibido_itm']);
                $datesTAB[7] = $textAutorizadoPor;
                $datesTAB[8] = $obj_fn->quitar_caracteresEspeciales($_POST['obs_itm']);
                $datesTAB[9] = "";
                $datesTAB[10]= "";
                $datesTAB[11]= trim($_POST['fechareg_i']);
                $datesTAB[12]= $_POST['nrotransf_i'];
                $datesTAB[13]= $obj_fn->encrypt_decrypt('decrypt',$_POST['idusitm_tk']);
                $datesTAB[14]= date("Y-m-d H:i:s");
                $datesTAB[15]= $txtEntregado;
                $datesTAB[16]= $nroVale;
                $datesTAB[17]= 0;
                $datesTAB[18]= $areaOperativa;
                $val = 0;
                $message = "Error al realizar la Transferencia.";
                $obj_mov = new MovimientoModel();

                $insertID = $obj_mov->registrar_Movimiento_Item_lastID($datesTAB);
                if((int)$insertID > 0){
                    $val = 1;
                    $message = "Transferencia realizada satisfactoriamente";

                    $acierto = 0;
                    for($j=0; $j<sizeof($detalleItems);$j++){

                        $idInvent = (int)$detalleItems[$j][0];

                        $obj_inv= new InventarioModel();
                        $dtlleItem = $obj_inv->detalle_Item_xID($idInvent);

                        $cantSolic = $detalleItems[$j][8];
                        $codMaterial =  $dtlleItem['cod_inv'];
                        $nroParte = $dtlleItem['nroparte_inv'];
                        $codActivo = $dtlleItem['cactivo_inv'];


                        $datesMOV[0] = $insertID; //idMov
                        $datesMOV[1] = $idInvent; //idInv
                        $datesMOV[2] = $codMaterial;//Codigo
                        $datesMOV[3] = $detalleItems[$j][2]; //Descripcion
                        $datesMOV[4] = $nroParte; //Nro.Parte
                        $datesMOV[5] = $cantSolic; //Cantidad
                        $datesMOV[6] = $detalleItems[$j][9];//Stock
                        $datesMOV[7] = $dtlleItem['id_cla'];
                        $datesMOV[8] = $dtlleItem['um_inv'];
                        $datesMOV[9] = $detalleItems[$j][10]; //Marca
                        $datesMOV[10]= $codActivo;//C.Activo
                        $datesMOV[11]= $dtlleItem['cinventario_inv'];//C.Inventario
                        $datesMOV[12]= $dtlleItem['cmapel_inv'];//C.Mapel
                        $datesMOV[13]= $dtlleItem['conu_inv'];//C.ONU

                        $insertDetalle = $obj_mov->registrar_Movimiento_Item_Detalle($datesMOV);
                        if($insertDetalle){
                            //Restamos la cantidad del item del almacen inicio
                            $valRestaStock = (float)$dtlleItem['cant_inv'] - (float)$cantSolic;
                            $datesUStock[0] = $idInvent;
                            $datesUStock[1] = $valRestaStock;
                            $obj_inv->actualizar_Stock_Item($datesUStock);

                            //Insertamos stock o item al nuevo almacen
                            $datesSearch[0] = $idAlm_destino;
                            $datesSearch[1] = trim($codMaterial);
                            $datesSearch[2] = trim($nroParte);
                            $datesSearch[3] = trim($codActivo);
                            //Código, unidad (equipo), OT y Eecha Recepción
                            $dtlleItemDes = $obj_inv->busca_existencia_Item_xDatos($datesSearch);
                            if(is_null($dtlleItemDes)){
                                $datesAlNew[0] = $idAlm_destino;
                                $datesAlNew[1] = $codMaterial;
                                $datesAlNew[2] = $cantSolic;
                                $datesAlNew[3] = $dtlleItem['des_inv'];
                                $datesAlNew[4] = $dtlleItem['um_inv'];
                                $datesAlNew[5] = $dtlleItem['nroparte_inv'];
                                $datesAlNew[6] = $dtlleItem['marca_inv'];
                                $datesAlNew[7] = $dtlleItem['observ_inv'];
                                $datesAlNew[8] = $idUSER;
                                $datesAlNew[9] = date("Y-m-d H:i:s");
                                $datesAlNew[10] = $dtlleItem['ordencompra_inv'];
                                $datesAlNew[11] = $dtlleItem['id_cla'];
                                $datesAlNew[12] = $dtlleItem['costo_act_inv'];
                                $datesAlNew[13] = $dtlleItem['frec_depre_act_inv'];
                                $datesAlNew[14] = $dtlleItem['val_depre_mensual_inv'];
                                $datesAlNew[15] = $dtlleItem['cactivo_inv'];
                                $datesAlNew[16] = $dtlleItem['cinventario_inv'];
                                $datesAlNew[17] = $dtlleItem['cmapel_inv'];
                                $datesAlNew[18] = $dtlleItem['conu_inv'];
                                $obj_inv->registrar_Item($datesAlNew);
                            }
                            else{
                                $valSumaStock_des = (int)$dtlleItemDes['cant_inv'] + (int)$cantSolic;
                                $datesUStock_des[0] = $dtlleItemDes['id_inv'];
                                $datesUStock_des[1] = (float)$valSumaStock_des;
                                $obj_inv->actualizar_Stock_Item($datesUStock_des);
                            }
                            $acierto++;
                        }
                    }

                    if($acierto == sizeof($detalleItems) || $acierto > 0){
                        $val = 1;
                        $datesCorrel[0] = $idalc;
                        $datesCorrel[1] = $valalc + 1;
                        $obj_alm = new AlmacenModel();
                        $obj_alm->actualizar_Correlativo_Almacen($datesCorrel);
                    }
                }
            }
            //Transferencia externa a Transito
            else if((int)$_POST['tipoTransfer'] == 2){
                $ttMotivo = "";
                $ttFechaGuia = "0000-00-00";
                $ttNroGuia = "";
                $ttDias = 0;
                $persona1 = "";
                $ndoc1 = "";
                $persona2 = "";
                $ndoc2 = "";
                if(!empty(trim($_POST['motivo_itm']))){ $ttMotivo = trim($_POST['motivo_itm']); }
                if(!empty(trim($_POST['fguia_itm']))){ $ttFechaGuia = $obj_fn->fecha_ESP_ENG($_POST['fguia_itm']); }
                if(!empty(trim($_POST['nguia_itm']))){ $ttNroGuia = $_POST['nguia_itm']; }
                if(!empty(trim($_POST['ndias_itm'])) and (int)$_POST['ndias_itm']>0){ $ttDias = (int)$_POST['ndias_itm']; }

                if(!empty(trim($_POST['aper1_itm']))){ $persona1 = $_POST['aper1_itm']; }
                if(!empty(trim($_POST['adoc1_itm']))){ $ndoc1 = $_POST['adoc1_itm']; }
                if(!empty(trim($_POST['aper2_itm']))){ $persona2 = $_POST['aper2_itm']; }
                if(!empty(trim($_POST['adoc2_itm']))){ $ndoc2 = $_POST['adoc2_itm']; }

                $datesTAB[0] = "TRA";
                $datesTAB[1] = $idalm;
                $datesTAB[2] = "";
                $datesTAB[3] = "";//O.Mantto
                $datesTAB[4] = $idAlm_destino;
                $datesTAB[5] = "";//Solicitado
                $datesTAB[6] = trim($_POST['recibido_itm']);
                $datesTAB[7] = $textAutorizadoPor;
                $datesTAB[8] = $obj_fn->quitar_caracteresEspeciales($_POST['obs_itm']);
                $datesTAB[9] = "";
                $datesTAB[10]= "";
                $datesTAB[11]= trim($_POST['fechareg_i']);
                $datesTAB[12]= $_POST['nrotransf_i'];
                $datesTAB[13]= (int)$obj_fn->encrypt_decrypt('decrypt',$_POST['idusitm_tk']);
                $datesTAB[14]= date("Y-m-d H:i:s");
                $datesTAB[15]= $txtEntregado;
                $datesTAB[16]= $nroVale;
                $datesTAB[17]= 0;
                $datesTAB[18]= $ttMotivo;
                $datesTAB[19]= $ttFechaGuia;
                $datesTAB[20]= $ttNroGuia;
                $datesTAB[21]= $ttDias;
                $datesTAB[22]= $persona1."-".$ndoc1;
                $datesTAB[23]= $persona2."-".$ndoc2;

                $val = 0;
                $message = "Error al realizar la Transferencia.";
                $obj_mov = new MovimientoModel();
                $insertID = $obj_mov->registrar_MovimientoTransito_Item_lastID($datesTAB);

                if((int)$insertID > 0){
                    $val = 1;
                    $message = "Transferencia a Transito realizada satisfactoriamente";
                    $acierto = 0;
                    for($j=0; $j<sizeof($detalleItems);$j++){
                        $idInvent = (int)$detalleItems[$j][0];
                        $cantSolic = $detalleItems[$j][8];
                        $codAlm_des = $detalleItems[$j][1];

                        $obj_inv= new InventarioModel();
                        $dtlleItem = $obj_inv->detalle_Item_xID($idInvent);
                        $fechaultcalibra = "0000-00-00";
                        if(!is_null($dtlleItem['fechaultcalibra_inv']) && !$dtlleItem['fechaultcalibra_inv'] = "0000-00-00"){  $fechaultcalibra = $dtlleItem['fechaultcalibra_inv'];}
                        $freccalibra = "";
                        if(!is_null($dtlleItem['freccalibra_inv']) && (int)$dtlleItem['freccalibra_inv'] > 0){  $freccalibra = $dtlleItem['freccalibra_inv'];}

                        $datesMOV[0] = $insertID;//IdMOV
                        $datesMOV[1] = $idInvent;
                        $datesMOV[2] = $codAlm_des;//Codigo
                        $datesMOV[3] = $detalleItems[$j][2];//Descripción
                        $datesMOV[4] = $detalleItems[$j][3]; //Nro.Parte
                        $datesMOV[5] = $cantSolic;
                        $datesMOV[6] = $detalleItems[$j][9]; //Stock
                        $datesMOV[7] = $dtlleItem['id_cla'];
                        $datesMOV[8] = $fechaultcalibra;
                        $datesMOV[9] = $freccalibra;
                        $datesMOV[10]= $dtlleItem['cactivo_inv'];
                        $datesMOV[11]= $dtlleItem['cinventario_inv'];
                        $datesMOV[12]= $dtlleItem['cmapel_inv'];
                        $datesMOV[13]= $dtlleItem['conu_inv'];
                        $insertDetalle = $obj_mov->registrar_MovimientoTransito_Item_Detalle($datesMOV);
                        if($insertDetalle){
                            $acierto++;
                            //Restamos la cantidad del item del almacen inicio
                            $valRestaStock = (float)$dtlleItem['cant_inv'] - (float)$cantSolic;
                            $datesUStockInitial[0] = $idInvent;
                            $datesUStockInitial[1] = $valRestaStock;
                            $obj_inv->actualizar_Stock_Item($datesUStockInitial);
                        }
                    }

                    if($acierto == sizeof($detalleItems) || $acierto > 0){
                        $val = 1;
                        $datesCorrel[0] = $idalc;
                        $datesCorrel[1] = $valalc + 1;
                        $obj_alm = new AlmacenModel();
                        $obj_alm->actualizar_Correlativo_Almacen($datesCorrel);
                    }
                }

            }

            echo json_encode(array('status'=>$val, 'tipotransfer'=>(int)$_POST['tipoTransfer'], 'isetId'=>$obj_fn->encrypt_decrypt('encrypt',$insertID), 'message'=>$message,'array'=>$datesTAB));

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function loadview_optionGuia(){
        try {
            $idTransfer = $_GET['id'];?>
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <input type="hidden" id="idtransf" value="<?=$idTransfer?>">
                    <div class="modal-header text-center" style="display: block;padding: 0.5rem;">
                        <h5 class="modal-title text-center">Opciónes descarga Guía</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xl-6 col-md-6 col-sm-12">
                                <div class="card mb-4">
                                    <img class="card-img-top img-fluid" src="../assets/img/optguia/formato.jpg" alt="Formato guia" width="70%">
                                    <div class="card-body pb-0">
                                        <label class="control control-solid control--radio mb-0">Guía Formato
                                            <input type="radio" name="rdbOption_g" checked="checked" value="1" required/>
                                            <span class="control__indicator"></span>
                                        </label>
                                    </div>
                                </div>

                            </div>
                            <div class="col-xl-6 col-md-6 col-sm-12">
                                <div class="card mb-4">
                                    <img class="card-img-top img-fluid" src="../assets/img/optguia/impresion.jpg" alt="Guia impresión" width="70%">
                                    <div class="card-body pb-0">
                                        <label class="control control-solid control--radio mb-0">Guía Impresión
                                            <input type="radio" name="rdbOption_g" value="2" required/>
                                            <span class="control__indicator"></span>
                                        </label>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <hr class="no-margin">
                    <div class="modal-footer pt-6 pb-5 text-center" style="display: block">
                        <button type="button" class="btn btn-outline-danger btn-hover-transform btn-sm" id="btnGenerated_exp">
                            <i class="fa fa-file-pdf-o position-left"></i>
                            Generar
                        </button>
                        <button type="button" class="btn btn-default btn-sm" id="btnCancel_exp">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
            <?php
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function lista_existencias_Inventario_xAlmacen_JSON(){
        try {
            $idalm = (int)$_GET['id'];
            $obj_inv = new InventarioModel();
            $lstInventario = $obj_inv->lista_inventario_xIdAlmacen($idalm);

            $datos = array();
            if(is_array($lstInventario)){

                foreach($lstInventario as $inventario){
                    $txtClasificacion = "NO DEFINIDO";
                    $dtlleClasificacion = $obj_inv->detalle_Clasificacion_xID($inventario['id_cla']);
                    if(!is_null($dtlleClasificacion)){
                        $txtClasificacion = $dtlleClasificacion['des_cla'];
                    }

                    $cantSplit = explode(".",$inventario['cant_inv']);
                    $stock = number_format($inventario['cant_inv'], 2);
                    $tipo = 1;
                    if($cantSplit[1] == "00"){
                        $stock = (int)$inventario['cant_inv'];
                        $tipo = 2;
                    }
                    $btnAdd  = '<button type="button" class="btn bg-brown-600 btn-hover-transform btn-sm" id="btnItem_AddCart" ';
                    $btnAdd .= '        data-id="'.$inventario['id_inv'].'" data-cod="'.$inventario['cod_inv'].'" ';
                    $btnAdd .= '        data-des="'.$inventario['des_inv'].'" data-nparte="'.$inventario['nroparte_inv'].'" ';
                    $btnAdd .= '        data-marca="'.$inventario['marca_inv'].'" data-stock="'.$stock.'" data-um="'.$inventario['um_inv'].'"';
                    $btnAdd .= '        data-cactivo="'.$inventario['cactivo_inv'].'" data-cinventario="'.$inventario['cinventario_inv'].'" ';
                    $btnAdd .= '        data-cmapel="'.$inventario['cmapel_inv'].'" data-conu="'.$inventario['conu_inv'].'" ';
                    $btnAdd .= '        data-tipo="'.$tipo.'" style="padding:0.3rem 1.2rem;"> ';
                    $btnAdd .= '  <i class="fa fa-cart-plus font-15"></i> Agregar ';
                    $btnAdd .= '</button>';

                    $row = array(
                        0 => "",
                        1 => $txtClasificacion,
                        2 => $inventario['cod_inv'],
                        3 => $inventario['des_inv'],
                        4 => $inventario['marca_inv'],
                        5 => $inventario['um_inv'],
                        6 => $inventario['nroparte_inv'],
                        7 => $inventario['cinventario_inv'],
                        8 => $inventario['cactivo_inv'],
                        9 => $inventario['cmapel_inv'],
                        10=> $inventario['conu_inv'],
                        11=> $stock,
                        12=> $btnAdd
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

    public function lista_existencias_Inventario_xAlmacen_Retiro_JSON(){
        try {

            $idalm = (int)$_GET['id'];
            $obj_inv = new InventarioModel();
            $lstInventario = $obj_inv->lista_inventario_xIdAlmacen($idalm);

            $obj_fn = new FuncionesModel();

            $datos = array();
            if(is_array($lstInventario)){
                foreach($lstInventario as $inventario){

                    $cantSplit = explode(".",$inventario['cant_inv']);
                    $stock = number_format($inventario['cant_inv'], 2);
                    $tipo = 1;
                    if($cantSplit[1] == "00"){
                        $stock = (int)$inventario['cant_inv'];
                        $tipo = 2;
                    }
                    $btnAdd  = '<button type="button" class="btn bg-brown-600 btn-hover-transform btn-sm" id="btnItem_AddCart_Retiro" ';
                    $btnAdd .= '        data-id="'.$inventario['id_inv'].'" data-cod="'.$inventario['cod_inv'].'" ';
                    $btnAdd .= '        data-des="'.$inventario['des_inv'].'" data-nparte="'.$inventario['nroparte_inv'].'" ';
                    $btnAdd .= '        data-unid="'.$inventario['und_inv'].'" data-omantto="'.$inventario['om_inv'].'" ';
                    $btnAdd .= '        data-ubic="'.$inventario['ubic_inv'].'" data-stock="'.$stock.'" ';
                    $btnAdd .= '        data-frec="'.$inventario['fecharec_inv'].'" data-reserva="'.$inventario['reserva_inv'].'" ';
                    $btnAdd .= '        data-tipo="'.$tipo.'" style="padding:0.3rem 1.2rem;"> ';
                    $btnAdd .= '  <i class="fa fa-cart-plus font-15"></i> Agregar ';
                    $btnAdd .= '</button>';

                    $row = array(
                        0 => "",
                        1 => $inventario['und_inv'],
                        2 => $inventario['cod_inv'],
                        3 => $stock,
                        4 => $inventario['des_inv'],
                        5 => $inventario['marca_inv'],
                        6 => $inventario['um_inv'],
                        7 => $inventario['ubic_inv'],
                        8 => $inventario['nroparte_inv'],
                        9 => $inventario['reserva_inv'],
                        10=> $inventario['ordencompra_inv'],
                        11=> $inventario['om_inv'],
                        12=> $obj_fn->fecha_ENG_ESP($inventario['fechapedido_inv']),
                        13=> $obj_fn->fecha_ENG_ESP($inventario['fecharec_inv']),
                        14=> $btnAdd
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

    public function list_View_Rows_File(){
        try {
            $filename = $_FILES['filedata_import']['tmp_name'];

            $array = explode('.', $_FILES['filedata_import']['name']);
            $extension = end($array);
            $readerType = null;
            $type = 0; // error definido
            $mensaje = "El archivo adjunto no tiene un formato valido.";
            $datosArray = array();
            if(trim($extension) == 'xlsx'){ $readerType = 'Xlsx'; }
            else if(trim($extension) == 'xls'){ $readerType = 'Xls'; }
            $obj_fn = new FuncionesModel();

            if(!is_null($readerType)) {
                $reader = IOFactory::createReader($readerType);
                $spreadsheet = $reader->load($filename);
                $sheetCount = $spreadsheet->getSheetCount();
                date_default_timezone_set('America/New_York');

                if ($sheetCount == 1 || $sheetCount == 2 ) {
                    $worksheet = $spreadsheet->setActiveSheetIndex(0);
                    $data = $worksheet->toArray();
                    $arreglo = array();
                    $fila_vacio = 0;
                    for ($row = 3; $row <= sizeof($data); $row++) {
                        unset($arreglo);
                        $column_vacio = 0;
                        for ($t = 0; $t <= 19; $t++) {
                            if ($data[$row][$t] != null || !empty($data[$row][$t])) {
                                if ($t == 1) {
                                    $arreglo[] = number_format(trim($data[$row][$t]), 2);
                                }

                                else if ($t == 7 || $t == 10 || $t == 16) {
                                    $arreglo[] = $obj_fn->fecha_ESP_ENG($data[$row][$t]);
                                }
                                else {
                                    $arreglo[] = trim($data[$row][$t]);
                                }
                            } else {
                                $arreglo[] = "";
                                $column_vacio++;
                            }
                        }

                        //verificamos cuantos valores nulos tiene default = 8
                        if ($column_vacio <= 13) {
                            array_push($datosArray, $arreglo);
                        } else {
                            $fila_vacio++;
                        }

                    }

                    if ($fila_vacio < sizeof($data)) {
                        $type = 1; //correcto
                        $mensaje = "Archivo cargado correctamente.";
                    }
                    else {
                        $type = 3; //archivo vacio
                        $mensaje = "Archivo adjunto no contiene información a mostrar.";
                    }
                }
                else if ($sheetCount > 2) {
                    $type = 2; // archivo con muchas hojas
                    $mensaje = "El archivo adjunto contiene varias hojas adjuntas, solo se admite el formato según la plantilla requerida.";
                }

            }

            $response = array(
                'status'=> $type,
                'message'=> $mensaje,
                'data'=> $datosArray
            );

            echo json_encode($response);

        }
        catch (PDOException $e) {
            throw $e;
        }
        catch (Exception $e) {
        }
    }

    public function registrar_Inventario_Import_JSON(){
        try {
            $obj_fn = new FuncionesModel();
            date_default_timezone_set("America/Lima");
            setlocale(LC_TIME, 'es_PE.UTF-8');
            $idUsuario = $obj_fn->encrypt_decrypt('decrypt',$_POST['idus']);
            $tipoLoad = (int)$_POST['tipoload'];
            $idalm = (int)$_POST['idalm'];
            $codigoMaterial = "";if(!is_null($_POST['n1'])){ $codigoMaterial = trim($_POST['n1']);}
            $cantidad = 0;if(!is_null($_POST['n2'])){ $cantidad = (int)$_POST['n2'];}
            $desItem = "";if(!is_null($_POST['n3'])){ $desItem = trim($_POST['n3']);}
            $undMedida = "";if(!is_null($_POST['n4'])){ $undMedida = trim($_POST['n4']);}
            $nroParte = "";if(!is_null($_POST['n5'])){ $nroParte = trim($_POST['n5']);}
            $marca = "";if(!is_null($_POST['n6'])){ $marca = trim($_POST['n6']);}
            $observ = "";if(!is_null($_POST['n7'])){ $observ = trim($_POST['n7']);}
            $fechaRecepcion = "0000-00-00";if(!is_null($_POST['n8'])){ $fechaRecepcion = trim($_POST['n8']);}
            $IdClasificacion = "";if(!is_null($_POST['n9'])){ $IdClasificacion = trim($_POST['n9']);}
            $nroGuia = "";if(!is_null($_POST['n10'])){ $nroGuia = trim($_POST['n10']);}
            $fechaUltcalibra = "0000-00-00";if(!is_null($_POST['n11'])){ $fechaUltcalibra = trim($_POST['n11']);}
            $frecCalibracion = 0;if((int)$_POST['n12'] > 0){ $frecCalibracion = (int)$_POST['n12'];}
            $cActivo = "";if(!is_null($_POST['n13'])){ $cActivo = trim($_POST['n13']);}
            $cInventario = "";if(!is_null($_POST['n14'])){ $cInventario = trim($_POST['n14']);}
            $cMapel = "";if(!is_null($_POST['n15'])){ $cMapel = trim($_POST['n15']);}
            $cONU = "";if(!is_null($_POST['n16'])){ $cONU = trim($_POST['n16']);}

            $fechaDepreciacion = "0000-00-00";if(!is_null($_POST['n17'])){ $fechaDepreciacion = trim($_POST['n17']);}
            $costoActivo = "";if(!is_null($_POST['n18'])){ $costoActivo = trim($_POST['n18']);}
            $frecDepreciacion = 0;if((int)$_POST['n19'] > 0){ $frecDepreciacion = (int)$_POST['n19'];}
            $valDepreciacion = 0;if((int)$_POST['n20'] > 0){ $valDepreciacion = (int)$_POST['n20'];}

            $val = 0;
            $message = "Error al realizar el registro.";
            if(!empty($codigoMaterial) && !empty($nroParte)){
                $datesTAB[0] = $idalm;
                $datesTAB[1] = $codigoMaterial;
                $datesTAB[2] = $cantidad;
                $datesTAB[3] = $desItem;
                $datesTAB[4] = $undMedida;
                $datesTAB[5] = $nroParte;
                $datesTAB[6] = $marca;
                $datesTAB[7] = $observ;
                $datesTAB[8] = $idUsuario;
                $datesTAB[9]= date("Y-m-d H:i:s");
                $datesTAB[10]= $fechaRecepcion;
                $datesTAB[11]= $IdClasificacion;
                $datesTAB[12]= $nroGuia;
                $datesTAB[13]= $fechaUltcalibra;
                $datesTAB[14]= $frecCalibracion;
                $datesTAB[15]= $fechaDepreciacion;
                $datesTAB[16]= $costoActivo;
                $datesTAB[17]= $frecDepreciacion;
                $datesTAB[18]= $valDepreciacion;
                $datesTAB[19]= $cActivo;
                $datesTAB[20]= $cInventario;
                $datesTAB[21]= $cMapel;
                $datesTAB[22]= $cONU;

                $datesSearch[0] = $idalm;
                $datesSearch[1] = trim($codigoMaterial);
                /*$datesSearch[2] = trim($nroParte);
                $datesSearch[3] = trim($cActivo);*/
                $obj_inv = new InventarioModel();
                $dtlleItem = $obj_inv->busca_existencia_Item_xCodMaterial($datesSearch);
                if(is_null($dtlleItem)){
                    $inserInventID= $obj_inv->registrar_Item_lastID($datesTAB);
                    if($inserInventID > 0) {
                        $val = 1;
                        $message = "Ítem registrado satisfactoriamente";
                        //Si el tipo de carga de datos es masivo = 2, generamos movimiento tipo IN
                        if ($tipoLoad == 2) {
                            $obj_us = new UsuarioModel();
                            $dtlleUsuario = $obj_us->detalle_Usuario_xID($idUsuario);
                            $textPersona = "";
                            if (is_array($dtlleUsuario)) {
                                $obj_per = new PersonaModel();
                                $dtllePersona = $obj_per->detalle_Persona_xID($dtlleUsuario['id_per']);
                                if (is_array($dtllePersona)) {
                                    $textPersona = $dtllePersona['ape_pa_per'] . " " . $dtllePersona['nombres_per'];
                                }
                            }

                            $obj_alm = new AlmacenModel();
                            $dtlleAlmacen = $obj_alm->detalle_Almacen_xID($idalm);
                            $IdServicio = $dtlleAlmacen['id_serv'];
                            $dtlleCorrelativo = $obj_alm->detalle_correlativo_Almacen($idalm);
                            $numCorrelativo = "";
                            if (is_array($dtlleCorrelativo)) {
                                $numCorrelativo = "IN-" . $IdServicio . "-" . str_pad((int)$dtlleCorrelativo['val_alc'] + 1, 6, "0", STR_PAD_LEFT);
                            }

                            $datesMOV[0] = "IN";
                            $datesMOV[1] = $idalm;//Almacen inicio
                            $datesMOV[2] = ""; //Unidad
                            $datesMOV[3] = ""; //orden Mantto
                            $datesMOV[4] = $idalm;//Almacen destino
                            $datesMOV[5] = $textPersona;//Recibido
                            $datesMOV[6] = $textPersona;//Nro DNI
                            $datesMOV[7] = $textPersona;//Autorizado por
                            $datesMOV[8] = $observ; //Observaciones
                            $datesMOV[9] = ""; //Documento
                            $datesMOV[10] = ""; //motivo
                            $datesMOV[11] = date("Y-m-d"); //Fecha
                            $datesMOV[12] = $numCorrelativo; //Nro Transac
                            $datesMOV[13] = $idUsuario; //Id Usuario
                            $datesMOV[14] = date("Y-m-d H:i:s"); //Fecha sistema
                            $datesMOV[15] = ""; //Entregado
                            $datesMOV[16] = ""; //NRO VALE
                            $datesMOV[17] = 0; //idMoV retiro
                            $datesMOV[18]= ""; //Area Operativa

                            $obj_mov = new MovimientoModel();
                            $insertID = $obj_mov->registrar_Movimiento_Item_lastID($datesMOV);
                            if ((int)$insertID > 0) {
                                $datesDEMOV[0] = $insertID;
                                $datesDEMOV[1] = $inserInventID;
                                $datesDEMOV[2] = $codigoMaterial;
                                $datesDEMOV[3] = $desItem;
                                $datesDEMOV[4] = $nroParte;
                                $datesDEMOV[5] = $cantidad;
                                $datesDEMOV[6] = $cantidad;//Stock
                                $datesDEMOV[7] = $IdClasificacion;
                                $datesDEMOV[8] = $undMedida;
                                $datesDEMOV[9] = $marca;
                                $datesDEMOV[10]= $cActivo;
                                $datesDEMOV[11]= $cInventario;
                                $datesDEMOV[12]= $cMapel;
                                $datesDEMOV[13]= $cONU;
                                $insertDetalle = $obj_mov->registrar_Movimiento_Item_Detalle($datesDEMOV);
                                if ($insertDetalle) {
                                    //Restamos la cantidad del item del almacen inicio
                                    $datesCorrel[0] = $idalm;
                                    $datesCorrel[1] = (int)$dtlleCorrelativo['val_alc'] + 1;
                                    $obj_alm = new AlmacenModel();
                                    $obj_alm->actualizar_Correlativo_Almacen($datesCorrel);
                                }
                            }
                        }
                    }
                }
                else{
                    $val = 2;
                    $message = "El ïtem a ingresar ya existe en el almacen, No es posible su registro";
                }
            }

            echo json_encode(array('status'=>$val, 'message'=>$message));
        }
        catch (PDOException $e) {
            throw $e;
        }
    }

    public function loadCampos_RetirarItem_Ajax(){
        try {
            $idus = $_GET['idus'];
            $idsu = (int)$_GET['idsu'];
            $idalm = (int)$_GET['idalm'];
            $obj_serv = new ServicioModel();
            $dtlleServUsuario = $obj_serv->detalle_ServicioUsuario_xIDSU($idsu);
            $IdServicio = $dtlleServUsuario['id_serv'];
            $obj_alm = new AlmacenModel();
            $dtlleAlmacen = $obj_alm->detalle_Almacen_xID($idalm);
            $dtlleCorrelativo = $obj_alm->detalle_correlativo_Almacen($idalm);
            $numCorrelativo = "";
            if(is_array($dtlleCorrelativo)){
                $numCorrelativo = "SO-".$IdServicio."-".str_pad((int)$dtlleCorrelativo['val_alc'] + 1,6,"0",STR_PAD_LEFT);
            }?>
            <form id="formRetiroItem" role="form" method="post">
                <input type="hidden" id="idusitm_tk" name="idusitm_tk" value="<?=$idus?>">
                <input type="hidden" id="idalm_i" name="idalm_i" value="<?=$idalm?>">
                <input type="hidden" id="fechareg_i" name="fechareg_i" value="<?=date("Y-m-d")?>">
                <input type="hidden" id="idalc" name="idalc" value="<?=$dtlleCorrelativo['id_alc']?>">
                <input type="hidden" id="valalc" name="valalc" value="<?=(int)$dtlleCorrelativo['val_alc']?>">
                <input type="hidden" id="nroretiro_i" name="nroretiro_i" value="<?=$numCorrelativo?>">
                <input type="hidden" id="autogenvale_i" name="autogenvale_i" value="<?=(int)$dtlleAlmacen['autogen_alm']?>">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title">
                                <h4 class="mb-0 text-brown-800 font-weight-bold">
                                    Retiro : <code class="text-danger-800"><?=$dtlleAlmacen['titulo_alm']?></code>
                                </h4>
                                <ol class="breadcrumb mb-0 pl-0 pt-1 pb-0">
                                    <li class="breadcrumb-item text-muted">Realice el retiro de uno o más ítems.</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12" id="mensajes_actions_act"></div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 mb-10">
                            <div class="card mb-4 card-shadow">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-3">
                                            <span class="bg-primary rounded-circle text-center wb-icon-box">
                                                <i class="icon-notebook text-light f24"></i>
                                            </span>
                                        </div>
                                        <div class="col-9">
                                            <h3 class="mt-1 mb-0" id="infoCorrelativo"><?=$numCorrelativo?></h3>
                                            <p class="f12 mb-0">Número Transacción</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 mb-10">
                            <div class="card mb-4 card-shadow">
                                <div class="card-body ">
                                    <div class="row">
                                        <div class="col-3">
                                                    <span class="bg-warning rounded-circle text-center wb-icon-box">
                                                        <i class="icon-calendar text-light f24"></i>
                                                    </span>
                                        </div>
                                        <div class="col-9">
                                            <h3 class="mt-1 mb-0"><?=date("d/m/Y H:s")?></h3>
                                            <p class="f12 mb-0">Fecha y hora registro</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-20 card-shadow">
                        <div class="card-header bg-secondary-light-5">
                            <h4 class="card-title font-weight-bold">Datos Generales</h4>
                        </div>
                        <hr class="no-padding no-margin">
                        <div class="card-body">
                            <p class="text-muted">
                                Todos los campos descritos con <code class="font-weight-bold">(*)</code>, son campos obligatorios.
                            </p>

                            <?php
                                if((int)$dtlleAlmacen['autogen_alm'] == 1){
                                    $nextNumVale = (int)$dtlleAlmacen['numautogen_alm'] + 1;?>
                                    <div class="row">
                                        <label for="nrovale_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left text-danger-800 font-weight-bold fz-13">
                                            Número Vale salida
                                            <span class="text-danger font-weight-bold">*</span>
                                        </label>
                                        <div class="col-sm-3">
                                            <input type="hidden"  id="nrovale_itm" name="nrovale_itm" value="<?=$nextNumVale?>">
                                            <input type="number" class="form-control border-input text-left text-danger-800 font-weight-bold fz-15"
                                                   disabled value="<?=str_pad($nextNumVale,8,"0",STR_PAD_LEFT)?>">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-xl-9 col-lg-9 col-md-9 col-sm-12 text-left offset-xl-4 offset-lg-4 offset-md-4">
                                            <small class="form-text text-muted fz-12">Número referencia, puede variar si existe un retiro simultaneo</small>
                                        </div>
                                    </div>
                                    <?php
                                }
                                else{?>
                                    <div class="form-group row">
                                        <label for="nrovale_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left text-danger-800 font-weight-bold fz-13">
                                            Número Vale salida
                                            <span class="text-danger font-weight-bold">*</span>
                                        </label>
                                        <div class="col-sm-3">
                                            <input type="number" class="form-control border-input text-left text-danger-800 font-weight-bold fz-15 activeCopy"
                                                   placeholder="nro. vale" tabindex="1"
                                                   id="nrovale_itm" name="nrovale_itm" required="required" autocomplete="off" maxlength="8"
                                                   oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                   step="1" min="1" onkeydown="return event.keyCode !== 69">
                                            <small class="form-text text-muted">Máximo 8 digitos</small>
                                        </div>
                                    </div>
                                    <?php
                                }
                            ?>
                            <div class="form-group row">
                                <label for="aper_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Atención a
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-5">
                                    <div class="input-group">
                                        <input type="text" class="form-control input-md text-left"  id="adoc_itm" name="adoc_itm" maxlength="12"
                                               placeholder="# documento"  required autocomplete="off" onkeypress="return sga.funcion.valideKey(event);">
                                        <div class="input-group-append">
                                            <button class="btn btn-sm btn-outline-secondary" type="button" id="btnSearchPersonal">
                                                <i class="ti-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">Máximo 12 carácteres</small>
                                </div>
                                <div id="progressID"></div>
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-5">
                                    <input type="text" class="form-control input-md text-left" autocomplete="off" name="aper_itm" id="aper_itm" maxlength="40" placeholder="nombres y apellidos" required>
                                    <small class="form-text text-muted">Máximo 40 carácteres</small>
                                </div>

                            </div>

                            <div class="form-group row">
                                <label for="autorizado_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left" id="tituloAutorizadoPor">
                                    Autorizado Por
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control input-md text-left activeCopy"
                                           id="autorizado_itm" name="autorizado_itm" maxlength="45" required
                                           placeholder="ingrese nombre del autorizante">
                                    <small class="form-text text-muted float-left">Máximo 45 carácteres</small>
                                    <div class="form-check float-right">
                                        <label class="form-check-label cursor-pointer">
                                            <input class="form-check-input" type="checkbox" id="chk_notAutorizado" name="chk_notAutorizado" value="1">
                                            <span class="font-weight-bold text-danger-800">Marque cuando NO APLICA</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-20 card-shadow">
                        <div class="card-header bg-secondary-light-5">
                            <h4 class="card-title font-weight-bold">Datos Complementarios (Opcionales)</h4>
                        </div>
                        <hr class="no-padding no-margin">
                        <div class="card-body">
                            <div class="form-group row">
                                <label for="area_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Área operativa
                                </label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control input-md text-left" autocomplete="off"
                                           name="area_itm" id="area_itm" maxlength="100" placeholder="área">
                                    <small class="form-text text-muted">Máximo 100 carácteres</small>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="area_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Tipo Cargo
                                </label>
                                <div class="col-sm-3">
                                    <select class="form-control" name="tipocargo_itm" id="tipocargo_itm">
                                        <option value="" selected>Seleccione...</option>
                                        <option value="Diario">Diario</option>
                                        <option value="Asignación">Asignación</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Observaciones
                                </label>
                                <div class="col-sm-7">
                                    <textarea class="form-control activeCopy" name="obs_itm" id="obs_itm" maxlength="3000"
                                              rows="5" cols="1" placeholder="obervaciones"></textarea>
                                    <small class="form-text text-muted">Máximo 3000 carácteres</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container-fluid">
                    <div class="page-title text-center">
                        <h4 class="mb-0 text-brown-800 font-weight-bold">
                            Seleccione los Ítems a Retirar
                        </h4>
                        <p class="text-muted text-center">Agregue cada ítems e ingrese la cantidad a retirar.</p>
                    </div>
                    <div class="card mb-20 card-shadow">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 text-lg-left text-md-left text-sm-center">
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" placeholder="Ingrese un código a buscar"
                                               name="txtcodSearching" id="txtcodSearching">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="btnSearchItemInventario" data-idalm="<?=$idalm?>">
                                                <i class="ti-search position-left"></i>
                                                Buscar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 text-lg-right text-md-right text-sm-center">
                                    <button type="button" class="btn btn-outline-secondary btn-hover-transform float-right"
                                            id="btnLoad_ItemAlmacen" data-idalm="<?=$idalm?>">
                                        <i class="fa fa-plus mr-10"></i>
                                        Agregar Ítems
                                    </button>
                                </div>
                            </div>
                        </div>
                        <hr class="no-padding no-margin">
                        <div class="table-responsive">
                            <table id="Tbl_DetalleItem" class="table mb-0 table-bordered table-striped table-sm">
                                <thead class="">
                                <tr>
                                    <th width="30">#</th>
                                    <th class="text-center">Código</th>
                                    <th class="text-left">Descripción</th>
                                    <th class="text-center">Unid.Med.</th>
                                    <th class="text-center">Nro.Parte/Serie</th>
                                    <th class="text-center" width="150">Cantidad</th>
                                    <th class="text-center" width="100">Stock</th>
                                    <th width="50"></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td colspan="8" class="text-center">No se encontraron ítems agregados.</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-info btn-hover-transform btn-lg mr-20">
                            <i class="ti-save position-left"></i>
                            Registrar retiro
                        </button>
                        <button type="button" id="btnCancel" class="btn btn-light btn-lg">
                            Cancelar
                        </button>
                    </div>
                </div>
            </form>
            <br><br><br>
            <?php
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function retirar_Item_JSON(){
        try {
            date_default_timezone_set("America/Lima");
            setlocale(LC_TIME, 'es_PE.UTF-8');
            $obj_fn = new FuncionesModel();
            $idalm = (int)$_POST['idalm_i'];
            $detalleItems = $_POST['detalle'];
            $idalc = (int)$_POST['idalc'];
            $valalc = (int)$_POST['valalc'];

            $formatRetiro = explode("-",$_POST['nroretiro_i']);
            $nroTransac = $_POST['nroretiro_i'];

            $chkNotAutorizadoPor = 0;
            if(isset($_POST['chk_notAutorizado'])){ $chkNotAutorizadoPor = 1; }

            $textAutorizadoPor = "NO APLICA AUTORIZACIÓN";
            if($chkNotAutorizadoPor == 0){
                $textAutorizadoPor = trim($_POST['autorizado_itm']);
            }

            $codigoTransac = trim($formatRetiro[0]);

            $nroVale = 0;
            if(isset($_POST['nrovale_itm'])){ $nroVale = (int)$_POST['nrovale_itm']; }

            $areaOperativa = "";
            if(isset($_POST['areaoperativa'])){ $areaOperativa = trim($_POST['areaoperativa']); }
            $tipoCargo = "";
            if(isset($_POST['tipocargo'])){ $tipoCargo = trim($_POST['tipocargo']); }

            $textoArea = $areaOperativa;
            if(!empty($areaOperativa) && !empty($tipoCargo)){
                $textoArea = $areaOperativa."/".$tipoCargo;
            }

            if((int)$_POST['autogenvale'] == 1){
                $obj_mv = new MovimientoModel();
                $obj_alm = new AlmacenModel();
                //verificamos si existe el numero de vale generado
                $dtlleMovVale = $obj_mv->lista_Movimientos_xNumVale($idalm,$nroVale);
                if(!is_null($dtlleMovVale)){
                    $dtlleAlmacen = $obj_alm->detalle_Almacen_xID($idalm);
                    if(!is_null($dtlleAlmacen)) {
                        $nroVale = (int)$dtlleAlmacen['numautogen_alm'] + 1;
                    }
                }
                //Actualizamos correlativo de autogenerado
                $obj_alm->actualizar_Correlativo_NroVale_Autogenerado($idalm,$nroVale);
            }

            $datesMOV[0] = $codigoTransac;
            $datesMOV[1] = $idalm; //Almacen inicio
            $datesMOV[2] = ""; //unidad
            $datesMOV[3] = ""; //orden Mantto
            $datesMOV[4] = $idalm;//Almacen destino
            $datesMOV[5] = $_POST['solicitado_itm']; //Recibido
            $datesMOV[6] = $_POST['docsolicitado_itm']; //Nro DNI
            $datesMOV[7] = $textAutorizadoPor; //Autorizado
            $datesMOV[8] = $obj_fn->quitar_caracteresEspeciales($_POST['obs_itm']);; //Observaciones
            $datesMOV[9] = ""; //Documento
            $datesMOV[10]= ""; //motivo
            $datesMOV[11]= $_POST['fechareg_i']; //Fecha
            $datesMOV[12]= $nroTransac; //Nro Transac
            $datesMOV[13]= $obj_fn->encrypt_decrypt('decrypt',$_POST['idusitm_tk']); //Id Usuario
            $datesMOV[14]= date("Y-m-d H:i:s"); //Fecha sistema
            $datesMOV[15]= ""; //Entregado
            $datesMOV[16]= $nroVale; //NRO VALE
            $datesMOV[17]= 0;  //idMoV ref
            $datesMOV[18]= $textoArea; //Area Operativa
            $val = 0;
            $message = "Error al realizar el retiro.";
            $obj_mov = new MovimientoModel();

            $insertID = $obj_mov->registrar_Movimiento_Item_lastID($datesMOV);
            if((int)$insertID > 0){
                $val = 1;
                $message = "A continuación de click en <strong>ACEPTAR</strong> para generar el vale.";

                $acierto = 0;
                for($j=0; $j<sizeof($detalleItems);$j++){
                    $idInvent = (int)$detalleItems[$j][0];
                    $cantSolic = $detalleItems[$j][8];

                    $obj_inv= new InventarioModel();
                    $dtlleInventario = $obj_inv->detalle_Item_xID($idInvent);

                    $datesMOV[0] = $insertID; //idMov
                    $datesMOV[1] = $idInvent; //idInv
                    $datesMOV[2] = $detalleItems[$j][1];//Codigo
                    $datesMOV[3] = $detalleItems[$j][2]; //Descripcion
                    $datesMOV[4] = $detalleItems[$j][3]; //Nro.Parte
                    $datesMOV[5] = $cantSolic; //Cantidad
                    $datesMOV[6] = $detalleItems[$j][9];//Stock
                    $datesMOV[7] = $dtlleInventario['id_cla'];
                    $datesMOV[8] = $dtlleInventario['um_inv'];
                    $datesMOV[9] = $detalleItems[$j][10]; //Marca
                    $datesMOV[10]= $detalleItems[$j][4];//C.Activo
                    $datesMOV[11]= $dtlleInventario['cinventario_inv'];//C.Inventario
                    $datesMOV[12]= $dtlleInventario['cmapel_inv'];//C.Mapel
                    $datesMOV[13]= $dtlleInventario['conu_inv'];//C.ONU
                    $insertDetalle = $obj_mov->registrar_Movimiento_Item_Detalle($datesMOV);

                    if($insertDetalle){
                        //Restamos la cantidad del item del almacen inicio
                        $valRestaStock = (float)$dtlleInventario['cant_inv'] - (float)$cantSolic;
                        $datesUStock[0] = $idInvent;
                        $datesUStock[1] = $valRestaStock;
                        $obj_inv->actualizar_Stock_Item($datesUStock);
                        $acierto++;
                    }
                }

                if($acierto == sizeof($detalleItems) || $acierto > 0){
                    $val = 1;
                    $datesCorrel[0] = $idalc;
                    $datesCorrel[1] = $valalc + 1;
                    $obj_alm = new AlmacenModel();
                    $obj_alm->actualizar_Correlativo_Almacen($datesCorrel);
                }
            }

            echo json_encode(array('status'=>$val, 'message'=>$message,'idmovimiento'=>$insertID,'nvale'=>$nroVale));
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function loadCampos_searchVale_JSON(){
        try {
            $idus = $_GET['idus'];
            $idsu = (int)$_GET['idsu'];
            $idalm = (int)$_GET['idalm'];
            $obj_alm = new AlmacenModel();
            $dtlleAlmacen = $obj_alm->detalle_Almacen_xID($idalm);?>
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="page-title">
                            <h4 class="mb-0 text-brown-800 font-weight-bold">
                                Devolver a: <code class="text-danger-800"><?=$dtlleAlmacen['titulo_alm']?></code>
                            </h4>
                            <ol class="breadcrumb mb-0 pl-0 pt-1 pb-0">
                                <li class="breadcrumb-item text-muted">Realice de una devolución de uno o más ítems.</li>
                            </ol>
                        </div>
                    </div>
                </div>
                <div class="card card-body card-shadow mb-20">
                    <form class="container" id="busarValeForm">
                        <input type="hidden" name="idaml_val" value="<?=$idalm?>">
                        <input type="hidden" name="idus_val" value="<?=$idus?>">
                        <input type="hidden" name="idsu_val" value="<?=$idsu?>">
                        <div class="row">
                            <label class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12 col-form-label text-lg-right text-md-right text-left fz-13">
                                Número Vale
                                <span class="text-danger font-weight-bold">*</span>
                            </label>
                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <input type="number" class="form-control border-input text-center fz-15"
                                       placeholder="número" tabindex="1" id="nrovale_itm" name="nrovale_itm" required="required" autocomplete="off" maxlength="8"
                                       oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                       step="1" min="1" onkeydown="return event.keyCode !== 69">
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <button type="submit" class="btn btn-primary btn-hover-transform text-white btn-block">
                                    <i class="ti-search mr-7"></i>
                                    Buscar vale
                                </button>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-xs-12">
                                <button type="button" class="btn btn-danger btn-hover-transform btn-block" id="btnCancel">
                                    Cancelar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="container-fluid" id="divBusquedaEvol"></div>
            <?php
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function loadCampos_DevolverItem(){
        try {
            $idalm = (int)$_GET['idaml_val'];
            $nroVale = $_GET['nrovale_itm'];
            $idus = $_GET['idus_val'];
            $idsu = (int)$_GET['idsu_val'];

            $datesMOV[0] = $idalm;
            $datesMOV[1] = $nroVale;
            $obj_mov = new MovimientoModel();
            $dtlleMovimiento = $obj_mov->detalle_Movimiento_xVale($datesMOV);
            if(is_array($dtlleMovimiento)){
                $obj_serv = new ServicioModel();
                $dtlleServUsuario = $obj_serv->detalle_ServicioUsuario_xIDSU($idsu);
                $IdServicio = $dtlleServUsuario['id_serv'];
                $obj_alm = new AlmacenModel();
                $dtlleCorrelativo = $obj_alm->detalle_correlativo_Almacen($idalm);
                $numCorrelativo = "";
                if(is_array($dtlleCorrelativo)){
                    $numCorrelativo = "DV-".$IdServicio."-".str_pad((int)$dtlleCorrelativo['val_alc'] + 1,6,"0",STR_PAD_LEFT);
                }
                $lstItemMov = $obj_mov->lista_MovimientoDetalle_xIdMovimiento($dtlleMovimiento['id_mov']);
                $itemADevolver = 0;?>
                    <input type="hidden" id="nItemMov" value="<?=sizeof($lstItemMov)?>">
                    <form id="formDevolverItem" role="form" method="post">
                        <input type="hidden" id="idmov_ret" name="idmov_ret" value="<?=$dtlleMovimiento['id_mov']?>">
                        <input type="hidden" id="idusitm_tk" name="idusitm_tk" value="<?=$idus?>">
                        <input type="hidden" id="idalm_i" name="idalm_i" value="<?=$idalm?>">
                        <input type="hidden" id="fechareg_i" name="fechareg_i" value="<?=date("Y-m-d")?>">
                        <input type="hidden" id="idalc" name="idalc" value="<?=$dtlleCorrelativo['id_alc']?>">
                        <input type="hidden" id="valalc" name="valalc" value="<?=(int)$dtlleCorrelativo['val_alc']?>">
                        <input type="hidden" id="nrodevolver_i" name="nrodevolver_i" value="<?=$numCorrelativo?>">

                        <div class="container">
                            <div class="card mb-20 card-shadow">
                                <div class="card-body bg-secondary-light-5">
                                    <h4 class="card-title">Detalle de la devolución</h4>
                                    <h6 class="card-subtitle text-muted font-weight-normal fz-12"> Todos los campos descritos con <code class="font-weight-bold">(*)</code>, son campos obligatorios.</h6>
                                </div>
                                <hr class="no-padding no-margin">
                                <div class="row">
                                    <div class="col-12" id="mensajes_actions_act"></div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 mb-10">
                                        <div class="card mb-0">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-3">
                                                        <span class="bg-primary rounded-circle text-center wb-icon-box">
                                                            <i class="icon-notebook text-light f24"></i>
                                                        </span>
                                                    </div>
                                                    <div class="col-9">
                                                        <h3 class="mt-1 mb-0" id="infoCorrelativo"><?=$numCorrelativo?></h3>
                                                        <p class="f12 mb-0">Número Transacción</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 mb-10">
                                        <div class="card mb-0">
                                            <div class="card-body ">
                                                <div class="row">
                                                    <div class="col-3">
                                                            <span class="bg-warning rounded-circle text-center wb-icon-box">
                                                                <i class="icon-calendar text-light f24"></i>
                                                            </span>
                                                    </div>
                                                    <div class="col-9">
                                                        <h3 class="mt-1 mb-0"><?=date("d/m/Y H:s")?></h3>
                                                        <p class="f12 mb-0">Fecha y hora registro</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="no-padding no-margin">
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                            Motivo de la Devolución
                                            <span class="text-danger font-weight-bold">*</span>
                                        </label>
                                        <div class="col-sm-6">
                                                <textarea class="form-control" name="motivo_itm" id="motivo_itm" maxlength="3000" required
                                                          rows="3" cols="1" placeholder="describa el motivo por el cual realiza la devolución."></textarea>
                                            <small class="form-text text-muted">Máximo 3000 carácteres</small>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="entregado_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                            Entregado Por
                                            <span class="text-danger font-weight-bold">*</span>
                                        </label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control input-md text-left"
                                                   id="entregado_itm" name="entregado_itm" maxlength="45" required
                                                   placeholder="ingrese nombre del receptor" value="<?=$dtlleMovimiento['solicitado_mov']?>">
                                            <small class="form-text text-muted">Máximo 45 carácteres</small>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="entregado_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                            Área de retorno
                                        </label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control input-md text-left"
                                                   id="arearetorno_itm" name="arearetorno_itm" maxlength="100" required
                                                   placeholder="área" value="<?=$dtlleMovimiento['areaoperativa_mov']?>">
                                            <small class="form-text text-muted">Máximo 100 carácteres</small>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                            Observaciones
                                        </label>
                                        <div class="col-sm-7">
                                                <textarea class="form-control" name="obs_itm" id="obs_itm" maxlength="3000"
                                                          rows="5" cols="1" placeholder="obervaciones"></textarea>
                                            <small class="form-text text-muted">Máximo 3000 carácteres</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="container-fluid">
                            <div class="card mb-20 card-shadow">
                                <div class="card-body bg-secondary-light-5">
                                    <div class="float-right">
                                        <h4>Retiro: <span class="font-weight-bold text-danger-400"><?=$dtlleMovimiento['nro_mov']?></span></h4>
                                    </div>
                                    <h4 class="card-title font-weight-bold">ÍTEMS RETIRADOS </h4>
                                    <h6 class="card-subtitle text-muted font-weight-normal fz-12">Indique la cantidad a devolver, caso contrario elimine los ítem que no se devolveran de la lista.</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                            <label class="col-form-label text-left mb-0 pb-0 text-muted">
                                                Retirado por
                                            </label>
                                            <input type="text" class="form-control input-md text-left" readonly
                                                   placeholder="ingrese nombre del solicitante" value="<?=$dtlleMovimiento['solicitado_mov']?>">
                                        </div>
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                            <label class="col-form-label text-left mb-0 pb-0 text-muted">
                                                Documento de retiro (DNI/CE)
                                            </label>
                                            <input type="text" class="form-control input-md text-left" readonly
                                                   placeholder="ingrese nombre del receptor" value="<?=$dtlleMovimiento['recibido_mov']?>">
                                        </div>
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                            <label class="col-form-label text-left mb-0 pb-0 text-muted">
                                                Autorizado Por
                                            </label>
                                            <input type="text" class="form-control input-md text-left" readonly
                                                   placeholder="ingrese nombre del autorizante" value="<?=$dtlleMovimiento['autorizado_mov']?>">
                                        </div>

                                    </div>
                                </div>
                                <hr class="no-padding no-margin">
                                <div class="table-responsive">
                                    <table id="Tbl_detalleRetiro" class="table mb-0 table-bordered table-striped table-sm">
                                        <thead class="">
                                        <tr>
                                            <th width="30" class="align-middle">#</th>
                                            <th class="text-center align-middle">Código</th>
                                            <th class="text-left align-middle">Descripción</th>
                                            <th class="text-center align-middle">Nro.Parte/Serie</th>
                                            <th class="text-center align-middle" width="150">Cant. Retirada</th>
                                            <th class="text-center align-middle bg-warning" width="150">Cant. Devuelta</th>
                                            <th class="text-center align-middle" width="150">Cant. Devolver</th>
                                            <th width="50"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        if(is_array($lstItemMov)){
                                            $row = 1;
                                            foreach ($lstItemMov as $retiroItem){
                                                $datSearchMov[0] = $dtlleMovimiento['id_mov'];
                                                $datSearchMov[1] = $retiroItem['id_inv'];

                                                $dtlleDevolucionAnterior = $obj_mov->cantidad_devuelta_xIDInventario($datSearchMov);

                                                $cantidadDevuelta = 0;
                                                if(!is_null($dtlleDevolucionAnterior['devuelto'])){
                                                    $cantidadDevuelta = (int)$dtlleDevolucionAnterior['devuelto'] * (-1);
                                                }

                                                if($cantidadDevuelta < (int)$retiroItem['cant_mde']){
                                                    $bgResutl = "";
                                                    if($cantidadDevuelta >0){ $bgResutl = "bg-warning bold-600";}
                                                    $itemADevolver++;?>
                                                <tr>
                                                    <td class="text-left align-middle">
                                                        <?=$row?>
                                                        <input type="hidden" name="materialRetiroID[]"  value="<?=$retiroItem['id_inv']?>">
                                                    </td>
                                                    <td class="text-center align-middle"><?=$retiroItem['cod_mde']?></td>
                                                    <td class="text-left align-middle"><?=$retiroItem['des_mde']?></td>
                                                    <td class="text-center align-middle"><?=$retiroItem['nparte_mde']?></td>
                                                    <td class="text-center align-middle" width="150">
                                                        <input type="hidden" class="form-control" id="cantidadRetiro<?=$row?>" value="<?=(int)$retiroItem['cant_mde']?>">
                                                        <?=(int)$retiroItem['cant_mde']?>
                                                    </td>

                                                    <td class="text-center align-middle <?=$bgResutl?>">
                                                        <?=$cantidadDevuelta?>
                                                    </td>
                                                    <td class="text-center align-middle" width="150">
                                                        <input type="text" name="cantDevolver[]" class="form-control text-center" id="cantidadDevol" value="1" data-pos="<?=$row?>" data-devolver="<?=(int)$retiroItem['cant_mde']-$cantidadDevuelta?>">
                                                    </td>
                                                    <td  class="text-center align-middle" width="50">
                                                        <button type="button" class="btn btn-danger btn-hover-transform btn-xs" title="Eliminar ítem" id="btnDelete_ItemRetiro">
                                                            <i class="fa fa-minus" aria-hidden="true"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <?php
                                                $row++;
                                                }
                                            }
                                            if($itemADevolver == 0){?>
                                                <tr>
                                                    <td colspan="7" class="text-center">No se encontraron ítems pendientes a devolver.</td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        else{?>
                                            <tr>
                                                <td colspan="7" class="text-center">No se encontraron ítems agregados.</td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 text-center">
                                    <?php
                                    if($itemADevolver > 0){?>
                                        <button type="submit" class="btn btn-info btn-hover-transform btn-lg mr-20">
                                            <i class="ti-save position-left"></i>
                                            Registrar Devolución
                                        </button>
                                        <?php
                                    }
                                    ?>
                                    <button type="button" id="btnCancel" class="btn btn-light btn-lg">
                                        Cancelar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <br><br><br>
                <?php

            }
            else{?>
                <div class="alert alert-warning text-center" role="alert">
                    <h4 class="alert-heading">Movimiento</h4>
                    <p>No se encontro ningún movimiento realizado en el Almacén con el Número de vale [<strong><?=$nroVale?></strong>], verifique el número ingresado caso contrario vuelva a intentarlo.</p>
                </div>
                <?php
            }
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function devolver_Item_JSON(){
        try {
            date_default_timezone_set("America/Lima");
            setlocale(LC_TIME, 'es_PE.UTF-8');
            $obj_fn = new FuncionesModel();
            $idalm = (int)$_POST['idalm_i'];
            $idalc = (int)$_POST['idalc'];
            $valalc = (int)$_POST['valalc'];
            $idMovRetiro = (int)$_POST['idmov_ret'];

            $obj_mov = new MovimientoModel();
            $dtlleMoviRetiro = $obj_mov->detalle_Movimiento_xID($idMovRetiro);

            $txtObservacion = "";
            if(isset($_POST['obs_itm']) && !empty($_POST['obs_itm'])){ $txtObservacion = $obj_fn->quitar_caracteresEspeciales($_POST['obs_itm']); }

            $arrayItemRetiro   = $_POST['materialRetiroID'];
            $arrayCantDevolver = $_POST['cantDevolver'];

            $val = 0;
            $message = "Error al realizar la devolucion.";

            //Registramos el Movimiento de Devoilución
            $datesTAB[0] = "DV";
            $datesTAB[1] = $idalm;//Almacen inicio
            $datesTAB[2] = ""; //Unidad
            $datesTAB[3] = ""; //orden Mantto
            $datesTAB[4] = $idalm;//Almacen destino
            $datesTAB[5] = "";//Solicitado
            $datesTAB[6] = "";//Nro DNI
            $datesTAB[7] = "";//Autorizado por
            $datesTAB[8] = $txtObservacion;
            $datesTAB[9] = "";//Documento
            $datesTAB[10]= $obj_fn->quitar_caracteresEspeciales($_POST['motivo_itm']);
            $datesTAB[11]= $_POST['fechareg_i']; //Fecha
            $datesTAB[12]= $_POST['nrodevolver_i']; //Nro Transac
            $datesTAB[13]= $obj_fn->encrypt_decrypt('decrypt', $_POST['idusitm_tk']);//Id Usuario
            $datesTAB[14]= date("Y-m-d H:i:s");//Fecha sistema
            $datesTAB[15]= $_POST['entregado_itm'];//Entregado
            $datesTAB[16]= "";//NRO VALE
            $datesTAB[17]= $idMovRetiro; //idMoV Padre retiro
            $datesTAB[18]= "";//Area Operativa
            $insertID = $obj_mov->registrar_Movimiento_Item_lastID($datesTAB);
            if ((int)$insertID > 0) {
                $val = 1;
                $message = "Devolución realizada satisfactoriamente";
                $acierto = 0;
                $obj_inv = new InventarioModel();
                for ($j = 0; $j < sizeof($arrayItemRetiro); $j++) {
                    $dtlleItemInvent = $obj_inv->detalle_Item_xID($arrayItemRetiro[$j]);

                    $datesMOV[0] = $insertID;
                    $datesMOV[1] = $dtlleItemInvent['id_inv'];
                    $datesMOV[2] = $dtlleItemInvent['cod_inv'];
                    $datesMOV[3] = $dtlleItemInvent['des_inv'];
                    $datesMOV[4] = $dtlleItemInvent['nroparte_inv'];
                    $datesMOV[5] = "-".$arrayCantDevolver[$j];//cantidad
                    $datesMOV[6] = $dtlleItemInvent['cant_inv'];//Stock
                    $datesMOV[7] = $dtlleItemInvent['id_cla'];
                    $datesMOV[8] = $dtlleItemInvent['um_inv'];
                    $datesMOV[9] = $dtlleItemInvent['marca_inv'];
                    $datesMOV[10]= $dtlleItemInvent['cactivo_inv'];
                    $datesMOV[11]= $dtlleItemInvent['cinventario_inv'];
                    $datesMOV[12]= $dtlleItemInvent['cmapel_inv'];
                    $datesMOV[13]= $dtlleItemInvent['conu_inv'];
                    $insertDetalle = $obj_mov->registrar_Movimiento_Item_Detalle($datesMOV);
                    if ($insertDetalle) {
                        $datesUStock[0] = $dtlleItemInvent['id_inv'];
                        $datesUStock[1] = (float)$dtlleItemInvent['cant_inv'] + (float)$arrayCantDevolver[$j]; //Retornar al Almacén de retiro = Se suma
                        $obj_inv->actualizar_Stock_Item($datesUStock);
                        $acierto++;
                    }
                }

                if ($acierto == sizeof($arrayItemRetiro) || $acierto > 0) {
                    $datesCarrel[0] = $idalc;
                    $datesCarrel[1] = (int)$valalc + 1;
                    $obj_alm = new AlmacenModel();
                    $obj_alm->actualizar_Correlativo_Almacen($datesCarrel);
                }
            }

            echo json_encode(array('status'=>$val, 'message'=>$message));
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lst_Inventario_Reporte_xAlmacen_JSON(){
        try {
            $obj_fn = new FuncionesModel();
            $tipoReporte = (int)$_GET['tipo'];
            $idAlm = (int)$_GET['almacen'];

            $obj_alm = new InventarioModel();

            if($tipoReporte == 1) {
                $lstInventario = $obj_alm->listar_Inventario_xAlmacen_Rpte($idAlm);
            }
            else if($tipoReporte == 2) {
                $idbai = (int)$_GET['corte'];
                $lstInventario = $obj_alm->listar_Inventario_xCorte_Rpte($idbai);
            }

            $datos = array();
            if(is_array($lstInventario)){
                foreach($lstInventario as $inventario){

                    $cantSplit = explode(".",$inventario['cant_inv']);
                    $stock = number_format($inventario['cant_inv'], 2);
                    if($cantSplit[1] == "00"){
                        $stock = (int)$inventario['cant_inv'];
                    }

                    $row = array(
                        0 => $inventario['cod_inv'],
                        1 => $stock,
                        2 => $obj_fn->quitar_caracteresEspeciales($inventario['um_inv']),
                        3 => $obj_fn->quitar_caracteresEspeciales($inventario['des_inv']),
                        4 => $inventario['und_inv'],
                        5 => $inventario['om_inv'],
                        6=> $obj_fn->fecha_ENG_ESP($inventario['fecharec_inv'])
                    );
                    array_push($datos, $row);
                }
            }

            echo json_encode($datos);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lst_Inventario_Movimiento_xTransaccion_INT_JSON(){
        try {
            $obj_fn = new FuncionesModel();
            $optionReporte = (int)$_GET['optionReport'];
            if (!empty($_GET['almacen'])) {
                $where[] = " id_alm_ini = " . (int)$_GET['almacen'];
            }

            if (!empty($_GET['transac'])) {

                $arrayTransac = $_GET['transac'];

                for($t= 0; $t<sizeof($arrayTransac);$t++) {
                    if (trim($arrayTransac[$t]) == "SO"){
                        $arrayTransac[] = "SW";
                        $arrayTransac[] = "PT";
                    }

                    if (trim($arrayTransac[$t]) == "DV"){
                        $arrayTransac[] = "DC";
                    }
                }


                $txtAction = "";
                for($i=0; $i<sizeof($arrayTransac); $i++){
                    if($i == sizeof($arrayTransac) - 1){
                        $txtAction .=  " action_mov = '" . trim($arrayTransac[$i])."' ";
                    }
                    else{
                        $txtAction .=  " action_mov = '" . trim($arrayTransac[$i])."'  OR ";
                    }
                }
                $where[] = "(".$txtAction.") ";
            }

            if (!empty($_GET['fecha'])) {
                $fecha = explode("to", $_GET['fecha']);
                $where[] = " ( fecha_mov BETWEEN '" . $obj_fn->fecha_ESP_ENG(trim($fecha[0])) . "' AND '" . $obj_fn->fecha_ESP_ENG(trim($fecha[1])) . "' ) ";
            }

            if (is_array($where)) {
                $where = implode(" AND ", $where);
            }
            else {
                $where = "";
            }
            $obj_mov = new MovimientoModel();
            $lstMovimientos = $obj_mov->listar_Movimientos_xAlmacen($where);


            $datos = array();
            if(is_array($lstMovimientos)){
                $obj_alm = new AlmacenModel();
                foreach($lstMovimientos as $movimiento){
                    $almacenRetiro = "";
                    if((int)$movimiento['id_alm_des'] != 0) {
                        $dtlleAlmacen = $obj_alm->detalle_Almacen_xID($movimiento['id_alm_des']);
                        if(is_array($dtlleAlmacen)){
                            $almacenRetiro = $dtlleAlmacen['titulo_alm'];
                        }
                    }

                    $btnValeGuia = "";
                    $numberValeGuia = '<span class="font-weight-bold text-danger">--°--</span>';

                    if(($movimiento['action_mov'] == "SW" || $movimiento['action_mov'] == "SO" || $movimiento['action_mov'] == "PT") && $optionReporte == 1){
                        if(!is_null($movimiento['nrovale_mov'])){
                            $numberValeGuia = $movimiento['nrovale_mov'];
                        }
                        $btnValeGuia='<button type="button" class="btn btn-outline-danger btn-hover-transform fz-16" id="dataExportPDF_Vale" data-id="' . $movimiento['id_mov'] . '" title="Generar"><i class="fa fa-file-pdf-o mr-7"></i>Vale</button>';
                    }

                    $txtSolicitado = "-.-";
                    if(!is_null($movimiento['solicitado_mov'])){$txtSolicitado = $movimiento['solicitado_mov'];}
                    $nroDocumento = "-.-";
                    if(!is_null($movimiento['recibido_mov'])){$nroDocumento = $movimiento['recibido_mov'];}
                    $txtAutorizado = "-.-";
                    if(!is_null($movimiento['autorizado_mov'])){$txtAutorizado = $movimiento['autorizado_mov'];}


                    $row = array(
                        0 => $movimiento['nro_mov'],
                        1 => $obj_fn->fecha_ENG_ESP($movimiento['fecha_mov']),
                        2 => $txtSolicitado,
                        3 => $nroDocumento,
                        4 => $txtAutorizado,
                        5 => $almacenRetiro,
                        6 => $numberValeGuia,
                        7 => $btnValeGuia
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

    public function lst_Inventario_Movimiento_xTransaccion_Ext_JSON(){
        try {
            $obj_fn = new FuncionesModel();
            $optionReporte = (int)$_GET['optionReport'];
            if (!empty($_GET['almacen'])) {
                $where[] = " id_alm_ini = " . (int)$_GET['almacen'];
            }

            if (!empty($_GET['transac'])) {
                $arrayTransac = $_GET['transac'];
                $txtAction = "";
                for($i=0; $i<sizeof($arrayTransac); $i++){
                    if($i == sizeof($arrayTransac) - 1){
                        $txtAction .=  " action_mov = '" . trim($arrayTransac[$i])."' ";
                    }
                    else{
                        $txtAction .=  " action_mov = '" . trim($arrayTransac[$i])."'  OR ";
                    }
                }
                $where[] = "(".$txtAction.") ";
            }

            if (!empty($_GET['fecha'])) {
                $fecha = explode("to", $_GET['fecha']);
                $where[] = " ( fecha_mov BETWEEN '" . $obj_fn->fecha_ESP_ENG(trim($fecha[0])) . "' AND '" . $obj_fn->fecha_ESP_ENG(trim($fecha[1])) . "' ) ";
            }

            if (is_array($where)) {
                $where = implode(" AND ", $where);
            }
            else {
                $where = "";
            }
            $obj_mov = new MovimientoModel();

            $lstMovimientos = $obj_mov->listar_Movimientos_xAlmacen_TRAExterno($where);

            $datos = array();
            if(is_array($lstMovimientos)){
                $obj_alm = new AlmacenModel();
                foreach($lstMovimientos as $movimiento){
                    $almcenDestino = "";
                    if((int)$movimiento['id_alm_des'] != 0) {
                        $dtlleAlmacen = $obj_alm->detalle_Almacen_xID($movimiento['id_alm_des']);
                        if(is_array($dtlleAlmacen)){
                            $almcenDestino = $dtlleAlmacen['titulo_alm'];
                        }
                    }
                    $btnGuia = "";
                    $numberGuia = '<span class="font-weight-bold text-danger">--°--</span>';

                    if ($movimiento['action_mov'] == "TRA" && !is_null($movimiento['nroguia_mov'] && $optionReporte == 1)){
                        $numberGuia = $movimiento['nroguia_mov'];
                        $btnGuia='<button type="button" class="btn btn-outline-danger btn-hover-transform fz-16" id="dataExportPDF_Guia" data-opt="1" data-id="' . $obj_fn->encrypt_decrypt("encrypt",$movimiento['id_movt']) . '" title="Generar"><i class="fa fa-file-pdf-o mr-7"></i> Guia</button>';
                    }

                    $estdTra = '<span class="alert btn-block bg-warning mb-0" style="padding:.25rem 1.2rem;">Transito</span>';
                    if($movimiento['estd_transito'] == "I"){ $estdTra = '<span class="alert btn-block bg-success text-white mb-0" style="padding:.25rem 1.2rem;">Ingresado</span>';}

                    $txtSolicitado = "-.-";
                    if(!is_null($movimiento['solicitado_mov'])){$txtSolicitado = $movimiento['solicitado_mov'];}
                    $txtRecibido = "-.-";
                    if(!is_null($movimiento['recibido_mov'])){$txtRecibido = $movimiento['recibido_mov'];}


                    $row = array(
                        0 => $movimiento['nro_mov'],
                        1 => $obj_fn->fecha_ENG_ESP($movimiento['fecha_mov']),
                        2 => $txtRecibido,
                        3 => $movimiento['motivotransfer_mov'],
                        4 => $almcenDestino,
                        5 => $numberGuia,
                        6 => $estdTra,
                        7 => $btnGuia
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

    public function lst_cortes_bk_Inventario_xAlmacen_JSON(){
        try {
            $obj_fn = new FuncionesModel();
            $datesBus[0] = (int)$_GET['idalm'];
            $datesBus[1] = (int)$_GET['periodo'];
            $datesBus[2] = (int)$_GET['mes'];

            $obj_alm = new InventarioModel();
            $lstCortes = $obj_alm->listar_cortes_Inventario_backup_xAlmacen($datesBus);

            $datos = array();
            if(is_array($lstCortes)){
                foreach($lstCortes as $corte){
                    $formatFecha = explode("-",$corte['fecha_bai']);
                    $semana = date('W',  mktime(0,0,0,(int)$corte['mes_bai'],(int)$formatFecha[2],(int)$corte['anio_bai']));

                    $row = array(
                        'id' => $corte['id_bai'],
                        'texto' => "S".$semana." ".$obj_fn->fecha_ENG_ESP($corte['fecha_bai'])
                    );
                    array_push($datos, $row);
                }
            }

            echo json_encode($datos);

        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function loadCampos_viewDetalle_Item_Ajax(){
        try {
            $idInvent = (int)$_GET['id'];
            $obj_inv = new InventarioModel();
            $dtlleItem = $obj_inv->detalle_Item_xID($idInvent);
            $cantSplit = explode(".",$dtlleItem['cant_inv']);
            $stock = number_format($dtlleItem['cant_inv'], 2);
            if($cantSplit[1] == "00"){
                $stock = (int)$dtlleItem['cant_inv'];
            }
            $obj_fn = new FuncionesModel();?>
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-body">
                        <h6 class="font-weight-bold fz-16">Datos Generales</h6>
                        <hr class="mt-0 pt-0 mb-5">
                        <?php
                        if(!empty(trim($dtlleItem['und_inv']))){?>
                            <div class="row">
                                <label class="col-lg-4 col-md-4 col-sm-12 col-xs-12 col-form-label text-lg-right text-md-right text-left fz-11">
                                    Unidad/Equipo
                                </label>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                    <input type="text" class="form-control input-md text-left border-0-input fz-12 text-danger-800"
                                           placeholder="unidad" value="<?=$dtlleItem['und_inv']?>" disabled>
                                </div>
                            </div>
                            <?php
                        }
                        if(!empty(trim($dtlleItem['cod_inv']))){?>
                            <div class="row">
                                <label class="col-lg-4 col-md-4 col-sm-12 col-xs-12 col-form-label text-lg-right text-md-right text-left fz-11">
                                    Código
                                </label>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                    <input type="text" class="form-control input-md text-left border-0-input fz-12 text-danger-800"
                                           placeholder="código" value="<?=$dtlleItem['cod_inv']?>" disabled>
                                </div>
                            </div>
                            <?php
                        }
                        if( (float)$stock > 0){?>
                            <div class="row">
                                <label class="col-lg-4 col-md-4 col-sm-12 col-xs-12 col-form-label text-lg-right text-md-right text-left fz-11">
                                    Stock
                                </label>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                    <input type="text" class="form-control input-md text-left border-0-input fz-12 text-danger-800"
                                           value="<?=$stock?>" disabled>
                                </div>
                            </div>
                            <?php
                        }
                        if(!empty(trim($dtlleItem['um_inv']))){?>
                            <div class="row">
                                <label class="col-lg-4 col-md-4 col-sm-12 col-xs-12 col-form-label text-lg-right text-md-right text-left fz-11">
                                    Unidad medida
                                </label>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                    <input type="text" class="form-control input-md text-left border-0-input fz-12 text-danger-800"
                                           placeholder="unidad medida" value="<?=$dtlleItem['um_inv']?>" disabled>
                                </div>
                            </div>
                            <?php
                        }
                        if(!empty(trim($dtlleItem['des_inv']))){?>
                            <div class="row">
                                <label class="col-lg-4 col-md-4 col-sm-12 col-xs-12 col-form-label text-lg-right text-md-right text-left fz-11">
                                    Descripción
                                </label>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                    <input type="text" class="form-control input-md text-left border-0-input fz-12 text-danger-800"
                                           placeholder="descripción ítem" value="<?=$dtlleItem['des_inv']?>" disabled>
                                </div>
                            </div>
                            <?php
                        }
                        if(!empty(trim($dtlleItem['ubic_inv']))){?>
                            <div class="row">
                                <label class="col-lg-4 col-md-4 col-sm-12 col-xs-12 col-form-label text-lg-right text-md-right text-left fz-11">
                                    Ubicación
                                </label>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                    <input type="text" class="form-control input-md text-left border-0-input fz-12 text-danger-800"
                                           placeholder="ubicación" value="<?=$dtlleItem['ubic_inv']?>" disabled>
                                </div>
                            </div>
                            <?php
                        }
                        if(!empty(trim($dtlleItem['nroparte_inv']))){?>
                            <div class="row">
                                <label class="col-lg-4 col-md-4 col-sm-12 col-xs-12 col-form-label text-lg-right text-md-right text-left fz-11">
                                    Número de parte
                                </label>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                    <input type="text" class="form-control input-md text-left border-0-input fz-12 text-danger-800"
                                           placeholder="nro. parte" value="<?=$dtlleItem['nroparte_inv']?>" disabled>
                                </div>
                            </div>
                            <?php
                        }
                        if(!empty(trim($dtlleItem['reserva_inv']))){?>
                            <div class="row">
                                <label class="col-lg-4 col-md-4 col-sm-12 col-xs-12 col-form-label text-lg-right text-md-right text-left fz-11">
                                    Reserva/Cesta
                                </label>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                    <input type="text" class="form-control input-md text-left border-0-input fz-12 text-danger-800"
                                           placeholder="reserva" value="<?=$dtlleItem['reserva_inv']?>" disabled>
                                </div>
                            </div>
                            <?php
                        }
                        if(!empty(trim($dtlleItem['om_inv']))){?>
                            <div class="row">
                                <label class="col-lg-4 col-md-4 col-sm-12 col-xs-12 col-form-label text-lg-right text-md-right text-left fz-11">
                                    Orden Mantto.
                                </label>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                    <input type="text" class="form-control input-md text-left border-0-input fz-12 text-danger-800"
                                           placeholder="o. mantto." value="<?=$dtlleItem['om_inv']?>" disabled>
                                </div>
                            </div>
                            <?php
                        }
                        if(!empty(trim($dtlleItem['fechapedido_inv'])) && trim($dtlleItem['fechapedido_inv']) != "0000-00-00"){?>
                            <div class="row">
                                <label class="col-lg-4 col-md-4 col-sm-12 col-xs-12 col-form-label text-lg-right text-md-right text-left fz-11">
                                    Fecha pedido
                                </label>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                    <input type="text" class="form-control input-md text-left border-0-input fz-12 text-danger-800"
                                           placeholder="fecha pedido" value="<?=$obj_fn->fecha_ENG_ESP($dtlleItem['fechapedido_inv'])?>" disabled>
                                </div>
                            </div>
                            <?php
                        }
                        if(!empty(trim($dtlleItem['fecharec_inv'])) && trim($dtlleItem['fecharec_inv']) != "0000-00-00"){?>
                            <div class="row">
                                <label class="col-lg-4 col-md-4 col-sm-12 col-xs-12 col-form-label text-lg-right text-md-right text-left fz-11">
                                    Fecha recepción
                                </label>
                                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                    <input type="text" class="form-control input-md text-left border-0-input fz-12 text-danger-800"
                                           placeholder="fecha rec." value="<?=$obj_fn->fecha_ENG_ESP($dtlleItem['fecharec_inv'])?>" disabled>
                                </div>
                            </div>
                            <?php
                        }
                        if( !empty(trim($dtlleItem['marca_inv'])) || (!empty(trim($dtlleItem['cunit_inv'])) && trim($dtlleItem['cunit_inv']) != '0.000') ||
                            (!empty(trim($dtlleItem['total_inv'])) && trim($dtlleItem['total_inv']) != '0.000') || (!empty(trim($dtlleItem['fechains_inv'])) && trim($dtlleItem['fechains_inv']) != "0000-00-00") ||
                            !empty(trim($dtlleItem['mecanico_inv'])) || !empty(trim($dtlleItem['observ_inv']))
                        ){?>
                            <br>
                            <h6 class="font-weight-bold fz-16">Datos Complementarios</h6>
                            <hr class="mt-0 pt-0">
                            <?php
                            if(!empty(trim($dtlleItem['marca_inv']))){?>
                                <div class="row">
                                    <label class="col-lg-4 col-md-4 col-sm-12 col-xs-12 col-form-label text-lg-right text-md-right text-left fz-11">
                                        Marca
                                    </label>
                                    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                        <input type="text" class="form-control input-md text-left border-0-input fz-12 text-danger-800"
                                               placeholder="marca" value="<?=$dtlleItem['marca_inv']?>" disabled>
                                    </div>
                                </div>
                                <?php
                            }
                            if(!empty(trim($dtlleItem['cunit_inv'])) && trim($dtlleItem['cunit_inv']) != '0.000'){?>
                                <div class="row">
                                    <label class="col-lg-4 col-md-4 col-sm-12 col-xs-12 col-form-label text-lg-right text-md-right text-left fz-11">
                                        Costo Unitario
                                    </label>
                                    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                        <input type="text" class="form-control input-md text-left border-0-input fz-12 text-danger-800"
                                               placeholder="c. unit." value="<?=$dtlleItem['cunit_inv']?>" disabled>
                                    </div>
                                </div>
                                <?php
                            }

                            if(!empty(trim($dtlleItem['total_inv'])) && trim($dtlleItem['total_inv']) != '0.000'){?>
                                <div class="row">
                                    <label class="col-lg-4 col-md-4 col-sm-12 col-xs-12 col-form-label text-lg-right text-md-right text-left fz-11">
                                        Total
                                    </label>
                                    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                        <input type="text" class="form-control input-md text-left border-0-input fz-12 text-danger-800"
                                               placeholder="total" value="<?=$dtlleItem['total_inv']?>" disabled>
                                    </div>
                                </div>
                                <?php
                            }

                            if(!empty(trim($dtlleItem['fechains_inv'])) && trim($dtlleItem['fechains_inv']) != "0000-00-00"){?>
                                <div class="row">
                                    <label class="col-lg-4 col-md-4 col-sm-12 col-xs-12 col-form-label text-lg-right text-md-right text-left fz-11">
                                        Fecha instalación
                                    </label>
                                    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                        <input type="text" class="form-control input-md text-left border-0-input fz-12 text-danger-800"
                                               placeholder="fecha ins" value="<?=$obj_fn->fecha_ENG_ESP($dtlleItem['fechains_inv'])?>" disabled>
                                    </div>
                                </div>
                                <?php
                            }

                            if(!empty(trim($dtlleItem['mecanico_inv']))){?>
                                <div class="row">
                                    <label class="col-lg-4 col-md-4 col-sm-12 col-xs-12 col-form-label text-lg-right text-md-right text-left fz-11">
                                        Mec.
                                    </label>
                                    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                        <input type="text" class="form-control input-md text-left border-0-input fz-12 text-danger-800"
                                               placeholder="personal" value="<?=$dtlleItem['mecanico_inv']?>" disabled>
                                    </div>
                                </div>
                                <?php
                            }

                            if(!empty(trim($dtlleItem['observ_inv']))){?>
                                <div class="row">
                                    <label class="col-lg-4 col-md-4 col-sm-12 col-xs-12 col-form-label text-lg-right text-md-right text-left fz-11">
                                        Observaciones.
                                    </label>
                                    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                                           <textarea class="form-control input-md text-left border-0-input fz-12 text-danger-800" rows="4" cols="1"
                                                     placeholder="obervaciones" disabled><?=$dtlleItem['observ_inv']?></textarea>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                            <?php
                        }
                        ?>
                    </div>
                    <hr class="no-margin">
                    <div class="modal-footer pt-6 pb-5 align-center">
                        <button type="button" class="btn bg-brown-700 btn-hover-transform btn-sm" data-dismiss="modal">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
            <?php
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function load_Transito_Tranferencia(){
        try {
            $obj_fn = new FuncionesModel();
            $tipoTransito = trim($_GET['tipoTransito']);
            $IdAlmacenDE = (int)$_GET['IdAlmacenDE'];
            $obj_inv = new InventarioModel();
            if($tipoTransito == "IN"){
                $lstTransito = $obj_inv->lista_Transitos_Ingresos_Activos_xAlmacen($IdAlmacenDE);?>
                <div class="container container-base">
                    <?php
                    if(!is_null($lstTransito)){
                        $obj_alm = new AlmacenModel();
                        foreach ($lstTransito as $transito){
                            $dtlleAlmacenInicio = $obj_alm->detalle_Almacen_xID($transito['id_alm_ini']);
                            $almSalida = "";
                            $fechaSalida = "**/**/**** **:**";
                            if(!is_null($dtlleAlmacenInicio)){ $almSalida = trim($dtlleAlmacenInicio['titulo_alm']); }
                            if(trim($transito['fechareg_alm']) != "0000-00-00 00:00:00"){
                                $fechaSalida = $obj_fn->fechaHora_ENG_ESP($transito['fechareg_mov']);
                            }
                            $fechaLlegada = "**/**/****";
                            if(trim($transito['fechaguia_mov']) != "0000-00-00" and !is_null($transito['fechaguia_mov']) and (int)$transito['timellegada_mov']>0){
                                $fechaLlegada =$obj_fn->sumar_dias_fecha(trim($transito['fechaguia_mov']),(int)$transito['timellegada_mov']);
                            }
                            $dtlleAlmacenFin = $obj_alm->detalle_Almacen_xID($transito['id_alm_des']);
                            $almDestino = "";
                            if(!is_null($dtlleAlmacenFin)){ $almDestino = trim($dtlleAlmacenFin['titulo_alm']); }
                            ?>
                            <div class="card card-shadow border-2-dashed pb-20 border-radius-10 card-time mt-20" id="cardDetailOrder<?=$transito['id_movt']?>">
                                <a class="float-right mr-10 mt-10 cursor-pointer text-success-800 btn-hover-transform"
                                   title="Ingresar orden" id="btnReciverOrder" data-id="<?=$transito['id_movt']?>">
                                    <i class="icon-check fa-3x text-hover-success"></i>
                                </a>
                                <div class="row d-flex justify-content-between px-3 top">
                                    <div class="d-flex">
                                        <h5>
                                            ORDEN:
                                            <span class="text-primary font-weight-bold">
                                            <a class="collapsed" data-toggle="collapse" href="#collapseDetailOrder<?=$transito['id_movt']?>"
                                               aria-expanded="false" aria-controls="collapseDetailOrder<?=$transito['id_movt']?>"
                                               data-id="<?=$transito['id_movt']?>" id="btnDetailOrder" title="Detalle orden transito" data-open="0">
                                                <u class="text-blue-800 fz-25"><?=$transito['nro_mov']?></u>
                                            </a>
                                        </span>
                                            <p class="fz-10 mt-10 mb-0">
                                                Salida:
                                                <span class="font-weight-bold"><?=$almSalida?></span>
                                            </p>
                                            <p class="fz-10 mt-10">
                                                Fecha y hora:
                                                <span class="font-weight-bold"><?=$fechaSalida?></span>
                                            </p>
                                        </h5>
                                    </div>
                                    <div class="d-flex flex-column text-sm-right">
                                        <p class="mb-0">
                                            Llegada prevista: &nbsp;
                                            <span class="font-weight-bold">
                                            <?=$fechaLlegada?>
                                        </span>
                                        </p>
                                        <p>
                                            Destino: &nbsp;
                                            <span class="font-weight-bold"><?=$almDestino?></span>
                                        </p>
                                    </div>
                                </div>
                                <div class="row"><div class="col-12" id="mensaje_text_<?=$transito['id_movt']?>"></div></div>
                                <div class="row d-flex justify-content-center">
                                    <div class="col-12">
                                        <ul id="progressbar" class="text-center">
                                            <li class="active step0"></li>
                                            <li class="active step0"></li>
                                            <li class="active step0"></li>
                                            <li class="step0"></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="row justify-content-between top">
                                    <div class="row d-flex icon-content"> <img class="icon" src="../assets/img/tracker/9nnc9Et.png">
                                        <div class="d-flex flex-column">
                                            <p class="font-weight-bold">Orden<br>Procesada</p>
                                        </div>
                                    </div>
                                    <div class="row d-flex icon-content"> <img class="icon" src="../assets/img/tracker/u1AzR7w.png">
                                        <div class="d-flex flex-column">
                                            <p class="font-weight-bold">Pedido<br>Enviado</p>
                                        </div>
                                    </div>
                                    <div class="row d-flex icon-content"> <img class="icon" src="../assets/img/tracker/TkPm63y.png">
                                        <div class="d-flex flex-column">
                                            <p class="font-weight-bold">Pedido<br>en Camino</p>
                                        </div>
                                    </div>
                                    <div class="row d-flex icon-content"> <img class="icon" src="../assets/img/tracker/HdsziHP.png">
                                        <div class="d-flex flex-column">
                                            <p class="font-weight-bold">Pedido<br>Recibido</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="collapse" id="collapseDetailOrder<?=$transito['id_movt']?>"></div>
                            <?php
                        }
                    }
                    else{?>
                        <div class="alert alert-info text-center mt-20" role="alert">
                            <h1 class="alert-heading">Transito</h1>
                            <p>
                                No se encontraron registros generados por tranferencias que se encuentren en <strong>TRANSITO INGRESO</strong>,
                                si considera que esto no es correcto, contactese con el Administrador y valide los datos considerados.
                            </p>
                            <hr>
                            <p class="mb-0">soporte-imc@confipetrol.pe</p>
                        </div>
                        <?php
                    }
                    ?>
                    <br>
                    <br>
                    <br>
                </div>
                <?php
            }
            else  if($tipoTransito == "SAL"){
                $IdAlmacenA = (int)$_GET['IdAlmacenA'];
                $lstTransito = $obj_inv->lista_Transitos_Salida_Activos_xAlmacen($IdAlmacenDE,$IdAlmacenA);
                if(!is_null($lstTransito)){?>
                    <div class="card card-shadow mb-4">
                        <div class="card-header">
                            <div class="card-title">
                                Registros encontrados [<?=sizeof($lstTransito)?>]
                            </div>
                        </div>
                        <table class="table table-sm">
                            <thead>
                            <tr>
                                <th scope="col" class="text-center">#</th>
                                <th scope="col" class="text-center">NRO.TRANSF.</th>
                                <th scope="col" class="text-center">FECHA GUIA</th>
                                <th scope="col" class="text-center">NRO. GUIA</th>
                                <th scope="col" class="text-center">SOLICITADO POR</th>
                                <th scope="col" class="text-center">MOTIVO</th>
                                <th scope="col" class="text-center">FECHA EST.LLEGADA</th>
                                <th scope="col" class="text-center">FECHA REGISTRO</th>
                                <th scope="col" class="text-center">ESTADO</th>
                                <th scope="col" class="text-center"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $item = 1;
                            foreach ($lstTransito as $transito){
                                $fechaRegistro = "**/**/**** **:**";
                                if(trim($transito['fechaingreso_mov']) != "0000-00-00 00:00:00" and !is_null($transito['fechaingreso_mov']) and $transito['estd_transito'] == "I"){
                                    $fechaRegistro = $obj_fn->fechaHora_ENG_ESP($transito['fechaingreso_mov']);
                                }

                                $fechaLlegada = "**/**/****";
                                if(trim($transito['fechaguia_mov']) != "0000-00-00" and !is_null($transito['fechaguia_mov']) and (int)$transito['timellegada_mov']>0){
                                    $fechaLlegada =$obj_fn->sumar_dias_fecha(trim($transito['fechaguia_mov']),(int)$transito['timellegada_mov']);
                                }

                                $lblEstado = '<span class="badge badge-pill badge-success d-inline-block">INGRESADO</span>';
                                if($transito['estd_transito'] == "T"){
                                    $lblEstado = '<span class="badge badge-pill badge-warning d-inline-block">EN CAMINO</span>';
                                } ?>
                                <tr>
                                    <td class="text-center"><?=$item?></td>
                                    <td class="text-center">
                                        <a class="cursor-pointer" data-id="<?=$transito['id_movt']?>" title="+ detalle..." id="btnDetailSalida">
                                            <u><?=$transito['nro_mov']?></u>
                                        </a>
                                    </td>
                                    <td class="text-center"><?=$obj_fn->fecha_ENG_ESP($transito['fechaguia_mov'])?></td>
                                    <td class="text-center"><?=$transito['nroguia_mov']?></td>
                                    <td class="text-center"><?=mb_strtoupper($transito['solicitado_mov'],"UTF-8")?></td>
                                    <td class="text-center"><?=mb_strtoupper($transito['motivotransfer_mov'],"UTF-8")?></td>
                                    <td class="text-center"><?=$fechaLlegada?></td>
                                    <td class="text-center"><?=$fechaRegistro?></td>
                                    <td class="text-center"><?=$lblEstado?></td>
                                    <td class="text-center">
                                        <a class="cursor-pointer" title="anular" id="btnAnularTransito">
                                            <i class="icon-action-undo text-danger fz-30"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                                $item++;
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                    <br>
                    <br>
                    <br>
                    <?php
                }
                else{?>
                    <div class="alert alert-info text-center mt-20" role="alert">
                        <h1 class="alert-heading">Transito</h1>
                        <p>
                            No se encontraron registros generados por tranferencias que se encuentren en <strong>TRANSITO SALIDA</strong>,
                            si considera que esto no es correcto, contactese con el Administrador y valide los datos considerados.
                        </p>
                        <hr>
                        <p class="mb-0">soporte-imc@confipetrol.pe</p>
                    </div>
                    <?php
                }
            }

        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function loadCampos_Detalle_TranferirTransito(){
        try {
            $obj_inv = new InventarioModel();
            $IdTransito = (int)$_GET['IdTransito'];
            $dtlleTransito = $obj_inv->detalle_Transferencia_Transito_xID($IdTransito);

            $almacenDestino = "Sin valor registrado";
            if(!is_null($dtlleTransito['id_alm_des'])) {
                $obj_alm = new AlmacenModel();
                $dtlleAlmacenDestino = $obj_alm->detalle_Almacen_xID($dtlleTransito['id_alm_des']);
                $almacenDestino = $dtlleAlmacenDestino['titulo_alm'];
            }
            $obj_fn = new FuncionesModel();?>
            <div class="card card-shadow mb-20 border-radius-10">
                <div class="card-body row">
                    <div class="col-lg-6 col-md-6 mb-10">
                        <div class="card mb-4 border-1-dashed border-radius-10">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-3">
                                        <span class="bg-primary rounded-circle text-center wb-icon-box">
                                            <i class="icon-notebook text-light f24"></i>
                                        </span>
                                    </div>
                                    <div class="col-9">
                                        <h3 class="mt-1 mb-0"><?=$dtlleTransito['nro_mov']?></h3>
                                        <p class="f12 mb-0">Número Transacción</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 mb-10">
                        <div class="card mb-4 border-1-dashed border-radius-10">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-3">
                                            <span class="bg-warning rounded-circle text-center wb-icon-box">
                                                <i class="icon-calendar text-light f24"></i>
                                            </span>
                                    </div>
                                    <div class="col-9">
                                        <h3 class="mt-1 mb-0"><?=$dtlleTransito['fechareg_mov']?></h3>
                                        <p class="f12 mb-0">Fecha y hora registro</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-header bg-secondary-light-5">
                    <h4 class="card-title font-weight-bold">Datos Generales</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        Todos los campos descritos con <code class="font-weight-bold">(*)</code>, son campos obligatorios.
                    </p>
                    <div class="cd" id="contedAlmDestinity">
                        <div class="form-group row">
                            <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                Almacén Destino
                            </label>
                            <div class="col-xl-6 col-lg-6 col-md-8 col-sm-12">
                                <input type="text" class="form-control input-md text-left" autocomplete="off"
                                       placeholder="almacen destino" value="<?=$almacenDestino?>" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                            Atencion a
                        </label>
                        <div class="col-xl-6 col-lg-6 col-md-8 col-sm-12">
                            <input type="text" class="form-control input-md text-left" readonly
                                   placeholder="ingrese nombre del receptor" autocomplete="off"
                                   value="<?=$dtlleTransito['recibido_mov']?>">
                        </div>
                    </div>

                    <?php
                    if(!empty(trim($dtlleTransito['observ_mov']))){?>
                        <div class="form-group row">
                            <label for="" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                Observaciones
                            </label>
                            <div class="col-xl-7 col-lg-7 col-md-8 col-sm-12">
                                <textarea class="form-control" rows="5" cols="1"
                                          placeholder="obervaciones" readonly><?=$dtlleTransito['observ_mov']?></textarea>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
                <div class="card-header bg-secondary-light-5">
                    <h4 class="card-title font-weight-bold">Datos Transito</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        Los campos descritos a continuación que hacen referencia al transito de la transferencia.
                    </p>
                    <div class="form-group row">
                        <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                            Fecha Guía
                        </label>
                        <div class="col-xl-3 col-lg-3 col-md-4 col-sm-5">
                            <input type="text" class="form-control input-md text-left inputFecha" autocomplete="off" readonly
                                   placeholder="**/**/****" value="<?=$obj_fn->fecha_ENG_ESP($dtlleTransito['fechaguia_mov'])?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="nguia_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                            Número de Guía
                        </label>
                        <div class="col-xl-3 col-lg-3 col-md-4 col-sm-5">
                            <input type="text" class="form-control input-md text-left" autocomplete="off" readonly
                                   placeholder="Nro. guia" value="<?=$dtlleTransito['nroguia_mov']?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="nguia_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                            Tiempo llegada estimada
                        </label>
                        <div class="col-xl-3 col-lg-3 col-md-4 col-sm-5">
                            <input type="text" class="form-control input-md text-left inputFecha" autocomplete="off" readonly
                                   placeholder="**/**/****" value="<?=$obj_fn->sumar_dias_fecha($dtlleTransito['fechaguia_mov'],(int)$dtlleTransito['timellegada_mov'])?>">
                        </div>
                    </div>
                </div>

                <div class="card-header bg-secondary-light-5">
                    <h4 class="card-title font-weight-bold">Datos Items</h4>
                </div>
                <div class="table-responsive">
                    <table id="Tbl_DetalleItem" class="table mb-0 table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th width="30">#</th>
                                <th class="text-center">Código</th>
                                <th class="text-center">Descripción</th>
                                <th class="text-center">Nro.Parte/Serie</th>
                                <th class="text-center">Cantidad<br>Transito</th>
                                <th class="text-center" style="width: 50px">Cantidad<br>Recepción</th>
                                <th class="text-center" width="160">Entrada<br>Mercancias</th>
                                <th class="text-center"></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $lstActivos = $obj_inv->lista_Transitos_Detalle_xAlmacen($IdTransito);
                        if(!is_null($lstActivos)){
                            $i=1;
                            foreach ($lstActivos as $activos){?>
                                <tr>
                                    <td class="text-center align-middle">
                                        <?=$i?>
                                        <input type="hidden" value="0" name="estatusItm" id="valStatus<?=$activos['id_mtde']?>">
                                    </td>
                                    <td class="text-center align-middle"><?=$activos['cod_mde']."-".$activos['id_inv']?></td>
                                    <td class="text-center align-middle"><?=$activos['des_mde']?></td>
                                    <td class="text-center align-middle"><?=$activos['nparte_mde']?></td>
                                    <td class="text-center align-middle">
                                        <?=(int)$activos['cant_mde']?>
                                        <input type="hidden" value="<?=(int)$activos['cant_mde']?>" id="valTransito<?=$activos['id_mtde']?>">
                                    </td>

                                    <?php
                                    $disabled = "";
                                    $valRecepcion = (int)$activos['cant_mde'];
                                    $ActonBtn = 0;
                                    if(trim($activos['estado_mde']) == "C" || trim($activos['estado_mde']) == "R"){
                                        $disabled = " disabled ";
                                        $valRecepcion = (int)$activos['recepcion_mde'];
                                        $ActonBtn = 1;
                                    }
                                    ?>

                                    <td class="text-center align-middle">
                                        <input type="text" class="form-control text-center actionCantidad enabled<?=$activos['id_mtde']?>"
                                               value="<?=$valRecepcion?>" id="valRecepcion<?=$activos['id_mtde']?>" data-pos="<?=$activos['id_mtde']?>" <?=$disabled?>>
                                    </td>
                                    <td class="text-center align-middle">
                                        <select class="form-control enabled<?=$activos['id_mtde']?> selectEstados"
                                                data-id="<?=$activos['id_mtde']?>" id="idEstados<?=$activos['id_mtde']?>" <?=$disabled?>>
                                            <option value="">Seleccione...</option>
                                            <?php
                                            if(trim($activos['estado_mde']) == "C"){?>
                                                <option value="C" selected>Conforme</option>
                                                <option value="R">Rechazado</option>
                                                <?php
                                            }
                                            else if(trim($activos['estado_mde']) == "R"){?>
                                                <option value="C">Conforme</option>
                                                <option value="R" selected>Rechazado</option>
                                                <?php
                                            }
                                            else{?>
                                                <option value="C">Conforme</option>
                                                <option value="R">Rechazado</option>
                                                <?php

                                            }
                                            ?>
                                        </select>
                                    </td>

                                    <?php
                                    if($ActonBtn == 0){?>
                                        <td class="text-center align-middle">
                                           <a class="cursor-pointer action<?=$activos['id_mtde']?>" id="btnRecepcion" data-id="<?=$activos['id_mtde']?>" title="validar">
                                               <i class="icon-check fz-30 text-success-400"></i>
                                           </a>
                                        </td>
                                        <?php
                                    }
                                    ?>

                                </tr>
                                <?php
                                $i++;
                            }
                        }
                        else{?>
                            <tr>
                                <td colspan="8" class="text-center align-middle">No se encontraron ítems agregados.</td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>

                <div class="card-footer text-right bg-grey-light-5">
                    <a class="btn btn-primary" data-toggle="collapse" href="#collapseDetailOrder<?=$IdTransito?>"
                       role="button" aria-expanded="false" aria-controls="collapseDetailOrder<?=$IdTransito?>">
                        Cerrar
                    </a>
                </div>
            </div>
            <?php
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function loadCampos_Detalle_Salida_TranferirTransito(){
        try {
            $obj_inv = new InventarioModel();
            $IdTransito = (int)$_GET['IdTransito'];
            $dtlleTransito = $obj_inv->detalle_Transferencia_Transito_xID($IdTransito);

            $obj_fn = new FuncionesModel();

            $almacenDestino = "Sin valor registrado";
            if(!is_null($dtlleTransito['id_alm_des'])) {
                $obj_alm = new AlmacenModel();
                $dtlleAlmacenDestino = $obj_alm->detalle_Almacen_xID($dtlleTransito['id_alm_des']);
                $almacenDestino = $dtlleAlmacenDestino['titulo_alm'];
            }
            $obj_fn = new FuncionesModel();?>
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 mb-10">
                                <div class="card mb-4 border-1-dashed border-radius-10">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-3">
                                        <span class="bg-primary rounded-circle text-center wb-icon-box">
                                            <i class="icon-notebook text-light f24"></i>
                                        </span>
                                            </div>
                                            <div class="col-9">
                                                <h3 class="mt-1 mb-0"><?=$dtlleTransito['nro_mov']?></h3>
                                                <p class="f12 mb-0">Número Transacción</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 mb-10">
                                <div class="card mb-4 border-1-dashed border-radius-10">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-3">
                                            <span class="bg-warning rounded-circle text-center wb-icon-box">
                                                <i class="icon-calendar text-light f24"></i>
                                            </span>
                                            </div>
                                            <div class="col-9">
                                                <h4 class="mt-1 mb-0"><?=$obj_fn->fechaHora_ENG_ESP($dtlleTransito['fechareg_mov'])?></h4>
                                                <p class="f12 mb-0">Fecha y hora registro</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h4 class="card-title font-weight-bold">Datos Generales</h4>
                        <div class="cd">
                            <div class="form-group row">
                                <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Almacén Destino
                                </label>
                                <div class="col-xl-6 col-lg-6 col-md-8 col-sm-12">
                                    <input type="text" class="form-control input-md text-left" autocomplete="off"
                                           placeholder="almacen destino" value="<?=$almacenDestino?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                Atencion a
                            </label>
                            <div class="col-xl-6 col-lg-6 col-md-8 col-sm-12">
                                <input type="text" class="form-control input-md text-left" readonly
                                       placeholder="ingrese nombre del receptor" autocomplete="off"
                                       value="<?=$dtlleTransito['recibido_mov']?>">
                            </div>
                        </div>
                        <?php
                        if(!empty(trim($dtlleTransito['observ_mov']))){?>
                            <div class="form-group row">
                                <label for="" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Observaciones
                                </label>
                                <div class="col-xl-7 col-lg-7 col-md-8 col-sm-12">
                                <textarea class="form-control" rows="5" cols="1"
                                          placeholder="obervaciones" readonly><?=$dtlleTransito['observ_mov']?></textarea>
                                </div>
                            </div>
                            <?php
                        }
                        ?>

                        <h4 class="card-title font-weight-bold">Datos Transito</h4>
                        <p class="text-muted">
                            Los campos descritos a continuación que hacen referencia al transito de la transferencia.
                        </p>
                        <div class="form-group row">
                            <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                Fecha Guía
                            </label>
                            <div class="col-xl-3 col-lg-3 col-md-4 col-sm-5">
                                <input type="text" class="form-control input-md text-left inputFecha" autocomplete="off" readonly
                                       placeholder="**/**/****" value="<?=$obj_fn->fecha_ENG_ESP($dtlleTransito['fechaguia_mov'])?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nguia_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                Número de Guía
                            </label>
                            <div class="col-xl-3 col-lg-3 col-md-4 col-sm-5">
                                <input type="text" class="form-control input-md text-left" autocomplete="off" readonly
                                       placeholder="Nro. guia" value="<?=$dtlleTransito['nroguia_mov']?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nguia_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                Tiempo llegada estimada
                            </label>
                            <div class="col-xl-3 col-lg-3 col-md-4 col-sm-5">
                                <input type="text" class="form-control input-md text-left inputFecha" autocomplete="off" readonly
                                       placeholder="**/**/****" value="<?=$obj_fn->sumar_dias_fecha($dtlleTransito['fechaguia_mov'],(int)$dtlleTransito['timellegada_mov'])?>">
                            </div>
                        </div>

                        <h4 class="card-title font-weight-bold">Datos</h4>
                        <div class="table-responsive">
                            <table class="table mb-0 table-bordered table-striped table-sm">
                                <thead class="">
                                <tr>
                                    <th width="30">#</th>
                                    <th class="text-center">Código</th>
                                    <th class="text-center">Descripción</th>
                                    <th class="text-center">Nro.Parte/Serie</th>
                                    <th class="text-center" width="150">Cantidad</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $lstActivos = $obj_inv->lista_Transitos_Detalle_xAlmacen($IdTransito);
                                if(!is_null($lstActivos)){
                                    $i=1;
                                    foreach ($lstActivos as $activos){?>
                                        <tr>
                                            <td class="text-center"><?=$i?></td>
                                            <td class="text-center"><?=$activos['cod_mde']?></td>
                                            <td class="text-center"><?=$activos['des_mde']?></td>
                                            <td class="text-center"><?=$activos['nparte_mde']?></td>
                                            <td class="text-center"><?=(int)$activos['cant_mde']?></td>
                                        </tr>
                                        <?php
                                        $i++;
                                    }
                                }
                                else{?>
                                    <tr>
                                        <td colspan="5" class="text-center">No se encontraron ítems agregados.</td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <hr class="no-margin">
                    <div class="modal-footer pt-6 pb-5 text-right">
                        <button type="button" class="btn btn-primary" data-dismiss="modal" aria-label="Close">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
            <?php
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function insertar_Transito_Inventario_JSON(){
        try {
            date_default_timezone_set("America/Lima");
            setlocale(LC_TIME, 'es_PE.UTF-8');
            $obj_fn = new FuncionesModel();
            $idTransito = (int)$_POST['idtranval'];
            $nroGuiaRegistro = $_POST['number2']."-".$_POST['number5']."-".$_POST['number7'];

            $obj_inv = new InventarioModel();
            $dtlleMovTrans = $obj_inv->detalle_Transferencia_Transito_xID($idTransito);

            $idUSER = (int)$dtlleMovTrans['id_us'];
            $idAlm_destino = (int)$dtlleMovTrans['id_alm_des'];
            $idAlmacenInicio = (int)$dtlleMovTrans['id_alm_ini'];

            $datesTAB[0] = $dtlleMovTrans['action_mov']; //Accion
            $datesTAB[1] = (int)$dtlleMovTrans['id_alm_ini']; //Almacen inicio
            $datesTAB[2] = ""; //Unidad
            $datesTAB[3] = ""; //orden Mantto
            $datesTAB[4] = (int)$dtlleMovTrans['id_alm_des'];//Almacen destino
            $datesTAB[5] = ""; //No se completa para transito
            $datesTAB[6] = $dtlleMovTrans['recibido_mov']; //Atención A
            $datesTAB[7] = $dtlleMovTrans['autorizado_mov']; //Autorizado por
            $datesTAB[8] = $dtlleMovTrans['observ_mov'];//Observaciones
            $datesTAB[9] = ""; //Documento
            $datesTAB[10]= ""; //motivo
            $datesTAB[11]= $dtlleMovTrans['fecha_mov']; //Fecha
            $datesTAB[12]= $dtlleMovTrans['nro_mov']; //Nro Transac
            $datesTAB[13]= (int)$dtlleMovTrans['id_us']; //Id Usuario
            $datesTAB[14]= $dtlleMovTrans['fechareg_mov']; //Fecha sistema
            $datesTAB[15]= ""; //Entregado
            $datesTAB[16]= ""; //NRO VALE
            $datesTAB[17]= 0; //idMoV retiro
            $datesTAB[18]= ""; //Area Operativa
            $val = 0;
            $message = "Error al realizar el ingreso de la Transferencia.";
            $obj_mov = new MovimientoModel();
            $insertID = $obj_mov->registrar_Movimiento_Item_lastID($datesTAB);
            $respuesta = array();
            if((int)$insertID > 0){
                $val = 1;
                $message = "Ingreso de Transferencia realizada satisfactoriamente";
                $lstActivos = $obj_inv->lista_Transitos_Detalle_xAlmacen($idTransito);
                $acierto = 0;
                if(!is_null($lstActivos)){

                    foreach ($lstActivos as $activoDetalle){
                        //Si estado es CONFORME
                        if($activoDetalle['estado_mde'] == "C") {

                            $cantSolic = $activoDetalle['cant_mde'];
                            $diferencia = 0;
                            if((int)$activoDetalle['recepcion_mde'] < (int)$activoDetalle['transito_mde']){
                                $cantSolic = (int)$activoDetalle['recepcion_mde'];
                                $diferencia = (int)$activoDetalle['transito_mde'] - (int)$activoDetalle['recepcion_mde'];
                            }

                            if($diferencia > 0){
                                $dtlleInv_Reversa = $obj_inv->detalle_Item_xID($activoDetalle['id_inv']);
                                if(!is_null($dtlleInv_Reversa)){
                                    $dataUpReversa[0] = $activoDetalle['id_inv'];
                                    $dataUpReversa[1] = (float)$dtlleInv_Reversa['cant_inv'] + (float)$diferencia;
                                    $dataUpReversa[2] = 1; //Estado inventario 1 Activo
                                    $obj_inv->update_Inventario_Reversa($dataUpReversa);
                                }
                            }

                            //Definimos valores generales
                            $codMaterial = $activoDetalle['cod_mde'];
                            $idInvent = $activoDetalle['id_inv'];
                            $nroParte = $activoDetalle['nparte_mde'];
                            $codActivo = $activoDetalle['cactivo_mde'];

                            $obj_inv = new InventarioModel();
                            $dtlleItem = $obj_inv->detalle_Item_xID($idInvent);

                            //Definimos campos a registrar
                            $datesMOV[0] = $insertID; //idMov
                            $datesMOV[1] = $idInvent; //idInv
                            $datesMOV[2] = $codMaterial; //Codigo
                            $datesMOV[3] = $activoDetalle['des_mde']; //Descripcion
                            $datesMOV[4] = $nroParte;  //Nro.Parte
                            $datesMOV[5] = $cantSolic; //Cantidad
                            $datesMOV[6] = $activoDetalle['stock_mde']; //Stock
                            $datesMOV[7] = $activoDetalle['id_cla'];
                            $datesMOV[8] = $dtlleItem['um_inv'];
                            $datesMOV[9] = $dtlleItem['marca_inv'];
                            $datesMOV[10] = $codActivo;//C.Activo
                            $datesMOV[11] = $dtlleItem['cinventario_inv'];//C.Inventario
                            $datesMOV[12] = $dtlleItem['cmapel_inv'];//C.Mapel
                            $datesMOV[13] = $dtlleItem['conu_inv'];//C.ONU
                            $insertDetalle = $obj_mov->registrar_Movimiento_Item_Detalle($datesMOV);

                            if ($insertDetalle) {
                                //Buscamos si el item existe en el almacen de destino al cual se transferira.
                                $datesSearch[0] = $idAlm_destino;
                                $datesSearch[1] = trim($codMaterial);
                                $datesSearch[2] = trim($nroParte);
                                $datesSearch[3] = trim($codActivo);
                                //Código material, nro parte y codigo activo
                                $dtlleItemDes = $obj_inv->busca_existencia_Item_xDatos($datesSearch);

                                $actionInvent = false;
                                if (is_null($dtlleItemDes)) {
                                    $ordenCompraInv = "";
                                    if (!empty($dtlleItem['ordencompra_inv'])) {
                                        $ordenCompraInv = trim($dtlleItem['ordencompra_inv']);
                                    }

                                    $datesAlNew[0] = $idAlm_destino;
                                    $datesAlNew[1] = $codMaterial;
                                    $datesAlNew[2] = $cantSolic;
                                    $datesAlNew[3] = $obj_fn->quitar_caracteresEspeciales($dtlleItem['des_inv']);
                                    $datesAlNew[4] = $dtlleItem['um_inv'];
                                    $datesAlNew[5] = $dtlleItem['nroparte_inv'];
                                    $datesAlNew[6] = $dtlleItem['marca_inv'];
                                    $datesAlNew[7] = $dtlleItem['observ_inv'];
                                    $datesAlNew[8] = $idUSER;
                                    $datesAlNew[9] = date("Y-m-d H:i:s");
                                    $datesAlNew[10] = $ordenCompraInv;
                                    $datesAlNew[11] = $dtlleItem['id_cla'];
                                    $datesAlNew[12] = $dtlleItem['costo_act_inv'];
                                    $datesAlNew[13] = $dtlleItem['frec_depre_act_inv'];
                                    $datesAlNew[14] = $dtlleItem['val_depre_mensual_inv'];
                                    $datesAlNew[15] = $dtlleItem['cactivo_inv'];
                                    $datesAlNew[16] = $dtlleItem['cinventario_inv'];
                                    $datesAlNew[17] = $dtlleItem['cmapel_inv'];
                                    $datesAlNew[18] = $dtlleItem['conu_inv'];
                                    $datesAlNew[19] = $dtlleItem['fechaultcalibra_inv'];
                                    $datesAlNew[20] = $dtlleItem['freccalibra_inv'];
                                    $actionInvent = $obj_inv->registrar_Item_calibracion($datesAlNew);
                                } else {
                                    //Actualizamos el stock
                                    $valSumaStock_des = (int)$dtlleItemDes['cant_inv'] + (int)$cantSolic;
                                    $datesUStock_des[0] = $dtlleItemDes['id_inv'];
                                    $datesUStock_des[1] = (float)$valSumaStock_des;
                                    $actionInvent = $obj_inv->actualizar_Stock_Item($datesUStock_des);
                                }

                                if ($actionInvent) {
                                    $acierto++;
                                }

                                //Si se realiza el registro o actualización del stock en el almacen destino
                                //se actualizara el Stock del Item del almacén Inicial
                                /*
                                 * Ahora se resta el inventario al realizar la transferencia
                                 *
                                if($actionInvent){
                                    //Restamos la cantidad del item del almacen inicio
                                    $valRestaStock = (float)$dtlleItem['cant_inv'] - (float)$cantSolic;
                                    $datesUStockInitial[0] = $idInvent;
                                    $datesUStockInitial[1] = $valRestaStock;
                                    $obj_inv->actualizar_Stock_Item($datesUStockInitial);
                                }
                                */
                            }
                        }
                        //Si estado es RECHAZADO
                        else if($activoDetalle['estado_mde'] == "R"){
                            $dtlleInv_Reversa = $obj_inv->detalle_Item_xID($activoDetalle['id_inv']);
                            if(!is_null($dtlleInv_Reversa)){
                                $dataUpReversa[0] = $activoDetalle['id_inv'];
                                $dataUpReversa[1] = (float)$dtlleInv_Reversa['cant_inv'] + (float)$activoDetalle['cant_mde'];
                                $dataUpReversa[2] = 1; //Estado inventario 1 Activo
                                $updateInventario = $obj_inv->update_Inventario_Reversa($dataUpReversa);
                                if($updateInventario) { $acierto++; }
                            }
                        }
                    }
                }

                //validamos que se cumplio el registro de cada item
                /**/
                if($acierto == sizeof($lstActivos) || $acierto > 0){
                    //Actualizamos el estado del transito
                    $datesTransferTrans[0] = $idTransito;
                    $datesTransferTrans[1] = "I";
                    $datesTransferTrans[2] = date("Y-m-d H:i:s");
                    $datesTransferTrans[3] = $nroGuiaRegistro;
                    $obj_invt = new InventarioModel();
                    $obj_invt->actualizar_Transferencia_Transito_Estado_xID($datesTransferTrans);
                }
            }
            echo json_encode(array('status'=>$val, 'message'=>$message,'respuesta'=>$respuesta));

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lst_Calibracion_xIdAlmacen_JSON(){
        try {
            $IdAlmacen = (int)$_GET['IdAlmacen'];
            $valSemaforo = trim($_GET['colorSemaforo']);
            $obj_fn = new FuncionesModel();
            $idUsuario = $obj_fn->encrypt_decrypt('decrypt',$_GET['idustk']);
            $accCalibra = 0;
            $obj_us = new UsuarioModel();
            $dtlleUsuario = $obj_us->detalle_Usuario_xID($idUsuario);
            if (!is_null($dtlleUsuario)){
                $obj_pf = new PerfilModel();
                $dtllePerfil = $obj_pf->detalle_Perfil_xID($dtlleUsuario['id_perfil']);
                if(!is_null($dtllePerfil)){
                    $accCalibra = (int)$dtllePerfil['calibra_perfil'];
                }
            }

            $obj_inv = new InventarioModel();
            $lstItemCalibrar = $obj_inv->lista_inventarios_xCalibrar_xIdAlmacen($IdAlmacen);

            $datos = array();
            if(!is_null($lstItemCalibrar)){
                $i = 1;
                $fechaActual = date("Y-m-d");
                $obj_mov = new MovimientoModel();
                foreach($lstItemCalibrar as $calibra){
                    $statusUltMov = "ALMACÉN";
                    $markIni = "";
                    $markFin = "";
                    $action =  1;
                    //BUSCAMOS SI GENERO EL MOV DE RETIRO O DEVOLUCIÓN
                    $dtlleMovRecibido = $obj_mov->lista_Inventario_status_Calibracion_xMovimientos($IdAlmacen,$calibra['id_inv']);
                    if(!is_null($dtlleMovRecibido)){
                        if($dtlleMovRecibido['action_mov'] == "PT" || $dtlleMovRecibido['action_mov'] == "SO"){
                            $statusUltMov = "RETIRO";
                        }
                        else if($dtlleMovRecibido['action_mov'] == "DC" || $dtlleMovRecibido['action_mov'] == "DV"){
                            $statusUltMov = "DEVOLUCIÓN";
                        }
                    }
                    else{//BUSCAMOS SI ESTA EN TRANSITO
                        $dtlleMovTransito = $obj_mov->lista_Inventario_status_Calibracion_xMovimientosTRANSITO($IdAlmacen,$calibra['id_inv']);
                        if(!is_null($dtlleMovTransito)) {
                            if ($dtlleMovTransito['action_mov'] == "TRA") { $statusUltMov = "TRANSITO"; }
                            $markIni = "<del>";
                            $markFin = "</del>";
                        }
                        else{//BUSCAMOS SI GENERO EL MOV DE TRANSFERENCIA
                            $dtlleMovRecibido = $obj_mov->lista_Inventario_status_Calibracion_xMovimientosTRANSFER($IdAlmacen,$calibra['id_inv']);
                            if(!is_null($dtlleMovRecibido)) {
                                if ($dtlleMovRecibido['action_mov'] == "TRA") {
                                    $action = 0;
                                }
                            }
                        }
                    }

                    if($action == 1) {
                        $btnCalibra = "";
                        $btnHistorial = "";
                        $fechProxCalibra = "**/**/****";
                        $frecCalibra = "";
                        $fechaultcalibra = "**/**/****";
                        $semaforo = "";
                        $diferenciaDias = 0;
                        $actSemaforo = "";
                        if (trim($calibra['fechaultcalibra_inv']) != '0000-00-00') {
                            $proxCalibracion = $obj_fn->sumar_meses_fecha($calibra['fechaultcalibra_inv'], (int)$calibra['freccalibra_inv']);
                            $fechProxCalibra = $obj_fn->fecha_ENG_ESP($proxCalibracion);

                            $frecCalibra = $calibra['freccalibra_inv'];

                            $fechaultcalibra = $obj_fn->fecha_ENG_ESP($calibra['fechaultcalibra_inv']);

                            $diferenciaDias = $obj_fn->difDias($proxCalibracion, $fechaActual);

                            if (strtotime($proxCalibracion) > strtotime($fechaActual) && $diferenciaDias > 60) {
                                $semaforo = '<i class="fa fa-circle text-success-600 pulse fz-18" aria-hidden="true"></i>';
                                $actSemaforo = "verde";
                            } else if (strtotime($proxCalibracion) > strtotime($fechaActual) && $diferenciaDias <= 60) {

                                if ($diferenciaDias > 0 && $diferenciaDias <= 30) {
                                    $semaforo = '<i class="fa fa-circle text-danger-800 pulse fz-18" aria-hidden="true"></i>';
                                    $diferenciaDias = 0;
                                    $actSemaforo = "rojo";
                                } else if ($diferenciaDias >= 31 && $diferenciaDias <= 60) {
                                    $semaforo = '<i class="fa fa-circle text-orange pulse fz-18" aria-hidden="true"></i>';
                                    $actSemaforo = "ambar";
                                }
                            } else {
                                $semaforo = '<i class="fa fa-circle text-danger-800 pulse fz-18" aria-hidden="true"></i>';
                                $diferenciaDias = 0;
                                $actSemaforo = "rojo";
                            }

                            if ($accCalibra == 1) {
                                $btnCalibra = '<a class="cursor-pointer text-hover-primary btn-hover-transform btn-lg" title="Nueva fecha calibración" id="updateCalibra" data-idinv="' . $calibra['id_inv'] . '" data-idalm="' . $IdAlmacen . '" data-fuc="' . $obj_fn->fecha_ENG_ESP_format($calibra['fechaultcalibra_inv'], ".") . '"><i class="icon-calendar"></i></a>';
                            }

                            $lstCalibraciones = $obj_inv->lista_Calibraciones_xIdInventario($calibra['id_inv']);

                            if (!is_null($lstCalibraciones)) {
                                $btnHistorial = '<a class="cursor-pointer text-hover-warning btn-hover-transform btn-lg" title="Historial" id="historyCalibra" data-idinv="' . $calibra['id_inv'] . '" data-idalm="' . $IdAlmacen . '"><i class="ti-pulse"></i></a>';
                            }
                        }

                        $row = array(
                            0 => $i,
                            1 => $markIni . $calibra['cod_inv'] . $markFin,
                            2 => $markIni . $calibra['des_inv'] . $markFin,
                            3 => $markIni . $calibra['nroparte_inv'] . $markFin,
                            4 => $markIni . $fechaultcalibra . $markFin,
                            5 => $markIni . $frecCalibra . $markFin,
                            6 => $markIni . $fechProxCalibra . $markFin,
                            7 => $markIni . $diferenciaDias . $markFin,
                            8 => $statusUltMov,
                            9 => $semaforo,
                            10 => $btnHistorial . $btnCalibra,
                            11 => $actSemaforo
                        );
                        array_push($datos, $row);
                        $i++;
                    }
                }
            }

            $tabla = array('data' => array());
            if($valSemaforo == "") {
                $tabla = array('data' => $datos);
            }
            else if($valSemaforo == "r") {
                $dataRojo = array();
                $cont = 1;
                for($i=0; $i<sizeof($datos); $i++){
                    if($datos[$i][11] == "rojo"){
                        $rowL = array(
                            0 => $cont,
                            1 => $datos[$i][1],
                            2 => $datos[$i][2],
                            3 => $datos[$i][3],
                            4 => $datos[$i][4],
                            5 => $datos[$i][5],
                            6 => $datos[$i][6],
                            7 => $datos[$i][7],
                            8 => $datos[$i][8],
                            9 => $datos[$i][9],
                            10=> $datos[$i][10],
                            11=> $datos[$i][11]
                        );
                        array_push($dataRojo, $rowL);
                        $cont++;
                    }
                }
                $tabla = array('data' => $dataRojo);
            }
            else if($valSemaforo == "a") {
                $dataAmbar = array();
                $cont = 1;
                for($i=0; $i<sizeof($datos); $i++){
                    if($datos[$i][11] == "ambar"){
                        $rowL = array(
                            0 => $cont,
                            1 => $datos[$i][1],
                            2 => $datos[$i][2],
                            3 => $datos[$i][3],
                            4 => $datos[$i][4],
                            5 => $datos[$i][5],
                            6 => $datos[$i][6],
                            7 => $datos[$i][7],
                            8 => $datos[$i][8],
                            9 => $datos[$i][9],
                            10=> $datos[$i][10],
                            11=> $datos[$i][11]
                        );
                        array_push($dataAmbar, $rowL);
                        $cont++;
                    }
                }
                $tabla = array('data' => $dataAmbar);
            }
            else if($valSemaforo == "v") {
                $dataVerde = array();
                $cont = 1;
                for($i=0; $i<sizeof($datos); $i++){
                    if($datos[$i][11] == "verde"){
                        $rowL = array(
                            0 => $cont,
                            1 => $datos[$i][1],
                            2 => $datos[$i][2],
                            3 => $datos[$i][3],
                            4 => $datos[$i][4],
                            5 => $datos[$i][5],
                            6 => $datos[$i][6],
                            7 => $datos[$i][7],
                            8 => $datos[$i][8],
                            9 => $datos[$i][9],
                            10=> $datos[$i][10],
                            11=> $datos[$i][11]
                        );
                        array_push($dataVerde, $rowL);
                        $cont++;
                    }
                }
                $tabla = array('data' => $dataVerde);
            }

            echo json_encode($tabla);
            unset($datos);

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function loadCampos_renew_calibracion(){
        try {
            $IdIventario = $_GET['IdInventario'];
            $IdAlmacen = $_GET['IdAlmacen'];
            $obj_fn = new FuncionesModel();
            $IdUsuario = $_GET['idustk'];
            $obj_inv = new InventarioModel();
            $dtlleInventario = $obj_inv->detalle_Item_xID($IdIventario);?>
            <form id="formCalibrationItem" role="form" method="post" enctype="multipart/form-data">
                <input type="hidden" id="idinv_c" value="<?=$IdIventario?>">
                <input type="hidden" id="idalm_c" value="<?=$IdAlmacen?>">
                <input type="hidden" id="idustk_c" name="idustk_c" value="<?=$IdUsuario?>">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title text-center">
                                <h2 class="mb-0 text-brown-800 font-weight-bold">
                                    Calibracion : <code class="text-primary-800"><?=$dtlleInventario['des_inv']?></code>
                                </h2>
                                <p class="breadcrumb-item text-muted">Realice la actualización de la calibración del Activo/Instrumento.</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 mb-10">
                            <div class="card mb-4 card-shadow">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-3">
                                            <span class="bg-primary rounded-circle text-center wb-icon-box">
                                                <i class="icon-notebook text-light f24"></i>
                                            </span>
                                        </div>
                                        <div class="col-9">
                                            <h3 class="mt-1 mb-0">CALI-<?=$dtlleInventario['cod_inv']."-".rand(1,200)?></h3>
                                            <p class="f12 mb-0">Número Transacción</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 mb-10">
                            <div class="card mb-4 card-shadow">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-3">
                                            <span class="bg-warning rounded-circle text-center wb-icon-box">
                                                <i class="icon-calendar text-light f24"></i>
                                            </span>
                                        </div>
                                        <div class="col-9">
                                            <h3 class="mt-1 mb-0"><?=date("d/m/Y H:s")?></h3>
                                            <p class="f12 mb-0">Fecha y hora registro</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card card-shadow">
                        <div class="card-header bg-secondary-light-5">
                            <h4 class="card-title font-weight-bold">Datos Generales</h4>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">
                                Todos los campos descritos con <code class="font-weight-bold">(*)</code>, son campos obligatorios.
                            </p>
                            <div class="row">
                                <div class="col-12" id="mensajes_actions"></div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Fecha ultima calibración
                                </label>
                                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-12">
                                    <input type="text" class="form-control input-md text-left" readonly
                                           value="<?=$obj_fn->fecha_ENG_ESP($dtlleInventario['fechaultcalibra_inv'])?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="fecha_cal" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Fecha calibración actual
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-12">
                                    <input type="text" class="form-control input-md text-left inputFecha" required
                                           name="fecha_cal" id="fecha_cal" maxlength="10" placeholder="**/**/****" autocomplete="off">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="filedata_cal" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Certificado calibración
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-xl-6 col-lg-6 col-md-8 col-sm-12">
                                    <input type="file" class="file" id="filedata_cal" name="filedata_cal" required
                                           data-show-preview="false" data-show-upload="false"
                                           data-show-caption="true" data-show-remove="true"
                                           data-show-cancel="false"
                                           data-browse-Label="Examinar"
                                           data-remove-Label="Eliminar"
                                           data-upload-Label="Visualizar"
                                           data-browse-class="btn waves-effect waves-light btn-outline-secondary cursor-pointer"
                                           data-upload-class="btn waves-effect waves-light btn-outline-info cursor-pointer"
                                           data-remove-class="btn waves-effect waves-light btn-outline-danger cursor-pointer">
                                    <span class="help-block">
                                        <small>Formato permitido: [<code>PDF</code>].</small>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 text-center">
                        <button type="button" id="btnCancel" class="btn btn-light mr-20 btn-lg" data-id="<?=$IdAlmacen?>">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-info btn-hover-transform btn-lg">
                            Registrar nueva calibración
                        </button>
                    </div>
                </div>
            </form>
            <br><br><br>
            <?php
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function register_date_Calibracion_JSON(){
        try {
            date_default_timezone_set("America/Lima");
            setlocale(LC_TIME, 'es_PE.UTF-8');

            $fileText = "";
            if(!empty($_FILES["filedata_cal"]['tmp_name'])) {
                $filename = $_FILES["filedata_cal"]["name"];
                $source = $_FILES["filedata_cal"]["tmp_name"];
                $directorio = "../assets/certificate-calibration/";

                //Obtenemos extension de archivo
                $info = new SplFileInfo($filename);
                $extension = $info->getExtension();
                //ciframos nombre al archivo
                $var_rand = rand(10000, 999999) * rand(10000, 999999);
                $fileName_tem = md5($var_rand . $extension);
                $fileName_tem = $fileName_tem . "." . $extension;

                $dir = opendir($directorio); //Abrimos el directorio de destino
                $target_path = $directorio . '/' . $fileName_tem; //Indicamos la ruta de destino, así como el nombre del archivo
                if (move_uploaded_file($source, $target_path)) {
                    $fileText = $fileName_tem;
                }

                closedir($dir); //Cerramos el directorio de destino
            }
            $idInventario = (int)$_REQUEST['idInventario'];
            $idAlmacen = (int)$_REQUEST['idAlmacen'];
            $obj_alm = new AlmacenModel();
            $dtlleAlmacen = $obj_alm->detalle_Almacen_xID($idAlmacen);
            $nameAlmacen = "";
            if(!is_null($dtlleAlmacen)){ $nameAlmacen = $dtlleAlmacen['titulo_alm']; }

            $obj_fn = new FuncionesModel();
            $idUsuario = $obj_fn->encrypt_decrypt('decrypt',$_REQUEST['idUsuario']);
            $obj_us = new UsuarioModel();
            $dtlleUsuario = $obj_us->detalle_Usuario_xID($idUsuario);
            $textPersona = "";
            if(is_array($dtlleUsuario)){
                $obj_per = new PersonaModel();
                $dtllePersona = $obj_per->detalle_Persona_xID($dtlleUsuario['id_per']);
                if(is_array($dtllePersona)) {
                    $textPersona = $dtllePersona['ape_pa_per']." ".$dtllePersona['nombres_per'];
                }
            }

            $fechaNewCalibra = "0000-00-00";
            if(!empty($_REQUEST['fecha_cal'])){
                $fechaNewCalibra = $obj_fn->fecha_ESP_ENG($_REQUEST['fecha_cal']);
            }


            $obj_inv= new InventarioModel();
            $dtlleItem = $obj_inv->detalle_Item_xID($idInventario);
            $nroGuia = 0;
            if(!is_null($dtlleItem['nguia_inv'])){$nroGuia = $dtlleItem['nguia_inv'];}

            $dtlleClasif = $obj_inv->detalle_Clasificacion_xID($dtlleItem['id_cla']);

            //Definimos campos a registrar
            $datesREGC[0] = $idInventario;
            $datesREGC[1] = $dtlleItem['cod_inv'];
            $datesREGC[2] = $dtlleItem['des_inv'];
            $datesREGC[3] = $dtlleClasif['des_cla'];
            $datesREGC[4] = $nroGuia;
            $datesREGC[5] = $dtlleItem['fechaultcalibra_inv'];
            $datesREGC[6] = $fechaNewCalibra;
            $datesREGC[7] = date("Y-m-d h:i:s");
            $datesREGC[8] = $fileText;
            $datesREGC[9] = $idUsuario;
            $datesREGC[10]= $textPersona;
            $datesREGC[11]= $idAlmacen;
            $datesREGC[12]= $nameAlmacen;
            $insertCalibration = $obj_inv->registrar_newFecha_Calibracion($datesREGC);

            $val = 0;
            $message = "Error al realizar el registro de la nueva fecha de calibración";
            if($insertCalibration){
                $val = 1;
                $message = "Se procedio a actualizar la fecha de Calibración del Activo/Instrumento.";
                //Actualizamos nueva fecha de calibración en Inventario
                $datesActualCalibra[0] = $idInventario;
                $datesActualCalibra[1] = $fechaNewCalibra;
                $obj_invt = new InventarioModel();
                $obj_invt->actualizar_fechaCalibracion_Inventario_xID($datesActualCalibra);
            }

            echo json_encode(array('status'=>$val, 'message'=>$message));

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function load_Historial_Calibracion_xIdInventario(){
        try {
            $IdInventario = (int)$_GET['IdInventario'];
            $IdAlmacen = (int)$_GET['IdAlmacen'];
            $obj_fn = new FuncionesModel();

            $obj_inv = new InventarioModel();
            $dtlleItem = $obj_inv->detalle_Item_xID($IdInventario);
            $obj_alm = new AlmacenModel();
            $dtlleAlmacen = $obj_alm->detalle_Almacen_xID($IdAlmacen);

            $lstCalibraciones = $obj_inv->lista_Calibraciones_xIdInventario($IdInventario);

            if(!is_null($lstCalibraciones)){?>
                <div class="container" id="divHtml2pdf">
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title text-center">
                                <h2 class="mb-0 text-brown-800 font-weight-bold">
                                    <?=$dtlleAlmacen['titulo_alm']?>
                                    <br>
                                    <code class="text-primary-800"><?=$dtlleItem['des_inv']?></code>
                                </h2>
                            </div>
                        </div>
                    </div>
                    <div class="card card-shadow mb-4">
                        <div class="card-header">
                            <div class="card-title text-center fz-25">
                                <a class="float-left cursor-pointer" title="Retornar" id="btnCancel" data-id="<?=$IdAlmacen?>">
                                    <i class="icon-arrow-left"></i>
                                </a>
                                Historial calibración:

                                <a class="btn btn-danger float-right text-white"
                                   id="btnCrearPdf" data-cod="<?=$dtlleItem['cod_inv']?>">
                                    <i class="fa fa-file-pdf-o position-left"></i>
                                    Generar PDF
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="main-timeline4">
                                <?php
                                foreach ($lstCalibraciones as $item){
                                    $fecha = explode("-",$item['fecha_new_ca']);
                                    ?>
                                    <div class="timeline">
                                        <div class="timeline-content">
                                            <span class="year"><?=$fecha[0]?></span>
                                            <div class="inner-content">
                                                <h3 class="title">
                                                    <?=$fecha[2]." ".$obj_fn->texto_mes_abrev($fecha[1])." ".$fecha[0]?>
                                                </h3>
                                                <div class="description text-muted fz-18">
                                                    <p class="fz-12 mb-0">Registrado el:</p>
                                                    <i class="fa fa-clock-o"></i>
                                                    <span class="font-weight-bold">
                                                           <?=$obj_fn->fechaHora_ENG_ESP($item['fechareg_ca'])?>
                                                       </span>
                                                </div>

                                                <div class="description text-muted fz-18">
                                                    <p class="fz-12 mb-0">Registrado por:</p>
                                                    <span class="font-weight-bold fz-18"><?=$item['name_ca']?></span>
                                                </div>
                                                <div class="description fz-18">
                                                    <a class="text-muted text-danger-600 cursor-pointer" title="Visualizar" id="overviewCalibrate" data-file="<?=$item['file_ca']?>" data-des="<?=$item['des_ca']?>">
                                                        <i class="fa fa-file-pdf-o"></i>&nbsp;
                                                        certificado
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <br>
                <br>
                <br>
                <?php
            }
            else{?>
                <div class="container container-base">
                    <div class="alert alert-info text-center mt-20" role="alert">
                        <h1 class="alert-heading">Calibración</h1>
                        <p>
                            No se encontraron registros agregados de calibración Activo/Instrumentos,
                            si considera que esto no es correcto, contactese con el Administrador y valide los datos considerados.
                        </p>
                        <hr>
                        <p class="mb-0">soporte-imc@confipetrol.pe</p>
                    </div>
                    <br>
                    <br>
                    <br>
                </div>
                <?php
            }
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function load_view_Pdf_Modal(){
        try {
            $description = $_GET['description'];?>
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><?=$description?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body no-padding">
                        <div id="filePDFContend" class="pdfobject-container" style="height: 400px"></div>
                    </div>
                </div>
            </div>
            <?php
        } catch (PDOException $e) {
            throw  $e;
        }
    }

    public function loadCampos_bajaItem(){
        try {
            $idus = $_GET['idus'];
            $idinv = $_GET['idinv'];
            $obj_inv = new InventarioModel();
            $dtlleItem= $obj_inv->detalle_Item_xID($idinv);?>
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="page-title">
                            <h4 class="mb-0 text-warning-0">
                                BAJA Ítem
                            </h4>
                            <ol class="breadcrumb mb-0 pl-0 pt-1 pb-0">
                                <li class="breadcrumb-item text-muted">Complete los campos a continuación para proceder con la baja del ítem.</li>
                            </ol>
                        </div>
                    </div>
                </div>
                <form id="formBajaItem"  role="form" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="idusitm_tk" value="<?=$idus?>">
                    <input type="hidden" name="idinv_i" value="<?=$idinv?>">
                    <input type="hidden" name="idalm_i" value="<?=$dtlleItem['id_alm']?>">
                    <div class="card card-shadow">
                        <div class="card-body">
                            <p class="text-muted">
                                Todos los campos descritos con <code class="font-weight-bold">(*)</code>, son campos obligatorios.
                            </p>
                            <div class="row">
                                <div class="col-12" id="mensajes_actions_bja"></div>
                            </div>
                            <div class="form-group row">
                                <label for="textbaja_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Motivo de Baja
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-5">
                                    <textarea class="form-control" name="textbaja_itm" maxlength="500" required
                                              rows="4" cols="1" placeholder="indique el motivo de baja"></textarea>
                                    <small class="form-text text-muted">Máximo 500 carácteres</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="filedata_cal" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Documento sustento
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-xl-6 col-lg-6 col-md-8 col-sm-12">
                                    <input type="file" class="file" id="filedata_itm" name="filedata_itm" required
                                           data-show-preview="false" data-show-upload="false"
                                           data-show-caption="true" data-show-remove="true"
                                           data-show-cancel="false"
                                           data-browse-Label="Examinar"
                                           data-remove-Label="Eliminar"
                                           data-upload-Label="Visualizar"
                                           data-browse-class="btn waves-effect waves-light btn-outline-secondary cursor-pointer"
                                           data-upload-class="btn waves-effect waves-light btn-outline-info cursor-pointer"
                                           data-remove-class="btn waves-effect waves-light btn-outline-danger cursor-pointer">
                                    <span class="help-block">
                                        <small>Formato permitido: [<code>PDF</code>].</small>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <hr class="no-margin no-padding">
                        <div class="card-footer bg-grey-light-5">
                            <div class="row">
                                <div class="col-12 text-center">
                                    <button type="button" id="btnCancel" class="btn btn-light mr-20 btn-lg">
                                        Cancelar
                                    </button>
                                    <button type="submit" class="btn bg-indigo-600 btn-hover-transform btn-lg">
                                        Registrar Baja
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <br><br><br>
            </div>
            <?php
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function register_Baja_Inventario_JSON(){
        try {
            date_default_timezone_set("America/Lima");
            setlocale(LC_TIME, 'es_PE.UTF-8');

            $fileText = "";
            if(!empty($_FILES["filedata_itm"]['tmp_name'])) {
                $filename = $_FILES["filedata_itm"]["name"];
                $source = $_FILES["filedata_itm"]["tmp_name"];
                $directorio = "../assets/certificate-baja/";

                //Obtenemos extension de archivo
                $info = new SplFileInfo($filename);
                $extension = $info->getExtension();
                //ciframos nombre al archivo
                $var_rand = rand(10000, 999999) * rand(10000, 999999);
                $fileName_tem = md5($var_rand . $extension);
                $fileName_tem = $fileName_tem . "." . $extension;

                $dir = opendir($directorio); //Abrimos el directorio de destino
                $target_path = $directorio . '/' . $fileName_tem; //Indicamos la ruta de destino, así como el nombre del archivo
                if (move_uploaded_file($source, $target_path)) {
                    $fileText = $fileName_tem;
                }

                closedir($dir); //Cerramos el directorio de destino
            }
            $idInventario = (int)$_REQUEST['idinv_i'];
            $idAlmacen = (int)$_REQUEST['idalm_i'];
            $obj_alm = new AlmacenModel();
            $dtlleAlmacen = $obj_alm->detalle_Almacen_xID($idAlmacen);
            $nameAlmacen = "";
            if(!is_null($dtlleAlmacen)){ $nameAlmacen = $dtlleAlmacen['titulo_alm']; }

            $obj_fn = new FuncionesModel();
            $idUsuario = $obj_fn->encrypt_decrypt('decrypt',$_REQUEST['idusitm_tk']);
            $obj_us = new UsuarioModel();
            $dtlleUsuario = $obj_us->detalle_Usuario_xID($idUsuario);
            $textPersona = "";
            if(is_array($dtlleUsuario)){
                $obj_per = new PersonaModel();
                $dtllePersona = $obj_per->detalle_Persona_xID($dtlleUsuario['id_per']);
                if(is_array($dtllePersona)) {
                    $textPersona = $dtllePersona['ape_pa_per']." ".$dtllePersona['nombres_per'];
                }
            }

            $obj_inv= new InventarioModel();
            $dtlleItem = $obj_inv->detalle_Item_xID($idInventario);
            $textBaja = "";
            if(!empty(trim($_REQUEST['textbaja_itm']))){$textBaja = $_REQUEST['textbaja_itm'];}

            $dtlleClasif = $obj_inv->detalle_Clasificacion_xID($dtlleItem['id_cla']);

            //Definimos campos a registrar
            $datesREGC[0] = $idInventario;
            $datesREGC[1] = $dtlleItem['cod_inv'];
            $datesREGC[2] = $dtlleItem['des_inv'];
            $datesREGC[3] = $dtlleClasif['des_cla'];
            $datesREGC[4] = $textBaja;
            $datesREGC[5] = date("Y-m-d h:i:s");
            $datesREGC[6] = $fileText;
            $datesREGC[7] = $idUsuario;
            $datesREGC[8] = $textPersona;
            $datesREGC[9] = $idAlmacen;
            $datesREGC[10]= $nameAlmacen;
            $insertCalibration = $obj_inv->registrar_Inventario_Baja_xID($datesREGC);

            $val = 0;
            $message = "Error al realizar el registro de la BAJA del ITEM.";
            if($insertCalibration){
                $val = 1;
                $message = "Se procedio a realizar la BAJA del ITEM.";
                //Actualizamos nueva fecha de calibración en Inventario
                $datesState[0] = $idInventario;
                $datesState[1] = 2;
                $obj_invt = new InventarioModel();
                $obj_invt->actualizar_Estado_Inventario_xID($datesState);
            }

            echo json_encode(array('status'=>$val, 'message'=>$message));

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lst_Inventario_Bajas_xIdAlmacen(){
        try {
            $IdAlmacen = (int)$_GET['IdAlmacen'];

            $obj_inv = new InventarioModel();
            $lstItemBaja = $obj_inv->lista_Items_xBaja_xIdAlmacen($IdAlmacen);

            $obj_fn = new FuncionesModel();
            if(!is_null($lstItemBaja)){?>
                <div class="card card-shadow mb-4">
                    <div class="card-header">
                        <div class="card-title">
                            Listado de bajas:
                            <a class="btn btn-success cursor-pointer btn-hover-transform text-white float-right" href="../../app/reporte-bajas-Export.php?almacen=<?=$IdAlmacen?>">
                                <i class="fa fa-cloud-upload position-left"></i>
                                Descargar
                            </a>
                        </div>
                    </div>
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Codigo</th>
                            <th scope="col">Descripción</th>
                            <th scope="col">Tipo</th>
                            <th scope="col">Realizado Por.</th>
                            <th scope="col">Realizado a las.</th>
                            <th scope="col"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $i = 1;
                        foreach ($lstItemBaja as $bajas){?>
                            <tr>
                                <th scope="row" class="align-middle"><?=$i?></th>
                                <td class="text-left align-middle"><?=$bajas['cod_inb']?></td>
                                <td class="align-middle"><?=$bajas['des_inb']?></td>
                                <td class="text-left align-middle"><?=strtoupper($bajas['tipo_inb'])?></td>
                                <td class="text-left align-middle"><?=$bajas['persona_us']?></td>
                                <td class="text-left align-middle"><?=$obj_fn->fechaHora_ENG_ESP($bajas['fechareg_inb'])?></td>
                                <td class="text-center align-middle">
                                    <a class="cursor-pointer text-hover-danger btn-lg" title="visualizar" id="overviewBaja" data-file="<?=$bajas['file_inb']?>" data-des="Baja: <?=$bajas['cod_inb']?>">
                                        <i class="fa fa-file-pdf-o text-muted"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php
                            $i++;
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <?php
            }
            else{?>
                <div class="container container-base">
                    <div class="alert alert-info text-center mt-20" role="alert">
                        <h1 class="alert-heading">Bajas</h1>
                        <p>
                            No se encontraron registros agregados como bajas,
                            si considera que esto no es correcto, contactese con el Administrador y valide los datos considerados.
                        </p>
                        <hr>
                        <p class="mb-0">soporte-imc@confipetrol.pe</p>
                    </div>
                    <br>
                    <br>
                    <br>
                </div>
                <?php
            }
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function load_campos_validarIngreso(){
        try {
            $IdTransito = (int)$_GET['IdTransito'];?>
            <div class="modal-dialog" role="document">
                <form id="formValidaIngreso" method="post">
                    <input type="hidden" name="idtranval" id="idtranval" value="<?=$IdTransito?>">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">
                                Validación Ingreso Transito
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p class="text-center">
                                Debe completar los campos descritos a continuación, estos campos corresponden al <code class="fz-15">Número de Guía Recibida.</code>
                            </p>
                            <div class="row">
                                <div class="col-12" id="mensaje_error_val"></div>
                            </div>
                            <div class="row">
                                <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12">
                                    <input type="text" class="form-control text-center fz-15" tabindex="1"
                                           name="number2" required="required" autocomplete="off" maxlength="2"
                                           oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                           step="1" min="0" onkeydown="return event.keyCode !== 69">

                                </div>
                                <div class="col-xl-1 col-lg-1 col-md-1 col-sm-1 align-middle text-center">
                                    <div class="text-muted fz-18 mt-10">-</div>
                                </div>
                                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12">
                                    <input type="text" class="form-control text-center fz-15" tabindex="2"
                                           name="number5" required="required" autocomplete="off" maxlength="5"
                                           oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                           step="1" min="0" onkeydown="return event.keyCode !== 69">
                                </div>
                                <div class="col-xl-1 col-lg-1 col-md-1 col-sm-1 align-middle text-center">
                                    <div class="text-muted fz-18 mt-10">-</div>
                                </div>
                                <div class="col-xl-5 col-lg-5 col-md-5 col-sm-12">
                                    <input type="text" class="form-control text-center fz-15" tabindex="3"
                                           name="number7" required="required" autocomplete="off" maxlength="7"
                                           oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                           step="1" min="0" onkeydown="return event.keyCode !== 69">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Enviar</button>
                        </div>
                    </div>
                </form>
            </div>
            <?php
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function loadCampos_Depreciacion_Activo(){
        try {?>
            <div class="card card-shadow mb-20">
                <div class="card-header bg-secondary-light-5">
                    <h4 class="card-title font-weight-bold">Datos Depreciación Activo</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        Todos los campos descritos con <code class="font-weight-bold">(*)</code>, son campos obligatorios.
                    </p>
                    <div class="form-group row">
                        <label for="fInicialDepre_itm" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                            Fecha Inicial Depreciación
                            <span class="text-danger font-weight-bold">*</span>
                        </label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control input-md text-left inputFecha" autocomplete="off"
                                   name="fInicialDepre_itm" id="fInicialDepre_itm" maxlength="10" placeholder="**/**/****" required>
                            <small class="form-text text-muted">Indique la fecha de inicio para el cálculo de la depreciación</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                            Costo del Activo
                            <span class="text-danger font-weight-bold">*</span>
                        </label>
                        <div class="col-sm-3">
                            <input type="number" class="form-control input-md text-left" min="0" step="0.01"
                                   name="costoActivo_itm" required placeholder="valor" autocomplete="off">
                            <small class="form-text text-muted">Valor actual del activo</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                            Frecuencia Depreciación del Activo
                            <span class="text-danger font-weight-bold">*</span>
                        </label>
                        <div class="col-sm-3">
                            <input type="number" class="form-control input-md text-left" min="0" step="1"
                                   name="frecDepre_itm" required placeholder="valor" autocomplete="off">
                            <small class="form-text text-muted">Número de meses</small>
                        </div>
                    </div>
                    <div class="row">
                        <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                            Valor Depreciación Mensual
                            <span class="text-danger font-weight-bold">*</span>
                        </label>
                        <div class="col-sm-3">
                            <input type="number" class="form-control input-md text-left" min="0" step="0.01"
                                   name="valMensual_itm" required placeholder="valor" autocomplete="off">
                            <small class="form-text text-muted">Valor a descontar mes a mes</small>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function loadCampos_newCodMaterial(){
        try {
            $idinv = (int)$_GET['idinv'];
            $idalm = (int)$_GET['idalm'];
            $codmat = trim($_GET['codmat']);?>
            <div class="form-group row">
                <div class="col-sm-6 offset-xl-3 offset-lg-3 offset-md-3">
                    <div class="card card-shadow card-body">
                        <form id="formValidate_Codigo" role="form" method="post" style="display: flex">
                            <input type="hidden" name="this_idInvent" value="<?=$idinv?>">
                            <input type="hidden" name="this_idAlm" value="<?=$idalm?>">
                            <input type="hidden" name="this_codMate" value="<?=$codmat?>">
                            <input type="text" class="form-control mb-2 mr-2 text-center" name="newcodmat_itm" id="newcodmat_itm"
                                   placeholder="ingrese nuevo código" required maxlength="12" autocomplete="off">
                            <button type="submit" class="btn btn-outline-success mb-2 mr-2 btnValidate_Submit">
                                <i class="ti-check"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger mb-2" id="btnValidate_Cancel">
                                <i class="ti-close"></i>
                            </button>
                        </form>
                        <small class="form-text text-muted text-center">Máximo 12 carácteres</small>
                    </div>
                </div>
            </div>
            <?php
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function valida_codmaterialNew_Item_JSON(){
        try {
            $datSearch[0] = (int)$_POST['this_idAlm'];
            $datSearch[1] = (int)$_POST['this_idInvent'];
            $datSearch[2] = trim($_POST['newcodmat_itm']);
            $obj_inv = new InventarioModel();
            $seacrching = $obj_inv->busca_existencia_codMaterial_xItem($datSearch);
            if(is_null($seacrching)){?>
                <div class="row">
                    <div class="col-sm-6 offset-xl-3 offset-lg-3 offset-md-3">
                        <div class="card card-shadow">
                            <div class="card-body ">
                                <div class="row">
                                    <div class="col-2 text-right">
                                        <span class="bg-success text-center wb-icon-box" style="margin-top: 18px;">
                                            <i class="ti-check text-light" style="font-size: 50px"></i>
                                        </span>
                                    </div>
                                    <div class="col-6">
                                        <h1 class="mt-1 mb-0"><?=$datSearch[2]?></h1>
                                        <p class="fz-14 mb-0">Código apto para registro</p>
                                    </div>
                                    <div class="col-4">
                                        <button type="button" class="btn btn-info mb-2 mr-2 btn-block mb-10" id="btnActualizar_Cod"
                                                data-cod="<?=$datSearch[2]?>">
                                            Actualizar
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-block" id="btnValidate_Cancel">
                                            Cancelar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <br>
                <?php
            }
            else{?>
                <div class="alert alert-danger mb-10 text-center alert-msje">El código ingresado ya se encuentra registrado.</div>
                <div class="form-group row">
                    <div class="col-sm-6 offset-xl-3 offset-lg-3 offset-md-3">
                        <div class="card card-shadow card-body">
                            <form id="formValidate_Codigo" role="form" method="post" style="display: flex">
                                <input type="hidden" name="this_idInvent" value="<?=$datSearch[1]?>">
                                <input type="hidden" name="this_idAlm" value="<?=$datSearch[0]?>">
                                <input type="hidden" name="this_codMate" value="<?=$datSearch[2]?>">
                                <input type="text" class="form-control mb-2 mr-2 text-center" name="newcodmat_itm" id="newcodmat_itm"
                                       placeholder="ingrese nuevo código" required maxlength="12" autocomplete="off">
                                <button type="submit" class="btn btn-outline-success mb-2 mr-2">
                                    <i class="ti-check"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger mb-2" id="btnValidate_Cancel">
                                    <i class="ti-close"></i>
                                </button>
                            </form>
                            <small class="form-text text-muted text-center">Máximo 12 carácteres</small>
                        </div>
                    </div>
                </div>
                <?php
            }

        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function busca_codMaterialNew_Item_JSON(){
        try {
            $datSearch[0] = (int)$_GET['idalm'];
            $datSearch[1] = (int)$_GET['codmat'];
            $obj_inv = new InventarioModel();
            $dtlleItem = $obj_inv->busca_existencia_Item_xCodMaterial($datSearch);

            $val = 0;
            $message = "El material ingresado ya se encuentra registrado, verifique su inventario y constate su existencia.";
            if(is_null($dtlleItem)){
                $val = 1;
                $message = "Material disponible para registro.";
            }
            echo json_encode(array('status'=>$val, 'message'=>$message));

        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function actualizar_Estado_Transito_JSON(){
        try {
            $dataUpdate[0] = (int)$_POST['id'];
            $dataUpdate[1] = (int)$_POST['valtransito'];
            $dataUpdate[2] = (int)$_POST['valrecepcion'];
            $dataUpdate[3] = trim($_POST['estado']);
            $obj_inv = new InventarioModel();
            $val = 0;
            $message = "Error al realizar la actualización del registro.";
            $update = $obj_inv->update_Estado_Item_Transito($dataUpdate);
            if($update){
                $val = 1;
                $message = "Estado registro actualizado satisfactoriamente.";
            }
            echo json_encode(array('status'=>$val, 'message'=>$message));

        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }
}