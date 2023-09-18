<?php
include('header.php');
require_once '../assets/util/Session.php';
require_once '../controller/ControlSesion.php';
require_once '../model/AlmacenModel.php';
require_once '../model/GerenciaModel.php';
require_once '../model/ServicioModel.php';
require_once '../model/PerfilModel.php';
$obj_serv = new ServicioModel();
$obj_alm = new AlmacenModel();
$lstSev = $obj_serv->lst_servicios_Asignados_Activos_xIDUS($user['id_us']);
$nameAlmacen = "Inventario";
if(sizeof($lstSev) == 1) {
    $lstAlmacenes = $obj_alm->lst_Almacenes_Asignados_xUsuario($lstSev[0]['id_su']);
    if(is_array($lstAlmacenes)) {
        if (sizeof($lstAlmacenes) == 1) {
            $nameAlmacen = $lstAlmacenes[0]['titulo_alm'];
        }
    }
}
$obj_pf = new PerfilModel();
$dtllePerfil = $obj_pf->detalle_Perfil_xID($user['perfil']);
?>
<input type="hidden" id="acc_edit" value="<?=$dtllePerfil['editar_perfil']?>">
<input type="hidden" id="acc_del" value="<?=$dtllePerfil['eliminar_perfil']?>">
<input type="hidden" id="acc_import" value="<?=$dtllePerfil['importar_perfil']?>">

<input type="hidden" id="inv_new" value="<?=$dtllePerfil['nuevo_perfil']?>">
<input type="hidden" id="inv_trans" value="<?=$dtllePerfil['transferir_perfil']?>">
<input type="hidden" id="inv_reti" value="<?=$dtllePerfil['retirar_perfil']?>">
<input type="hidden" id="inv_devol" value="<?=$dtllePerfil['devolver_perfil']?>">
<div class="container-fluid" id="divTabla">
    <div class="page-title pl-0 pr-0 pb-10">
        <h4 class="mb-0">
            <span id="txtAlmacen"><?=$nameAlmacen?></span>
        </h4>
        <ol class="breadcrumb mb-0 pl-0 pt-1 pb-0">
            <li class="breadcrumb-item text-muted">Agregue o actualize los datos del almacén.</li>
        </ol>
    </div>
    <input type="hidden" id="count_servicio" value="<?= sizeof($lstSev) ?>">
    <?php
    if(sizeof($lstSev) == 1) {?>
        <input type="hidden" id="IdServicioUsuario" value="<?= $lstSev[0]['id_su'] ?>">
        <?php
        $lstAlmacenes = $obj_alm->lst_Almacenes_Asignados_xUsuario($lstSev[0]['id_su']);?>
        <input type="hidden" id="count_almacen" value="<?= sizeof($lstAlmacenes) ?>">
        <?php
        if(is_array($lstAlmacenes)){
            if(sizeof($lstAlmacenes) == 1){?>
                <input type="hidden" id="IdAlmacen" value="<?= $lstAlmacenes[0]['id_alm'] ?>">
                <input type="hidden" id="textAlmacen" value="<?= $nameAlmacen?>">
                <?php
            }
            else{?>
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-10">
                        <select name="IdAlmacen" id="IdAlmacen" class="form-control selectClass" data-placeholder="Almacén...">
                            <option></option>
                            <?php
                            foreach ($lstAlmacenes as $almacen){?>
                                <option value="<?=$almacen['id_alm']?>" data-vista="<?=$almacen['vista_alm']?>"><?=$almacen['titulo_alm']?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <?php
            }
        }
    }
    else{
        $obj_ge = new GerenciaModel();
        $lstGerencias = $obj_ge->lst_Gerencia_Activas();?>
        <input type="hidden" id="count_almacen" value="0">
        <div class="row">
            <div class="col-lg-7 col-md-6 col-sm-12 col-xs-12 mb-10">
                <select id="IdServicioUsuario" class="form-control input-md selectClass">
                    <option value="">Servicio...</option>
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
            <div class="col-lg-5 col-md-6 col-sm-12 col-xs-12 mb-10">
                <select id="IdAlmacen" class="form-control selectClass" data-placeholder="Almacén..." disabled>
                    <option></option>
                </select>
            </div>
        </div>
        <?php
    }
    ?>
    <div class="row" id="divOption_btns"></div>
    <div id="divLoad_import"></div>
    <div id="divContend"></div>
</div>
<div class="container-fluid" id="divResponse"></div>
<div class="scrollToTop"><i class="fa fa-arrow-up"></i></div>

<div class="loader-custom-container">
    <span class="loader-custom"></span>
</div>

<?php
include('footer.php');
?>
<link href="../assets/css/buttons.dataTables.css<?=$version?>" rel="stylesheet" />
<!-- Formatter -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/formatter.js/0.1.5/jquery.formatter.min.js"></script>
<!-- Inputfiles Bootstrap-->
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.0.1/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.0.1/js/fileinput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.0.1/js/locales/es.min.js"></script>
<!-- Calendar  flatpickr -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/2.4.8/flatpickr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/2.4.8/l10n/es.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/2.4.8/flatpickr.min.css"/>
<!-- html2pdf -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
<!-- Bootstrap  Table -->
<link href="https://unpkg.com/bootstrap-table@1.18.1/dist/bootstrap-table.min.css" rel="stylesheet">
<script src="https://unpkg.com/bootstrap-table@1.18.1/dist/bootstrap-table.min.js"></script>
<script src="https://unpkg.com/bootstrap-table@1.18.1/dist/bootstrap-table-locale-all.min.js"></script>
<!--toastr-->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css<?=$version?>" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>
<script src="https://cdn.sheetjs.com/xlsx-0.20.0/package/dist/xlsx.full.min.js"></script>
<script src="../assets/ajax/inventario.js<?=$version?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', ()=>{
        $('.loader-custom-container').hide();
    });
</script>
<style>
    .loader-custom-container{
        width: 100vw;
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        z-index: 10000;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: rgba(0,0,0,.25);
    }

    .loader-custom {
        width: 120px;
        height: 120px;
        border: 12px solid #FFF;
        border-bottom-color: #FF3D00;
        border-radius: 50%;
        display: inline-block;
        box-sizing: border-box;
        animation: rotation 1s linear infinite;
    }

    @keyframes rotation {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
    } 
</style>