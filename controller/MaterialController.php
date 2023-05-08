<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);
require_once '../model/MaterialModel.php';
require_once '../model/AlmacenModel.php';
require_once '../model/ColaboradorModel.php';
require_once '../model/ServicioModel.php';
require_once '../model/PersonaModel.php';
require_once '../model/UnidadMModel.php';
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

require '../assets/plugins/barcode/autoload.php';

require '../assets/plugins/PHPMailer-5.2.25/src/Exception.php';
require '../assets/plugins/PHPMailer-5.2.25/src/PHPMailer.php';
require '../assets/plugins/PHPMailer-5.2.25/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;

$action = $_REQUEST["action"];
$controller = new MaterialController();
call_user_func(array($controller,$action));

class MaterialController {

    public function loadCampos_ingresoMaterial(){
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
            }?>
            <div class="container-fluid no-padding" id="divMTabla">
                <!--
                <div class="page-title pl-0 pr-0 pb-10 text-center">
                    <h4 class="mb-0 text-info">
                        Materiales
                    </h4>
                    <p class="text-muted">
                        Lista de materiales ingresados al almacén.
                    </p>
                </div>
                -->
                <input type="hidden" id="count_almacen" value="<?= sizeof($lstAlmacen) ?>">
                <?php

                if(sizeof($lstAlmacen) == 1){?>
                    <input type="hidden" id="IdAlmacen" value="<?= $lstAlmacen[0]['id_alm'] ?>">
                    <?php
                }
                else if(sizeof($lstAlmacen) > 1){?>
                    <div class="col-lg-5 col-md-6 col-sm-12 col-xs-12 mb-10 pl-0 pt-1 pb-0">
                        <select id="IdAlmacen" class="form-control selectClass" data-placeholder="Almacén...">
                            <option></option>
                            <?php
                            foreach ($lstAlmacen as $almacen){?>
                                <option value="<?=$almacen['id_alm']?>"><?=$almacen['titulo_alm']?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <?php
                }
                ?>
                <div class="card shadow">
                    <table id="Tbl_Material" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th class="text-center text-uppercase align-middle"></th>
                            <th class="text-center text-uppercase align-middle"></th>
                            <th class="text-center text-uppercase align-middle">Clasificación</th>
                            <th class="text-center text-uppercase align-middle">Código</th>
                            <th class="text-center text-uppercase align-middle">Descripción</th>
                            <th class="text-center text-uppercase align-middle">U.M.</th>
                            <th class="text-center text-uppercase align-middle">Ubic.</th>
                            <th class="text-center text-uppercase align-middle">Frecuencia<br>renovación</th>
                            <th class="text-center text-uppercase align-middle">Estado</th>
                            <th class="text-center text-uppercase align-middle"></th>
                        </tr>
                        </thead>
                    </table>
                </div>
                <br><br><br>
            </div>
            <div class="container-fluid" id="divMResponse"></div>
            <?php
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function loadCampos_Ingresos(){
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
            }?>
            <div class="card card-shadow mb-4">
                <ul class="nav nav-fill mb-4 nav-pills" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#tab-material">
                            <i class="fas fa-dolly display-5 op-3"></i>
                            <div class="card-title fz-15 mb-0">Material</div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab-personal">
                            <i class="fa fa-id-card display-5 op-3 fa-flip-horizontal"></i>
                            <div class="card-title fz-15 mb-0">Personal</div>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane active" id="tab-material" role="tabpanel">
                    <div class="container-fluid no-padding" id="divMTabla">
                        <div class="page-title pl-0 pr-0 pb-10 text-center">
                            <h4 class="mb-0 text-info">
                                Materiales
                            </h4>
                            <p class="text-muted">
                                Lista de materiales ingresados al almacén.
                            </p>
                        </div>
                        <input type="hidden" id="count_almacen" value="<?= sizeof($lstAlmacen) ?>">
                        <?php

                        if(sizeof($lstAlmacen) == 1){?>
                            <input type="hidden" id="IdAlmacen" value="<?= $lstAlmacen[0]['id_alm'] ?>">
                            <?php
                        }
                        else if(sizeof($lstAlmacen) > 1){?>
                            <div class="col-lg-5 col-md-6 col-sm-12 col-xs-12 mb-10 pl-0 pt-1 pb-0">
                                <select id="IdAlmacen" class="form-control selectClass" data-placeholder="Almacén...">
                                    <option></option>
                                    <?php
                                    foreach ($lstAlmacen as $almacen){?>
                                        <option value="<?=$almacen['id_alm']?>"><?=$almacen['titulo_alm']?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <?php
                        }
                        ?>
                        <div class="card shadow">
                            <table id="Tbl_Material" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th class="text-center text-uppercase align-middle"></th>
                                    <th class="text-center text-uppercase align-middle"></th>
                                    <th class="text-center text-uppercase align-middle">Clasificación</th>
                                    <th class="text-center text-uppercase align-middle">Código</th>
                                    <th class="text-center text-uppercase align-middle">Descripción</th>
                                    <th class="text-center text-uppercase align-middle">U.M.</th>
                                    <th class="text-center text-uppercase align-middle">Ubic.</th>
                                    <th class="text-center text-uppercase align-middle">Frecuencia<br>renovación</th>
                                    <th class="text-center text-uppercase align-middle">Estado</th>
                                    <th class="text-center text-uppercase align-middle"></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                        <br><br><br>
                    </div>
                    <div class="container-fluid" id="divMResponse"></div>
                </div>
                <div class="tab-pane" id="tab-personal" role="tabpanel">
                    <div class="container-fluid no-padding" id="divPTabla">
                        <div class="page-title pl-0 pr-0 pb-10 text-center">
                            <h4 class="mb-0 text-info">
                                Personal
                            </h4>
                            <p class="text-muted">
                                Lista de colaboradores registrados en el servicio seleccionado.
                            </p>
                        </div>
                        <div class="card shadow">
                            <table id="Tbl_Personal" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th class="text-center text-uppercase align-middle">Servicio</th>
                                    <th class="text-center text-uppercase align-middle">Área Operativa</th>
                                    <th class="text-center text-uppercase align-middle">Apellidos y nombres</th>
                                    <th class="text-center text-uppercase align-middle">Nro.Documento</th>
                                    <th class="text-center text-uppercase align-middle">Biometria</th>
                                    <th class="text-center text-uppercase align-middle" >Estado</th>
                                    <th class="text-center text-uppercase align-middle"></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                        <br><br><br>
                    </div>
                    <div class="container-fluid" id="divPResponse"></div>
                </div>
            </div>
            <?php
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function load_camposDespacho_Material(){
        try {
            $idAlmacen = (int)$_GET['idalm'];
            $idColaboradorTK = $_GET['idntify'];
            $obj_alm = new AlmacenModel();
            $dtlleAlmacen = $obj_alm->detalle_Almacen_xID($idAlmacen);
            $numAutogen = 0;
            $identyAlmacen = 0;
            $desAlmacen = "";
            $idServicio = 0;
            if(!is_null($dtlleAlmacen)){
                $identyAlmacen = (int)$dtlleAlmacen['id_alm'];
                $desAlmacen = $dtlleAlmacen['titulo_alm'];
                $idServicio = $dtlleAlmacen['id_serv'];
                $numAutogen = (int)$dtlleAlmacen['autogen_desp_alm'] + 1;
            }
            $obj_fn = new FuncionesModel();
            $IdColaborador = $obj_fn->encrypt_decrypt("decrypt",$idColaboradorTK);
            $obj_col = new ColaboradorModel();
            $dtlleCol = $obj_col->detalle_Colaborador_xId($IdColaborador);
            if(!is_null($dtlleCol)){
                $txtEstado = '<span class="text-danger-700">CESADO</span>';
                if((int)$dtlleCol['condicion_col'] == 1){ $txtEstado = '<span class="text-success-600">Activo</span>';}
                ?>
                <input type="hidden" id="idIdentify" value="<?=$IdColaborador?>">
                <div class="row">
                    <div class="col-xl-6 col-lg-5 col-md-7 col-sm-8 mb-4 offset-xl-2 offset-lg-2 offset-md-1">
                        <div class="card mb-4 card-shadow">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-2">
                                <span class="bg-warning rounded-circle text-center wb-icon-box">
                                    <i class="icon-user text-light f24"></i>
                                </span>
                                    </div>
                                    <div class="col-9">
                                        <h4 class="mt-1 mb-0 text-uppercase" id="textPersona">
                                            <?=$dtlleCol['apa_col']." ".$dtlleCol['ama_col'].", ".$dtlleCol['nombres_col']?>
                                        </h4>
                                        <footer class="blockquote-footer">
                                            <strong>Puesto: </strong>
                                            <cite title="Source Title">
                                                <?=mb_strtoupper($dtlleCol['cargo_col'],"UTF-8")?>
                                            </cite>
                                        </footer>
                                        <p class="f12 mb-0 float-right">Estado: <span class="bold-500"><?=$txtEstado?></span></p>
                                        <footer class="blockquote-footer">
                                            <strong>Servicio: </strong>
                                            <cite title="Source Title"><?=$dtlleCol['servicio_col']?></cite>
                                        </footer>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-3 col-md-3 col-sm-4 mb-4">
                        <button type="button" class="btn btn-outline-secondary btn-block btn-hover-transform" id="btnNewSearch">Nueva búsqueda</button>
                        <button type="button" class="btn btn-outline-secondary btn-block btn-hover-transform"
                                id="btnHistory" data-id="<?=$idColaboradorTK?>" >Historial</button>
                        <button type="button" class="btn btn-danger btn-block btn-hover-transform" id="btnCancelHistory" style="display: none">Cancelar</button>
                    </div>
                </div>
                <div class="container-fluid no-padding" id="divDespacho">
                    <?php
                    if((int)$dtlleCol['condicion_col'] == 1){?>
                        <div class="page-title text-center py-10">
                            <h4 class="mb-0 text-brown-800 font-weight-bold">
                                Movimientos a ejecutar
                            </h4>
                            <p class="breadcrumb-item text-muted mb-0">Elija una transacción a realizar.</p>
                        </div>
                        <div class="card card-shadow mb-4">
                            <ul class="nav nav-fill mb-4 nav-pills" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#tab-retiro">
                                        <i class="fas fa-dolly display-5 op-3"></i>
                                        <div class="card-title fz-15 mb-0">Retiro</div>
                                    </a>
                                </li><!--
                                <li class="nav-item">
                                    <a class="nav-link btn-hover-transform" data-toggle="tab" href="#tab-devolucion">
                                        <i class="fas fa-dolly display-5 op-3 fa-flip-horizontal"></i>
                                        <div class="card-title fz-15 mb-0">Devolución</div>
                                    </a>
                                </li>-->
                            </ul>
                        </div>
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab-retiro" role="tabpanel">
                                <div class="container-fluid mt-20 no-padding">
                                    <div class="row">
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 mb-10 offset-xl-2 offset-lg-2 offset-md-2">
                                            <div class="card mb-4 card-shadow">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-3">
                                                            <span class="bg-primary rounded-circle text-center wb-icon-box">
                                                                <i class="icon-notebook text-light f24"></i>
                                                            </span>
                                                        </div>
                                                        <div class="col-9">
                                                            <h3 class="mt-1 mb-0">
                                                                <?="DP-".$identyAlmacen."-".str_pad($numAutogen,6,"0",STR_PAD_LEFT)?>
                                                            </h3>
                                                            <p class="f12 mb-0">Número de transacción</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 mb-10">
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
                                </div>
                                <div class="container-fluid no-padding">
                                    <div class="form-group row">
                                        <div class="col-12 text-center">
                                            <h5 class="card-title text-center text-primary pt-20">Despacho de Material</h5>
                                            <p class="text-center">Digite el <code class="highlighter-rouge">código del material</code> para realizar su búsqueda respectiva.</p>
                                            <form id="frm_searchMaterial" class="form-inline" style="display: block">
                                                <input hidden name="idcoltk_des" id="idcoltk_des" value="<?=$idColaboradorTK?>">
                                                <input hidden name="namecol_des" id="namecol_des" value="<?=$dtlleCol['apa_col']." ".$dtlleCol['ama_col'].", ".$dtlleCol['nombres_col']?>">
                                                <input hidden name="puestocol_des" id="puestocol_des" value="<?=$dtlleCol['cargo_col']?>">
                                                <input hidden name="areaopcol_des" id="areaopcol_des" value="<?=$dtlleCol['areaop_col']?>">
                                                <input hidden name="ndoc_des" id="ndoc_des" value="<?=$obj_fn->encrypt_decrypt("encrypt",$dtlleCol['ndoc_col'])?>">
                                                <input hidden name="idalmacen_des" id="idalmacen_des" value="<?=$identyAlmacen?>">
                                                <input hidden name="desAlmacen_des" id="desAlmacen_des" value="<?=$desAlmacen?>">
                                                <input hidden name="idServicio_des" id="idServicio_des" value="<?=$idServicio?>">
                                                <input hidden name="fechahora_des" id="fechahora_des" value="<?=date("Y-m-d H:i")?>">
                                                <input hidden name="transaccion_des" id="transaccion_des" value="<?="DP-".$identyAlmacen."-".str_pad($numAutogen,6,"0",STR_PAD_LEFT)?>">
                                                <input hidden name="codigo_des" id="codigo_des" value="<?=$numAutogen?>">
                                                <select class="form-control text-lg-search-mat mr-2 fz-18" style="height: 56px;" id="selTipo" name="selTipo">
                                                    <option value="EPPS" selected>EPPS</option>
                                                    <option value="CONSUMIBLES">CONSUMIBLES</option>
                                                    <option value="MATERIALES INSTA. PERMANENTE">MATERIALES INSTA. PERMANENTE</option>
                                                    <option value="MATERIALES EQUI. UTILES DE OFICINA">MATERIALES EQUI. UTILES DE OFICINA</option>
                                                    <option value="REPUESTOS PROPIOS">REPUESTOS PROPIOS</option>
                                                    <option value="REPUESTOS TERCEROS">REPUESTOS TERCEROS</option>
                                                </select>
                                                <input class="form-control form-control-lg text-center text-lg-search-mat mr-2 width-40 fz-20"
                                                       id="codmaterial" name="codmaterial" type="text" placeholder="código de material" autocomplete="off">
                                                <button type="submit" class="btn btn-info btn-lg btn-hover-transform" style="height: 55px" title="Buscar">Buscar</button>
                                                <button type="button" class="btn btn-warning btn-lg btn-hover-transform" style="height: 55px;font-size: 25px;" title="Grabar"
                                                        id="btnSaveDespacho">
                                                    <i class="ti-save"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="container-fluid" id="divTblMaterial">
                                    <table id="Tbl_Despacho" class="table table-md border-separate sw-border-gap-15">
                                        <thead>
                                        <tr>
                                            <th data-toggle="true" class="text-center text-uppercase whitespace-no-wrap align-middle tracking-widest text-sm font-medium px-4 py-3 text-grey-800">#</th>
                                            <th data-toggle="true" class="text-center text-uppercase whitespace-no-wrap align-middle tracking-widest text-sm font-medium px-4 py-3 text-grey-800">Código</th>
                                            <th data-hide="phone,tablet" class="text-center text-uppercase whitespace-no-wrap align-middle tracking-widest text-sm font-medium px-4 py-3 text-grey-800">Descripción</th>
                                            <th data-hide="phone,tablet" class="text-center text-uppercase whitespace-no-wrap align-middle tracking-widest text-sm font-medium px-4 py-3 text-grey-800">U.M.</th>
                                            <th data-hide="phone,tablet" class="text-center text-uppercase whitespace-no-wrap align-middle tracking-widest text-sm font-medium px-4 py-3 text-grey-800" style="width: 20%">Cantidad</th>
                                            <th data-hide="phone,tablet" class="text-center text-uppercase whitespace-no-wrap align-middle tracking-widest text-sm font-medium px-4 py-3 text-grey-800"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr class="transition duration-300 ease-in-out hover:shadow-lg">
                                            <td colspan="6" class="text-center text-muted">
                                                No se encontraron materiales agregados.
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!--<div class="tab-pane" id="tab-devolucion" role="tabpanel"></div>-->
                        </div>
                        <?php
                    }
                    else  if((int)$dtlleCol['condicion_col'] == 2){?>
                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 mb-4 offset-xl-3 offset-lg-3 offset-md-3">
                                <div class="alert alert-warning" role="alert">
                                    <h4 class="alert-heading">AVISO</h4>
                                    <p>El Personal se encuentra en condición de CESE, no es posible generar ninguna transacción.</p>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <div class="container-fluid" id="divHistorial" style="display:none">
                    <div class="page-title text-center py-10">
                        <h4 class="mb-0 text-brown-800 font-weight-bold">
                            Historial Despacho
                        </h4>
                        <p class="breadcrumb-item text-muted mb-0">Muestra las salidas de los materiales generados al colaborador.</p>
                    </div>
                    <div class="card card-shadow">
                        <div class="table-responsive">
                            <table id="Tbl_Historial" class="table datatable-responsive-row-control">
                                <thead>
                                <tr>
                                    <th>Cod.Despacho</th>
                                    <th>Servicio</th>
                                    <th>Almacen</th>
                                    <th>Código</th>
                                    <th>Descripción material</th>
                                    <th>U.M.</th>
                                    <th>Cantidad</th>
                                    <th>Fecha Entrega</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <?php
            }
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function searching_Material_xCodigo_JSON(){
        try {
            $idAlmacen = (int)$_POST['idalm'];
            $codMaterial = trim($_POST['codmat']);
            $val = 0;
            $mesage = 'El código ingresado no pertenece a ningún material registrado al almacén seleccionado, verifique nuevamente el código de material ingresado y vuelva a intentarlo.';
            $obj_mat = new MaterialModel();
            $dtlleMaterial = $obj_mat->buscar_Material_xCodigo($idAlmacen,$codMaterial);

            $datos = array();
            if(!is_null($dtlleMaterial)){
                $val = 1;
                $mesage = 'Código ubicado.';
                $datos = array(
                    'idalm'=>$dtlleMaterial['id_alm'],
                    'idmat'=>$dtlleMaterial['id_mat'],
                    'clasificacion'=>$dtlleMaterial['clasificacion_mat'],
                    'codigo'=>$dtlleMaterial['cod_mat'],
                    'des'=>$dtlleMaterial['des_mat'],
                    'um'=>$dtlleMaterial['um_mat'],
                    'renova'=>$dtlleMaterial['renova_mat'],
                    'frecrenova'=>$dtlleMaterial['frecrenova_mat']
                );
            }
            echo json_encode(array('status'=>$val,'detail'=>$datos,'message'=>$mesage));

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_Despacho_JSON(){
        try {
            date_default_timezone_set("America/Lima");
            setlocale(LC_TIME, 'es_PE.UTF-8');
            $obj_fn = new FuncionesModel();
            $idUsuario = $obj_fn->encrypt_decrypt('decrypt',$_POST['idUsusrio_tk']);
            $idColaborador = (int)$obj_fn->encrypt_decrypt('decrypt',$_POST['idColaborador_tk']);
            $nameColaborador = $_POST['namecol'];
            $puestoColaborador = $_POST['puestocol'];
            $areaColaborador = $_POST['areaopcol'];
            $docColaborador = $obj_fn->encrypt_decrypt('decrypt',$_POST['ndoccol']);
            $idAlmacen = (int)$_POST['idAlmacen'];
            $desAlmacen = trim($_POST['desAlmacen']);
            $idServicio = (int)$_POST['idServicio'];
            $fechaHoraRegistro = trim($_POST['fechahora']);
            $fechaInicioTransac = explode(" ",$fechaHoraRegistro);
            $fechaTransac = trim($fechaInicioTransac[0]);
            $horaTransac  = trim($fechaInicioTransac[1]);
            $codTransaccion = trim($_POST['codtransaccion']);
            $codigoOperacion = (int)$_POST['codigo'];
            $detalleMaterial = $_POST['detalle'];

            $tipoValidacion = trim($_POST['tipovalida']);
            $codigoValidacion = trim($_POST['codigovalida']);
            $timeValidacion = trim($_POST['timevalida']);
            $statusValidacion = (int)$_POST['statusvalida'];
            $tipoDespacho = trim($_POST['seltipo']);

            $obj_serv = new ServicioModel();
            $dtlleServ = $obj_serv->detalle_Servicio_xID($idServicio);
            $desServicio = "";
            if(!is_null($dtlleServ)){ $desServicio = $dtlleServ['des_serv']; }
            $obj_per = new PersonaModel();
            $dtllePersona = $obj_per->detalle_Persona_xIDUsuario($idUsuario);
            $nameUsuario = "";
            $ndocusuario = "";
            if(!is_null($dtllePersona)){
                $nameUsuario = $dtllePersona['ape_pa_per']." ".$dtllePersona['ape_ma_per'].", ".$dtllePersona['nombres_per'];
                $ndocusuario = trim($dtllePersona['ndoc_per']);
            }

            $obj_mat = new MaterialModel();
            if((int)$_POST['codigo'] > 0){
                $obj_alm = new AlmacenModel();
                //verificamos si existe el numero de despacho generado en el almacen
                $dtlleMovDespacho = $obj_mat->detalle_Despachos_xNumOperacion($idAlmacen, $codigoOperacion);
                if (!is_null($dtlleMovDespacho)) {
                    $dtlleAlmacen = $obj_alm->detalle_Almacen_xID($idAlmacen);
                    if (!is_null($dtlleAlmacen)) {
                        $numIncrement = (int)$dtlleAlmacen['autogen_desp_alm'] + 1;
                        $codigoOperacion = $numIncrement;
                        $codTransaccion = "DP-" . $idAlmacen . "-" . str_pad($numIncrement, 6, "0", STR_PAD_LEFT);
                    }
                }

                //Actualizamos correlativo de autogenerado
                $obj_alm->actualizar_Correlativo_NroDespacho($idAlmacen, $codigoOperacion);
            }

            $datesDes[0] = $idServicio;
            $datesDes[1] = $desServicio;
            $datesDes[2] = $idAlmacen;
            $datesDes[3] = $desAlmacen;
            $datesDes[4] = $idColaborador;
            $datesDes[5] = $nameColaborador;
            $datesDes[6] = $puestoColaborador;
            $datesDes[7] = $docColaborador;
            $datesDes[8] = $fechaTransac;
            $datesDes[9] = $horaTransac;
            $datesDes[10]= $codTransaccion;
            $datesDes[11]= $codigoOperacion;
            $datesDes[12]= date("Y-m-d H:i:s");
            $datesDes[13]= $idUsuario;
            $datesDes[14]= $nameUsuario;
            $datesDes[15]= $tipoValidacion;
            $datesDes[16]= $codigoValidacion;
            $datesDes[17]= $statusValidacion;
            $datesDes[18]= $timeValidacion;
            $datesDes[19]= "GENERADO";
            $datesDes[20]= $ndocusuario;
            $datesDes[21]= $tipoDespacho;
            $val = 0;
            $message = "Error al realizar el registro del despacho.";

            $insertID = $obj_mat->registrar_Despacho_lastID($datesDes);
            if((int)$insertID > 0) {
                $val = 1;
                $message = "Despacho realizado satisfactoriamente";
                for($j=0; $j<sizeof($detalleMaterial);$j++){
                    $frecuencia = 0;
                    $fechaEstRenovacion = "0000-00-00";
                    if((int)$detalleMaterial[$j][6] == 1){
                        $frecuencia = (int)$detalleMaterial[$j][7];
                        $fechaEstRenovacion = $obj_fn->sumar_meses_fecha($fechaTransac,$frecuencia);
                    }

                    $dtlleMat = $obj_mat->detalle_Material_xID($detalleMaterial[$j][1]);
                    if(!is_null($dtlleMat)){
                        if((int)$dtlleMat['actiondel_mat'] == 1){
                            $obj_mat->actualizar_actionDelete_Material_xID($detalleMaterial[$j][1],0);
                        }
                    }

                    $datesMOV[0] = $insertID;
                    $datesMOV[1] = $codTransaccion;
                    $datesMOV[2] = $detalleMaterial[$j][1];//idmat
                    $datesMOV[3] = $detalleMaterial[$j][2];//clasificacion
                    $datesMOV[4] = $detalleMaterial[$j][3];//codigo
                    $datesMOV[5] = $detalleMaterial[$j][4];//des
                    $datesMOV[6] = $detalleMaterial[$j][5];//um
                    $datesMOV[7] = $detalleMaterial[$j][8];//cant
                    $datesMOV[8] = $areaColaborador;
                    $datesMOV[9] = $fechaTransac;
                    $datesMOV[10]= $horaTransac;
                    $datesMOV[11]= $frecuencia;
                    $datesMOV[12]= $fechaEstRenovacion;
                    $datesMOV[13]= $idColaborador;
                    $datesMOV[14]= $docColaborador;
                    $datesMOV[15]= $nameColaborador;
                    $datesMOV[16]= $idAlmacen;
                    $datesMOV[17]= $desAlmacen;
                    $datesMOV[18]= $idServicio;
                    $datesMOV[19]= $desServicio;
                    $datesMOV[20]= $fechaHoraRegistro;
                    $datesMOV[21]= $nameUsuario;
                    $obj_mat->registrar_Despacho_Detalle($datesMOV);
                }
            }

            echo json_encode(array('status'=>$val, 'message'=>$message,'idDespacho'=>$obj_fn->encrypt_decrypt('encrypt',$insertID)));

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function historial_Detalle_xColaborador_JSON(){
        try {
            $obj_fn = new FuncionesModel();
            $idColaborador = (int)$obj_fn->encrypt_decrypt('decrypt',$_GET['id']);

            $obj_mat = new MaterialModel();
            $lstDespachos = $obj_mat->lista_Despachos_Detalle_Historial_xColaborador($idColaborador);
            $datos = array();
            if(!is_null($lstDespachos)){
                foreach ($lstDespachos as $item){
                    $row = array(
                        0 => $item['codigodes'],
                        1 => $item['servicio'],
                        2 => $item['almacen'],
                        3 => $item['codigo'],
                        4 => $item['descripcion'],
                        5 => $item['unidadm'],
                        6 => $item['cantidad'],
                        7 => $item['fechaentrega']." ".$item['horaentrega']
                    );
                    array_push($datos, $row);
                }
            }
            echo json_encode(array('data' => $datos));
            unset($datos);
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_Despachos_xColaborador_JSON(){
        try {
            $obj_fn = new FuncionesModel();
            $ndoc = trim($_POST['ndoc_his']);
            $fechaDesde = null;
            $fechaHasta = null;
            if(isset($_POST['fDesde_his'])){ $fechaDesde = $obj_fn->fecha_ESP_ENG($_POST['fDesde_his']); }
            if(isset($_POST['fHasta_his'])){ $fechaHasta = $obj_fn->fecha_ESP_ENG($_POST['fHasta_his']); }

            $val = 0;
            $mesage = 'El número ingresado no pertenece a ningún colaborador registrado, verifique nuevamente el número de documento <code>(DNI/CEX)</code> y vuelva a intentarlo';

            $obj_col = new ColaboradorModel();
            $dtlleCol = $obj_col->buscar_colaborador_xnDoc($ndoc);
            $datos = array();
            $idColaborador = 0;
            if(!is_null($dtlleCol)){
                $val = 1;
                $mesage = 'Colaborador validado';
                $idColaborador = (int)$dtlleCol['id_col'];
                $obj_mat = new MaterialModel();
                if(is_null($fechaDesde) && is_null($fechaHasta)) {
                    $lstDespachos = $obj_mat->lista_Despachos_xColaborador($idColaborador);
                }
                else{
                    $datosSearch[0] = $idColaborador;
                    $datosSearch[1] = trim($fechaDesde);
                    $datosSearch[2] = trim($fechaHasta);
                    $lstDespachos = $obj_mat->lista_Despachos_Rango_xColaborador($datosSearch);
                }

                if(!is_null($lstDespachos)){
                    foreach ($lstDespachos as $item){
                        $row = array(
                            0 => $item['codigodes'],
                            1 => $item['servicio'],
                            2 => $item['almacen'],
                            3 => $item['codigo'],
                            4 => $item['descripcion'],
                            5 => $item['unidadm'],
                            6 => $item['cantidad'],
                            7 => $item['fechaentrega']." ".$item['horaentrega']
                        );
                        array_push($datos, $row);
                    }
                }
            }
            echo json_encode(array('status'=>$val,'message'=>$mesage,'identify'=>$obj_fn->encrypt_decrypt("encrypt",$idColaborador),'data' => $datos));
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_Consumos_xAlmacen_JSON(){
        try {
            $obj_fn = new FuncionesModel();
            $idAlmacen = 0;
            if (!empty($_GET['almacen'])) { $idAlmacen = (int)$_GET['almacen'];}

            $fechaInicio = null;
            $fechaFin = null;
            if (!empty($_GET['fechaini'])) {
                $fechaInicio = $obj_fn->fecha_ESP_ENG($_GET['fechaini']);
            }
            if (!empty($_GET['fechafin'])) {
                $fechaFin = $obj_fn->fecha_ESP_ENG($_GET['fechafin']);
            }


            $datosSearch[0] = $idAlmacen;
            $datosSearch[1] = trim($fechaInicio);
            $datosSearch[2] = trim($fechaFin);
            $obj_mat = new MaterialModel();
            $lstConsumos = $obj_mat->lista_Consumos_Rango_xAlmacen($datosSearch);

            $datos = array();
            if(is_array($lstConsumos)){
                foreach($lstConsumos as $movimiento){
                    $codigo = explode("-",$movimiento['codigodes']);
                    $row = array(
                        0 => $movimiento['colaborador'],
                        1 => $movimiento['area'],
                        2 => $movimiento['fechaentrega']." ".$movimiento['horaentrega'],
                        3 => $movimiento['creadopor'],
                        4 => str_pad((int)$codigo[2],3,"0",STR_PAD_LEFT),
                        5 => $movimiento['codigo'],
                        6 => $movimiento['descripcion'],
                        7 => $movimiento['unidadm'],
                        8 => $movimiento['cantidad']
                    );
                    array_push($datos, $row);
                }
            }
            echo json_encode(array('data' => $datos));
            unset($datos);

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_Despachos_Reporte_xColaborador_JSON(){
        try {
            $obj_fn = new FuncionesModel();
            $ndoc = trim($_POST['ndoc_his']);
            $fechaDesde = null;
            $fechaHasta = null;
            if(isset($_POST['fDesde_his'])){ $fechaDesde = $obj_fn->fecha_ESP_ENG($_POST['fDesde_his']); }
            if(isset($_POST['fHasta_his'])){ $fechaHasta = $obj_fn->fecha_ESP_ENG($_POST['fHasta_his']); }

            $val = 0;
            $mesage = 'El número ingresado no pertenece a ningún colaborador registrado, verifique nuevamente el número de documento <code>(DNI/CEX)</code> y vuelva a intentarlo';

            $obj_col = new ColaboradorModel();
            $dtlleCol = $obj_col->buscar_colaborador_xnDoc($ndoc);
            $datos = array();
            $idColaborador = 0;
            if(!is_null($dtlleCol)){
                $val = 1;
                $mesage = 'Colaborador validado';
                $idColaborador = (int)$dtlleCol['id_col'];
                $obj_mat = new MaterialModel();
                if(is_null($fechaDesde) && is_null($fechaHasta)) {
                    $lstDespachos = $obj_mat->lista_Despachos_xColaborador($idColaborador);
                }
                else{
                    $datosSearch[0] = $idColaborador;
                    $datosSearch[1] = trim($fechaDesde);
                    $datosSearch[2] = trim($fechaHasta);
                    $lstDespachos = $obj_mat->lista_Despachos_Rango_xColaborador($datosSearch);
                }

                if(!is_null($lstDespachos)){
                    foreach ($lstDespachos as $item){
                        $styleEstado = " text-success ";
                        $inDel = "";
                        $outDel = "";
                        if((int)$item['condicion_des'] == 0){
                            $styleEstado = " text-danger-600 ";
                            $inDel = "<del>";
                            $outDel = "</del>";
                        }
                        $row = array(
                            0 => $inDel.$item['servicio'].$outDel,
                            1 => $inDel.$item['almacen'].$outDel,
                            2 => $inDel.$item['ndoc'].$outDel,
                            3 => $inDel.$item['colaborador'].$outDel,
                            4 => $inDel.$item['fecha']." ".$item['hora'].$outDel,
                            5 => $inDel.str_pad($item['codigo'],3,"0",STR_PAD_LEFT).$outDel,
                            6 => $inDel.$item['validacion'].$outDel,
                            7 => $item['estado'],
                            8 => $obj_fn->encrypt_decrypt("encrypt",$item['id_des']),
                            9 => $item['fechajun'],
                            10=> $styleEstado,
                            11=>(int)$item['condicion_des']
                        );
                        array_push($datos, $row);
                    }
                }
            }
            echo json_encode(array('status'=>$val,'message'=>$mesage,'identify'=>$obj_fn->encrypt_decrypt("encrypt",$idColaborador),'data' => $datos));
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function load_campos_AnulaDespacho(){
        try {
            $idDespacho = $_GET['iddes'];
            $idUsuario = $_GET['idus'];?>
            <div class="modal-dialog modal-sm" role="document">
                <form id="formAnulaDespacho" method="post">
                    <input type="hidden" name="iddes_tk" value="<?=$idDespacho?>">
                    <input type="hidden" name="idus_tk" value="<?=$idUsuario?>">
                    <input type="hidden" name="dateh" value="<?=date("Y-m-d H:i:s")?>">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">
                               Anular despacho
                            </h5>
                        </div>
                        <div class="modal-body">
                            <p class="text-center fz-12 mb-0">
                                <code><em>De be indicar el motivo por el cual se procederá a anular el despacho seleccionado, este proceso una vez realizado no podra ser revertido.</em></code>
                            </p>
                            <div class="form-group row">
                                <div class="col-12" id="mensaje_error_val"></div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <textarea name="motivo_anu" required class="form-control" rows="5" cols="1" maxlength="500" placeholder="describa el motivo de la anulación"></textarea>
                                    <span class="text-muted fz-12">Máximo 500 caracteres</span>
                                </div>
                        </div>
                        <div class="modal-footer text-center pb-0">
                            <button type="button" class="btn btn-default mr-10" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Enviar</button>
                        </div>
                    </div>
                </form>
            </div>
            <?php
        } catch (PDOException $e) {
            throw  $e;
        }
    }

    public function anular_Despacjo_JSON(){
        try {
            date_default_timezone_set("America/Lima");
            setlocale(LC_TIME, 'es_PE.UTF-8');
            $obj_fn = new FuncionesModel();
            $idDespacho = $obj_fn->encrypt_decrypt("decrypt",$_POST['iddes_tk']);
            $idUsuario  = $obj_fn->encrypt_decrypt("decrypt",$_POST['idus_tk']);
            $fechaAnula = trim($_POST['dateh']);
            $motivoAnula = trim($_POST['motivo_anu']);
            $obj_per = new PersonaModel();
            $dtllePersona = $obj_per->detalle_Persona_xIDUsuario($idUsuario);
            $nameUsuario = "";
            if(!is_null($dtllePersona)){
                $nameUsuario = $dtllePersona['ape_pa_per']." ".$dtllePersona['ape_ma_per'].", ".$dtllePersona['nombres_per'];
            }

            $datesTAB[0] = $idDespacho;
            $datesTAB[1] = $idUsuario;
            $datesTAB[2] = $nameUsuario;
            $datesTAB[3] = $fechaAnula;
            $datesTAB[4] = $motivoAnula;
            $datesTAB[5] = "ANULADO";
            $datesTAB[6] = 0;
            $val = 0;
            $message = "Error al anular el despacho.";
            $obj_mat = new MaterialModel();
            $updateDespacho = $obj_mat->anula_Despacho_xID($datesTAB);
            if($updateDespacho){
                $obj_mat->anula_Despacho_Detalle_xIDDespacho($idDespacho);
                $val = 1;
                $this->enviarEmail_alertAnulaDespacho($idDespacho, $idUsuario);
            }


            echo json_encode(array('status'=>$val, 'message'=>$message));

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_Material_xAlmacen_JSON(){
        try {
            $idAlmacen = 0;
            if (!empty($_GET['idAlmacen'])) {
                $idAlmacen =  (int)$_GET['idAlmacen'];
            }

            $datos = array();
            if($idAlmacen > 0) {
                $obj_mat = new MaterialModel();
                $lstMaterial = $obj_mat->lista_Material_xAlmacen($idAlmacen);
                if (!is_null($lstMaterial)) {
                    foreach ($lstMaterial as $material) {

                        $btnBaja= '<a class="cursor-pointer text-hover-danger" id="bajaAltaMat" data-id="'.$material['id_mat'].'" data-opc="0" title="Baja"><i class="f24 opacity-7 ti-thumb-down"></i></a>';
                        $btnAlta= '<a class="cursor-pointer text-hover-primary" id="bajaAltaMat" data-id="'.$material['id_mat'].'" data-opc="1" title="Alta"><i class="f24 opacity-7 ti-thumb-up"></i></a>';
                        $btnEdit= '<a class="cursor-pointer text-hover-primary ml-10" id="editMat" data-id="'.$material['id_mat'].'" data-idalm="'.$idAlmacen.'" title="Editar"><i class="f24 opacity-7 ti-pencil-alt"></i></a>';
                        $btnDel = '<a class="cursor-pointer text-hover-danger ml-10" id="deleteMat" data-id="'.$material['id_mat'].'" data-opc="'.$material['actiondel_mat'].'" title="Eliminar"><i class="f24 opacity-7 ti-trash"></i></a>';

                        $optBajaAlta = "";
                        $optEdit = "";

                        $estado = "";
                        if ((int)$material['condicion_mat'] == 1) {
                            $estado = '<span class="label label-block text-success-600">ACTIVO</span>';
                            $optBajaAlta = $btnBaja;
                            $optEdit = $btnEdit;
                        }
                        else if ((int)$material['condicion_mat'] == 0) {
                            $estado = '<span class="label label-block text-danger">SUSPENDIDO</span>';
                            $optBajaAlta = $btnAlta;
                        }

                        $optDel = "";
                        if((int)$material['actiondel_mat'] == 1) {
                            $optDel = $btnDel;
                        }

                        $frecuencia = "";
                        if((int)$material['frecrenova_mat'] > 0){
                            $frecuencia = $material['frecrenova_mat']." MESES";
                        }

                        $barCode = "";
                        if(!is_null($material['cod_mat'])){
                            $barCode = '<a  class="cursor-pointer text-hover-primary" id="btn_generatedCBar"'.
                                '    data-cod="'.$material['cod_mat'].'" data-des="'.$material['des_mat'].'" data-um="'.$material['um_mat'].'" title="Generar código">'.
                                '    <i class="fa fa-barcode position-left"></i>'.
                                '    <u>'.$material['cod_mat'].'</u>'.
                                '</a>';
                        }

                        $textUbica = "";
                        if(!is_null($material['ubica_mat'])){ $textUbica = trim($material['ubica_mat']); }

                        $row = array(
                            0 => "",
                            1 => (int)$material['id_mat'],
                            2 => $material['clasificacion_mat'],
                            3 => $barCode,
                            4 => $material['des_mat'],
                            5 => $material['um_mat'],
                            6 => $textUbica,
                            7 => $frecuencia,
                            8 => $estado,
                            9 => $optBajaAlta.$optEdit.$optDel
                        );

                        array_push($datos, $row);
                    }
                }
            }

            $tabla = array('data' => $datos);
            echo json_encode($tabla);
            unset($datos);

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function altaBaja_Material_JSON(){
        try {
            $datesUpdate[0] = (int)$_POST['id'];
            $datesUpdate[1] = (int)$_POST['estado'];
            $val = 0;
            $message = "Se produjo un error al intentar actualizar el estado del Material.";
            $obj_mat = new MaterialModel();
            $updateEstado = $obj_mat->update_Material_Estado_xID($datesUpdate);
            if($updateEstado) {
                $val = 1;
                $message = "Material actualizado satisfactoriamente.";
            }

            echo json_encode(array('status'=>$val,'message'=>$message));

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function delete_Material_JSON(){
        try {
            $idmaterial = (int)$_POST['id'];
            $Opc = (int)$_POST['opc'];
            $val = 0;
            $message = "Se produjo un error al intentar eliminar el Material.";
            if($Opc == 1) {
                $obj_mat = new MaterialModel();
                $delete = $obj_mat->eliminar_Material_xID($idmaterial);
                if ($delete) {
                    $val = 1;
                    $message = "Material eliminado satisfactoriamente.";
                }
            }

            echo json_encode(array('status'=>$val,'message'=>$message));

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function loadCampos_EditarMaterial(){
        try {
            $idMaterial = (int)$_GET['idmat'];
            $idAlmacen = (int)$_GET['idalm'];
            $obj_mat = new MaterialModel();
            $dtlleMaterial = $obj_mat->detalle_Material_xID($idMaterial);
            $obj_um = new UnidadMModel();
            $lstUM = $obj_um->listar_unidadM_All();?>
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="page-title">
                            <h4 class="mb-0 text-warning-0">
                                Actualizar Material
                            </h4>
                            <ol class="breadcrumb mb-0 pl-0 pt-1 pb-0">
                                <li class="breadcrumb-item text-muted">Actualice los datos del material.</li>
                            </ol>
                        </div>
                    </div>
                </div>
                <div id="divchangeCodigo"></div>
                <form id="formEditMaterial" role="form" method="post">
                    <input type="hidden" name="idalm_i" value="<?=$idAlmacen?>">
                    <input type="hidden" name="idmat_i" value="<?=$idMaterial?>">
                    <div class="card card-shadow mb-20">
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
                                    <select name="clasifica_i" class="form-control classSelect" required data-placeholder="Seleccione...">
                                        <option></option>
                                        <?php
                                        if(trim($dtlleMaterial['clasificacion_mat']) == "CONSUMIBLES"){?>
                                            <option value="CONSUMIBLES" selected>CONSUMIBLES</option>
                                            <option value="EPPS">EPPS</option>
                                            <option value="MATERIALES INSTA. PERMANENTE">MATERIALES INSTA. PERMANENTE</option>
                                            <option value="MATERIALES EQUI. UTILES DE OFICINA">MATERIALES EQUI. UTILES DE OFICINA</option>
                                            <option value="REPUESTOS PROPIOS">REPUESTOS PROPIOS</option>
                                            <option value="REPUESTOS TERCEROS">REPUESTOS TERCEROS</option>
                                            <?php
                                        }
                                        else if(trim($dtlleMaterial['clasificacion_mat']) == "EPPS"){?>
                                            <option value="CONSUMIBLES" >CONSUMIBLES</option>
                                            <option value="EPPS" selected>EPPS</option>
                                            <option value="MATERIALES INSTA. PERMANENTE">MATERIALES INSTA. PERMANENTE</option>
                                            <option value="MATERIALES EQUI. UTILES DE OFICINA">MATERIALES EQUI. UTILES DE OFICINA</option>
                                            <option value="REPUESTOS PROPIOS">REPUESTOS PROPIOS</option>
                                            <option value="REPUESTOS TERCEROS">REPUESTOS TERCEROS</option>
                                            <?php
                                        }
                                        else if(trim($dtlleMaterial['clasificacion_mat']) == "MATERIALES INSTA. PERMANENTE"){?>
                                            <option value="CONSUMIBLES" >CONSUMIBLES</option>
                                            <option value="EPPS">EPPS</option>
                                            <option value="MATERIALES INSTA. PERMANENTE" selected>MATERIALES INSTA. PERMANENTE</option>
                                            <option value="MATERIALES EQUI. UTILES DE OFICINA">MATERIALES EQUI. UTILES DE OFICINA</option>
                                            <option value="REPUESTOS PROPIOS">REPUESTOS PROPIOS</option>
                                            <option value="REPUESTOS TERCEROS">REPUESTOS TERCEROS</option>
                                            <?php
                                        }
                                        else if(trim($dtlleMaterial['clasificacion_mat']) == "MATERIALES EQUI. UTILES DE OFICINA"){?>
                                            <option value="CONSUMIBLES" >CONSUMIBLES</option>
                                            <option value="EPPS">EPPS</option>
                                            <option value="MATERIALES INSTA. PERMANENTE">MATERIALES INSTA. PERMANENTE</option>
                                            <option value="MATERIALES EQUI. UTILES DE OFICINA" selected>MATERIALES EQUI. UTILES DE OFICINA</option>
                                            <option value="REPUESTOS PROPIOS">REPUESTOS PROPIOS</option>
                                            <option value="REPUESTOS TERCEROS">REPUESTOS TERCEROS</option>
                                            <?php
                                        }
                                        else if(trim($dtlleMaterial['clasificacion_mat']) == "REPUESTOS PROPIOS"){?>
                                            <option value="CONSUMIBLES" >CONSUMIBLES</option>
                                            <option value="EPPS">EPPS</option>
                                            <option value="MATERIALES INSTA. PERMANENTE">MATERIALES INSTA. PERMANENTE</option>
                                            <option value="MATERIALES EQUI. UTILES DE OFICINA">MATERIALES EQUI. UTILES DE OFICINA</option>
                                            <option value="REPUESTOS PROPIOS" selected>REPUESTOS PROPIOS</option>
                                            <option value="REPUESTOS TERCEROS">REPUESTOS TERCEROS</option>
                                            <?php
                                        }
                                        else if(trim($dtlleMaterial['clasificacion_mat']) == "REPUESTOS TERCEROS"){?>
                                            <option value="CONSUMIBLES" >CONSUMIBLES</option>
                                            <option value="EPPS">EPPS</option>
                                            <option value="MATERIALES INSTA. PERMANENTE">MATERIALES INSTA. PERMANENTE</option>
                                            <option value="MATERIALES EQUI. UTILES DE OFICINA">MATERIALES EQUI. UTILES DE OFICINA</option>
                                            <option value="REPUESTOS PROPIOS">REPUESTOS PROPIOS</option>
                                            <option value="REPUESTOS TERCEROS" selected>REPUESTOS TERCEROS</option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <input type="hidden" name="codigo_i" id="codigo_i" value="<?=$dtlleMaterial['cod_mat']?>">
                                <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Código
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-3">
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control input-md text-left" disabled
                                               placeholder="código" id="cod_temp" value="<?=$dtlleMaterial['cod_mat']?>">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="btnChangeCodMate"
                                                    data-idalm="<?=$idAlmacen?>"  data-idmat="<?=$idMaterial?>" data-codmat="<?=$dtlleMaterial['cod_mat']?>">
                                                <i class="ti-pencil"></i>
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Descripción
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control input-md text-left"
                                           name="des_i" maxlength="150" required
                                           placeholder="describa el material" value="<?=$dtlleMaterial['des_mat']?>">
                                    <small class="form-text text-muted">Máximo 150 carácteres</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Unidad medida
                                    <span class="text-danger font-weight-bold">*</span>
                                </label>
                                <div class="col-sm-3">
                                    <select name="um_i" class="form-control classSelect" required>
                                        <option value="">Seleccione...</option>
                                        <?php
                                        if(!is_null($lstUM)){
                                            foreach ($lstUM as $unidadM){
                                                if(trim($unidadM['cod_um']) == trim(mb_strtoupper($dtlleMaterial['um_mat'],"UTF-8"))){?>
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
                                <label for="ubicacion_i" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Ubicación
                                </label>
                                <div class="col-sm-3">
                                    <input type="text" class="form-control input-md text-left"
                                           name="ubicacion_i" placeholder="ingrese valor" maxlength="12"
                                           value="<?=$dtlleMaterial['ubica_mat']?>">
                                </div>
                            </div>
                            <div class="row">
                                <label for="titulo" class="col-sm-4 col-form-label text-lg-right text-md-right text-left">
                                    Frecuencia renovación (Meses)
                                </label>
                                <div class="col-sm-3">
                                    <input type="text" class="form-control input-md text-left"
                                           name="frec_i" placeholder="ingrese valor"
                                           onkeyup="if(!Number(this.value)){this.value = ''; }"
                                           value="<?=$dtlleMaterial['frecrenova_mat']?>">
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

    public function loadCampos_newCodMaterial(){
        try {
            $idalm = (int)$_GET['idalm'];
            $idmat = (int)$_GET['idmat'];
            $codmat = trim($_GET['codmat']);?>
            <div class="form-group row">
                <div class="col-sm-6 offset-xl-3 offset-lg-3 offset-md-3">
                    <div class="card card-shadow card-body">
                        <form id="formValidate_Codigo" role="form" method="post" style="display: flex">
                            <input type="hidden" name="this_idAlm" value="<?=$idalm?>">
                            <input type="hidden" name="this_idMat" value="<?=$idmat?>">
                            <input type="hidden" name="this_codMate" value="<?=$codmat?>">
                            <input type="text" class="form-control mb-2 mr-2 text-center" name="newcodmat_i" id="newcodmat_i"
                                   placeholder="ingrese nuevo código" required maxlength="12" autocomplete="off"
                                   onchange="sga.funcion.valida_sinEspacios(event)">
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
            $datSearch[1] = (int)$_POST['this_idMat'];
            $datSearch[2] = trim($_POST['newcodmat_i']);
            $obj_mat = new MaterialModel();
            $searching = $obj_mat->busca_existencia_codMaterial_xAlmacen($datSearch);
            if(is_null($searching)){?>
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
                                            Agregar
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
                                <input type="hidden" name="this_idAlm" value="<?=$datSearch[0]?>">
                                <input type="hidden" name="this_idMat" value="<?=$datSearch[1]?>">
                                <input type="hidden" name="this_codMate" value="<?=$datSearch[2]?>">
                                <input type="text" class="form-control mb-2 mr-2 text-center" name="newcodmat_i" id="newcodmat_i"
                                       placeholder="ingrese nuevo código" required maxlength="12" autocomplete="off"
                                       onchange="sga.funcion.valida_sinEspacios(event)">
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

    public function actualizar_Material_JSON(){
        try {
            $Frecuencia = (int)$_POST['frec_i'];
            $renovacion = 0;
            if($Frecuencia > 0){ $renovacion = 1; }

            $datesChange[0] = $_POST['idmat_i'];
            $datesChange[1] = $_POST['clasifica_i'];
            $datesChange[2] = $_POST['codigo_i'];
            $datesChange[3] = $_POST['des_i'];
            $datesChange[4] = $_POST['um_i'];
            $datesChange[5] = $renovacion;
            $datesChange[6] = $Frecuencia;
            $datesChange[7] = $_POST['ubicacion_i'];
            $obj_mat = new MaterialModel();
            $updateItem = $obj_mat->update_Material_xID($datesChange);
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

    public function loadCampos_loadMaterial(){
        try {
            $idAlmacen = (int)$_GET['idalm'];
            $obj_alm = new AlmacenModel();
            $dtlleAlmacen = $obj_alm->detalle_Almacen_xID($idAlmacen);?>
            <div class="container">
                <div class="page-title">
                    <h4 class="mb-0 text-info text-center">
                        Cargar/Actualizar Nuevos Materiales
                    </h4>
                    <ol class="breadcrumb mb-0 pl-0 pt-1 pb-0 text-center" style="display: block">
                        <li class="breadcrumb-item text-muted">Registre uno o varios materiales al almacén seleccionado, primero descargue la plantilla, copie los equipos a cargar y listo.</li>
                    </ol>
                </div>
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title"><?=$dtlleAlmacen['titulo_alm']?></h5>
                        <div class="row">
                            <div class="col-12 text-center" id="divMsg_iFile"></div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <form id="fmrLoad_DataMaterial" enctype="multipart/form-data">
                                    <input type="hidden" id="iAlm" value="<?=$idAlmacen?>">
                                    <input type="file" class="file" id="file_data" name="file_data" required
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
                        <button type="button" id="btnCancel_Load" class="btn btn-light mr-10 btnDisabled">
                            Cancelar
                        </button>
                        <a type="button" class="btn bg-green-600 btn-md btn-hover-transform text-hover-white btnDisabled"
                           href="../assets/formato/Plantilla-material.xlsx">
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

    public function load_File_Material(){
        try {
            date_default_timezone_set("America/Lima");
            setlocale(LC_TIME, 'es_PE.UTF-8');
            $idAlmacen = (int)$_POST['idalm'];
            $filename = $_FILES['file_data']['tmp_name'];

            $array = explode('.', $_FILES['file_data']['name']);
            $extension = end($array);
            $readerType = null;
            $type = 0; // error definido
            $mensaje = "El archivo adjunto no tiene un formato valido.";
            $datosError = array();
            if(trim($extension) == 'xlsx'){ $readerType = 'Xlsx'; }
            else if(trim($extension) == 'xls'){ $readerType = 'Xls'; }

            $successLoad = 0;
            $datosArray = array();
            if(!is_null($readerType)) {
                $reader = IOFactory::createReader($readerType);
                $spreadsheet = $reader->load($filename);
                $sheetCount = $spreadsheet->getSheetCount();
                date_default_timezone_set('America/New_York');

                if ($sheetCount == 1 || $sheetCount == 2 ) {
                    $worksheet = $spreadsheet->setActiveSheetIndex(0);
                    $data = $worksheet->toArray();
                    $arreglo = array();
                    for ($row = 1; $row <= sizeof($data); $row++) {
                        unset($arreglo);
                        $columnEmpty = 0;
                        for ($t = 0; $t <= 5; $t++) {
                            if(trim($data[$row][$t]) != null || trim($data[$row][$t]) != ""){
                                $arreglo[] = trim($data[$row][$t]);
                            }
                            else{
                                $arreglo[] = "";
                                $columnEmpty++;
                            }
                        }

                        //verificamos cuantos valores nulos tiene
                        if($columnEmpty == 0){
                            array_push($datosArray, $arreglo);
                        }
                    }
                }
                else if ($sheetCount > 2) {
                    $type = 0; // archivo con muchas hojas
                    $mensaje = "El archivo adjunto contiene varias hojas adjuntas, solo se admite el formato según la plantilla requerida.";
                }
            }

            $obj_mat = new MaterialModel();
            $obj_um = new UnidadMModel();
            if(sizeof($datosArray) > 0){
                for ($i = 0; $i < sizeof($datosArray); $i++) {
                    $CampoNoCoyncid = 0;
                    if (trim($datosArray[$i][0]) != "EPPS" && trim($datosArray[$i][0]) != "CONSUMIBLES" &&
                        trim($datosArray[$i][0]) != "MATERIALES INSTA. PERMANENTE" && trim($datosArray[$i][0]) != "MATERIALES EQUI. UTILES DE OFICINA" &&
                        trim($datosArray[$i][0]) != "REPUESTOS PROPIOS" && trim($datosArray[$i][0]) != "REPUESTOS TERCEROS") { $CampoNoCoyncid++; }
                    $datesSearch[0] = $idAlmacen;
                    $datesSearch[1] = trim($datosArray[$i][1]);
                    $existeCodigo = $obj_mat->busca_codMaterial_xAlmacen($datesSearch);
                    if (!is_null($existeCodigo)) { $CampoNoCoyncid++; }
                    $existeUM = $obj_um->busca_existenciaUM_xCodigo(mb_strtoupper(trim($datosArray[$i][3]),"UTF-8"));
                    if (is_null($existeUM)) { $CampoNoCoyncid++; }
                    if ((int)$datosArray[$i][4] == 1 && (int)$datosArray[$i][5] == 0) { $CampoNoCoyncid++; }

                    if ($CampoNoCoyncid == 0) {
                        $datesReg[0] = $idAlmacen;
                        $datesReg[1] = $datosArray[$i][0];
                        $datesReg[2] = $datosArray[$i][1];
                        $datesReg[3] = $datosArray[$i][2];
                        $datesReg[4] = $datosArray[$i][3];
                        $datesReg[5] = $datosArray[$i][4];
                        $datesReg[6] = $datosArray[$i][5];
                        $insertMat = $obj_mat->registrar_Material($datesReg);
                        if ($insertMat) { $successLoad++; }
                        else {
                            array_push($datosError, $datosArray[$i]);
                        }
                    }
                    else {
                        array_push($datosError, $datosArray[$i]);
                    }
                }

                if ($successLoad > 0 &&  count($datosError) == 0) {
                    $type = 1; //correcto
                    $mensaje = "Materiales cargados correctamente.";
                }
                else if ($successLoad > 0 &&  count($datosError) > 0) {
                    $type = 2; //archivo vacio
                    $mensaje = "Materiales fueron cargados correctamente, pero con algunos errores.";
                }
                else if ($successLoad == 0 &&  count($datosError) > 0) {
                    $type = 3; //archivo vacio
                    $mensaje = "Error al cargar, los materiales";
                }
            }

            $archivo = "";
            if(sizeof($datosError)>0){
                //generamos el excel a descargar de errores
                /****** ESTILOS ******/
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
                $sty_leyenda = [
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
                        'color' => ['argb' => '305496']
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
                    65  =>  'A', 66  =>  'B', 67  =>  'C', 68  =>  'D', 69  =>  'E', 70  =>  'F', 71  =>  'G', 72  =>  'H',
                    73  =>  'I', 74  =>  'J', 75  =>  'K', 76  =>  'L', 77  =>  'M', 78  =>  'N', 79  =>  'O', 80  =>  'P',
                    81  =>  'Q', 82  =>  'R', 83  =>  'S', 84  =>  'T', 85  =>  'U', 86  =>  'V', 87  =>  'W', 88  =>  'X',
                    89  =>  'Y', 90  =>  'Z',
                );
                $titulos = array(
                    0 => "CLASIFICACION",
                    1 => "CODIGO",
                    2 => "DESCRIPCION",
                    3 => "UNIDAD MEDIDA",
                    4 => "RENOVACION\n1->SI 0->NO",
                    5 => "FRECUENCIA"
                );

                //creamos el libro de trabajo
                $spreadsheet = new Spreadsheet();

                /************************* VALORES **********************************/
                $spreadsheet->createSheet(0);
                $hoja1 = new Worksheet($spreadsheet, 'Materiales');
                $spreadsheet->addSheet($hoja1,0);
                $sheet0 = $spreadsheet->setActiveSheetIndex(0);

                for ($col = 0; $col < sizeof($titulos); $col++) {
                    $sheet0->setCellValue($letras[$col+65] . '1', $titulos[$col]);
                    $sheet0->getStyle($letras[$col+65] . '1')->applyFromArray($sty_title);
                    $sheet0->getStyle($letras[$col+65] . '1')->getAlignment()->setWrapText(true);
                }

                //Writer error item
                $lineError = 2;
                for($i = 0; $i < sizeof($datosError); $i++){
                    for($j=0; $j <sizeof($datosError[$i])-1; $j++){
                        if($j==2){
                            $sheet0->setCellValue($letras[65+$j].$lineError, trim($datosError[$i][$j]));
                            $sheet0->getStyle($letras[65+$j].$lineError)->applyFromArray($sty_text_left);
                        }
                        else if($j==4 || $j==5){
                            $sheet0->setCellValue($letras[65+$j].$lineError, (int)$datosError[$i][$j]);
                            $sheet0->getStyle($letras[65+$j].$lineError)->applyFromArray($sty_text_center);
                        }
                        else {
                            $sheet0->setCellValue($letras[65+$j].$lineError, trim($datosError[$i][$j]));
                            $sheet0->getStyle($letras[65+$j].$lineError)->applyFromArray($sty_text_center);
                        }
                    }
                    $sheet0->getRowDimension($lineError)->setRowHeight(15);
                    $lineError++;
                }

                $sheet0->getColumnDimension('A')->setWidth(16);
                $sheet0->getColumnDimension('B')->setWidth(13);
                $sheet0->getColumnDimension('C')->setWidth(50);
                $sheet0->getColumnDimension('D')->setWidth(18);
                $sheet0->getColumnDimension('E')->setWidth(15);
                $sheet0->getColumnDimension('F')->setWidth(15);
                $sheet0->getSheetView()->setZoomScale(100);

                /************************* LEYENDA ********************************/
                $spreadsheet->createSheet(1);
                $hoja2 = new Worksheet($spreadsheet, 'Leyenda');
                $spreadsheet->addSheet($hoja2, 1);
                $sheet1 = $spreadsheet->setActiveSheetIndex(1);

                $sheet1->mergeCells('A1:B1');
                $sheet1->setCellValue('A1', "UNIDAD DE MEDIDA");
                $sheet1->getStyle('A1:B1')->applyFromArray($sty_leyenda);
                $sheet1->setCellValue('A2', "CODIGO");
                $sheet1->getStyle('A2')->applyFromArray($sty_leyenda);
                $sheet1->setCellValue('B2', "DESCRIPCION");
                $sheet1->getStyle('B2')->applyFromArray($sty_leyenda);
                $obj_ume = new UnidadMModel();
                $lstUM = $obj_ume->listar_unidadM_All();
                if(!is_null($lstUM)){
                    $lineLey = 3;
                    foreach ($lstUM as $unidadM){
                        $sheet1->setCellValue('A'.$lineLey, $unidadM['cod_um']);
                        $sheet1->getStyle('A'.$lineLey)->applyFromArray($sty_text_center);
                        $sheet1->setCellValue('B'.$lineLey, $unidadM['des_um']);
                        $sheet1->getStyle('B'.$lineLey)->applyFromArray($sty_text_left);
                        $sheet1->getRowDimension($lineLey)->setRowHeight(15);
                        $lineLey++;
                    }
                }

                $sheet1->setCellValue('D1', "CLASIFICACION");
                $sheet1->getStyle('D1')->applyFromArray($sty_leyenda);
                $sheet1->setCellValue('D2', "EPPS");
                $sheet1->getStyle('D2')->applyFromArray($sty_text_center);
                $sheet1->setCellValue('D3', "CONSUMIBLES");
                $sheet1->getStyle('D3')->applyFromArray($sty_text_center);
                $sheet1->setCellValue('D4', "MATERIALES INSTA. PERMANENTE");
                $sheet1->getStyle('D4')->applyFromArray($sty_text_center);
                $sheet1->setCellValue('D5', "MATERIALES EQUI. UTILES DE OFICINA");
                $sheet1->getStyle('D5')->applyFromArray($sty_text_center);
                $sheet1->setCellValue('D6', "REPUESTOS PROPIOS");
                $sheet1->getStyle('D6')->applyFromArray($sty_text_center);
                $sheet1->setCellValue('D7', "REPUESTOS TERCEROS");
                $sheet1->getStyle('D7')->applyFromArray($sty_text_center);

                $sheet1->getColumnDimension('A')->setWidth(10);
                $sheet1->getColumnDimension('B')->setWidth(16);
                $sheet1->getColumnDimension('C')->setWidth(11);
                $sheet1->getColumnDimension('D')->setWidth(23);
                $sheet1->getSheetView()->setZoomScale(100);
                /************************* ********************************/
                $spreadsheet->setActiveSheetIndex(0);

                $sheetIndex2 = $spreadsheet->getIndex($spreadsheet->getSheetByName('Worksheet 2'));
                $spreadsheet->removeSheetByIndex($sheetIndex2);
                $sheetIndex1 = $spreadsheet->getIndex($spreadsheet->getSheetByName('Worksheet 1'));
                $spreadsheet->removeSheetByIndex($sheetIndex1);
                $sheetIndex = $spreadsheet->getIndex($spreadsheet->getSheetByName('Worksheet'));
                $spreadsheet->removeSheetByIndex($sheetIndex);

                $ruta = "../assets/error-file/";
                $archivo = md5("fileError-".$idAlmacen . date("d-m-Y H:i:s")) . ".xlsx";

                $writer = new Xlsx($spreadsheet);
                $writer->save($ruta.$archivo);
            }

            $response = array(
                'status'=> $type,
                'message'=> $mensaje,
                'dataError'=> $datosError,
                'succesLoad'=>$successLoad,
                'file'=>$archivo,
                'readerType'=>$readerType
            );

            echo json_encode($response);

        }
        catch (PDOException $e) {
            throw $e;
        }
        catch (Exception $e) {
        }
    }

    public function generatedCodeBar_Material(){
        try {
            $codigo = trim($_GET['codigo']);
            $descrip = trim($_GET['descrip']);
            $unidadM = trim($_GET['unidadM']);

            $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
            $code2 ='';
            foreach(str_split($codigo) as $key => $c){
                $code2 .=$c;
                if(count(str_split($codigo)) != $key)
                    $code2 .=' ';

            }?>
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-info text-center" style="display: block">
                        <h6 class="modal-title text-white ">
                            Código de Barra Generado
                        </h6>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 text-center" id="displayCB">
                                <div id="fieldCB" class="text-center">
                                    <center>
                                        <large>
                                        <b id="title"><?=$unidadM." - ".$descrip ?></b>
                                        </large>
                                    </center>
                                    <img id="imgPrint" src="data:image/png;base64,<?php echo base64_encode($generator->getBarcode($codigo, 'C128')) ?>">
                                    <div id="codeCB"><?php echo $code2 ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer text-center" style="display: block">
                        <button type="button" class="btn btn-success btn-hover-transform" id="printCodebar">
                            <i class="icon-printer position-left"></i>
                            Imprimir
                        </button>
                        <button type="button" class="btn btn-primary btn-hover-transform" id="downloadCodebar" data-code="<?=$codigo?>">
                            <i class="icon-cloud-download position-left"></i>
                            Descargar
                        </button>
                        <button type="button" class="btn btn-danger btn-hover-transform" id="pdfCreatorCodebar"
                                data-code="<?=$codigo?>" data-des="<?=$descrip?>" data-um="<?=$unidadM?>">
                            <i class="fa fa-file-pdf-o position-left"></i>
                            Generar PDF
                        </button>
                        <button type="button" class="btn btn-default mr-10" data-dismiss="modal">
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

    public function enviarEmail_alertAnulaDespacho($idDespacho, $idUsuario){
        try {
            date_default_timezone_set("America/Lima");
            setlocale(LC_TIME, 'es_PE.UTF-8');
            $obj_per = new PersonaModel();
            $dtllePersona = $obj_per->detalle_Persona_xIDUsuario($idUsuario);
            $obj_fn = new FuncionesModel();
            $obj_dsp = new MaterialModel();
            $dtlleDespacho = $obj_dsp->detalle_Despacho_xID($idDespacho);
            $fechaHoraDespacho = "--/--/-- --:--";
            $ServicioAlmacen = "--/--";
            $codTransac = "--";
            $motivoAnula = "";
            if(!is_null($dtlleDespacho)){
                $fechaHoraDespacho = $obj_fn->fecha_ENG_ESP($dtlleDespacho['fecha_des'])." ".$dtlleDespacho['hora_des'];
                $ServicioAlmacen = $dtlleDespacho['desserv_des']."/".$dtlleDespacho['desalm_des'];
                $codTransac = $dtlleDespacho['codtransac_des'];
                $motivoAnula = $dtlleDespacho['motivoanula_des'];
            }

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
            $mail->Subject = "ALERTA DESPACHO ANULADO";
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
            $courp .='<select>';
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
            $courp .='                           <span style="font-size:30px;font-family:Helvetica,Arial,sans-serif;font-weight:100;color:#515151"><b>CONTROL DE EPPS Y MATERIALES</b></span>';
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
            $courp .=                                        ucwords(strtolower($dtllePersona['nombres_per'])) .' ' .ucwords(strtolower($dtllePersona['ape_pa_per'])).' ' .ucwords(strtolower($dtllePersona['ape_ma_per']));
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
            $courp .='                                        Se emite la siguiente alerta para indicar que se a realizado la anulación del despacho según el siguiente detalle:</span></td>';
            $courp .='                                 </tr>';
            $courp .='                                  <tr>';
            $courp .='                                   <td style="background:#f7f7f7;padding-top:10px;padding-bottom:10px ">';
            $courp .='									   <table border="0" align="center" style="background:#f7f7f7;width:100%;max-width:600px;font-size:14px;font-family:Helvetica,Arial,sans-serif;color:#797979">';
            $courp .='                                     <tbody>';
            $courp .='                                       <tr>';
            $courp .='                                         <td height="36" align="right" style="width:40%">Código Transacción : </td>';
            $courp .='                                         <td style="width:60%" align="left">';
            $courp .='											 <b style="font-size:20px;font-family:Helvetica,Arial,sans-serif;font-weight:100;color:#002b4d">'. $codTransac .'</b>';
            $courp .='										   </td>';
            $courp .='                                       </tr>';
            $courp .='                                       <tr>';
            $courp .='                                         <td height="36" align="right" style="width:40%">Servicio/Almacén : </td>';
            $courp .='                                         <td style="width:60%" align="left">';
            $courp .='											 <b style="font-size:20px;font-family:Helvetica,Arial,sans-serif;font-weight:100;color:#002b4d">'. $ServicioAlmacen .'</b>';
            $courp .='										   </td>';
            $courp .='                                       </tr>';
            $courp .='                                       <tr>';
            $courp .='                                         <td height="36" align="right" style="width:40%">Fecha/Hora Despacho : </td>';
            $courp .='                                         <td style="width:60%" align="left">';
            $courp .='											 <b style="font-size:20px;font-family:Helvetica,Arial,sans-serif;font-weight:100;color:#002b4d">'. $fechaHoraDespacho .'</b>';
            $courp .='										   </td>';
            $courp .='                                       </tr>';
            $courp .='                                       <tr>';
            $courp .='                                          <td height="36" align="right" style="width:40%">Motivo anulación : </td>';
            $courp .='                                          <td style="width:60%" align="left">';
            $courp .='											  <b style="font-size:20px;font-family:Helvetica,Arial,sans-serif;font-weight:100;color:#002b4d">'. $motivoAnula .'</b>';
            $courp .='										    </td>';
            $courp .='                                       </tr>';
            $courp .='                                     </tbody>';
            $courp .='                                   </table></td>';
            $courp .='                                 </tr>';
            $courp .='                                <tr>';
            $courp .='                                    <td align="center" style="padding:0 0 20px 0;line-height:12px;font-family:Helvetica,Arial,sans-serif;font-size:14px;text-align:center;color:#797979">';
            $courp .='                                       <span style="font-size:12px;font-family:Helvetica,Arial,sans-serif;color:#797979">';
            $courp .='                                            Por tanto, se pone en conocimiento para los fines que se estime conveniente.';
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
            $mail->AddAddress (strtolower($dtllePersona['email_per']), ($dtllePersona['ape_pa_per']." ".$dtllePersona['ape_ma_per']." ".$dtllePersona['nombres_per']));
            $mail->AddCC("fernando.macedo@confipetrol.pe", "Macedo, Fernando Aquiles");
            $exito = $mail->Send();

            $val = 0;
            if($exito) {  $val = 1; }

            return $val;

        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }
}

