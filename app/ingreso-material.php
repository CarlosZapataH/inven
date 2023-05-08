<?php
include('header.php');
require_once '../assets/util/Session.php';
require_once '../controller/ControlSesion.php';
require_once '../model/GerenciaModel.php';
require_once '../model/ServicioModel.php';
require_once '../model/AlmacenModel.php';
require_once '../model/PerfilModel.php';
$obj_serv = new ServicioModel();
$lstSev = $obj_serv->lst_servicios_Asignados_Activos_xIDUS($user['id_us']);

$obj_pf = new PerfilModel();
$dtllePerfil = $obj_pf->detalle_Perfil_xID($user['perfil']);
$obj_alm = new AlmacenModel();?>
<style id="stylePrint">
    div#displayCB {
        display: flex;
        height: 100%;
        width: 100%;
        align-items: center;
    }
    @media print{
        div#displayCB {
            display: flex;
            height: auto;
            width: 100%;
            align-items: center;
        }
    }
    #displayCB #fieldCB,#displayCB large {
        margin: auto;
    }
    #fieldCB img{
        height: 22vh;
        max-width: 100%;
    }
    div#codeCB {
        font-weight: 700;
        font-size: 64px;
        text-align: justify;
        text-align-last: justify;
        margin: 0 40px 0 40px;
    }
    b#title{
        font-size: 37px;
    }
    img#imgPrint{
        text-align: center;
        align-items: center;
        display: inline-flex;
        height: 22vh;
        max-width: 100%;
    }
</style>
<input type="hidden" id="acc_edit" value="<?=$dtllePerfil['editar_perfil']?>">
<input type="hidden" id="acc_del" value="<?=$dtllePerfil['eliminar_perfil']?>">
<div class="page-title py-10" id="divITitulo">
    <h4 class="mb-0 text-brown-800 font-weight-bold text-center ">
        Ingreso Material
    </h4>
    <p class="breadcrumb-item text-muted mb-0 text-center ">Elija una acción a realizar.</p>
    <input type="hidden" id="count_servicio" value="<?= sizeof($lstSev) ?>">
    <?php
    $lstAlmacenes = array();
    if(count($lstSev) == 1) {
        $lstAlmacenes = $obj_alm->lst_Almacenes_Asignados_xUsuario($lstSev[0]['id_su']);?>
        <input type="hidden" id="IdServicioUsuario" value="<?= $lstSev[0]['id_su'] ?>">
        <?php
    }
    else{
        $obj_ge = new GerenciaModel();
        $lstGerencias = $obj_ge->lst_Gerencia_Activas();?>
        <div class="row">
            <div class="col-xl-6 col-lg-6 col-md-8 col-sm-12 mb-10 offset-xl-3 offset-lg-3 offset-md-2">
                <select id="IdServicioUsuario" class="form-control input-md selectSearch" data-placeholder="Servicio...">
                    <option></option>
                    <?php
                    if (!is_null($lstGerencias)) {
                        $obj_serv_b = new ServicioModel();
                        foreach ($lstGerencias as $gerencia) {
                            $lstServiciosUS = $obj_serv_b->lst_Servicio_xGerencia_Usuario($gerencia['id_ge'], $user['id_us']);
                            if (!is_null($lstServiciosUS)) {?>
                                <optgroup label="<?= $gerencia['des_ge'] ?>">
                                    <?php foreach ($lstServiciosUS as $servicio) { ?>
                                        <option value="<?= $servicio['id_su'] ?>">
                                            <?= $servicio['des_serv'] ?>
                                        </option>
                                    <?php } ?>
                                </optgroup>
                            <?php }
                        }
                    }
                    ?>
                </select>
            </div>
        </div>
        <?php
    }
    ?>
</div>
<div class="container-fluid" id="divIContend"></div>
<div class="container" id="divIResponse"></div>
<div class="scrollToTop"><i class="fa fa-arrow-up"></i></div>
<?php
include('footer.php');
?>
<!-- Formatter -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/formatter.js/0.1.5/jquery.formatter.min.js"></script>
<!-- bootstrap-select-->
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css?<?=$version?>" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"></script>
<!-- Inputfiles Bootstrap-->
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.0.1/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.0.1/js/fileinput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.0.1/js/locales/es.min.js"></script>
<!--toastr-->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css<?=$version?>" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>
<!--html2canvas-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
<!--signature-->
<link href="../assets/plugins/signature/css/signature-pad.css" rel="stylesheet">
<script src="../assets/ajax/ingreso-material.js<?=$version?>"></script>