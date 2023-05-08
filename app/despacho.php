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
$nameAlmacen = "Control de EPPS ";
if(sizeof($lstSev) == 1) {
    $lstAlmacenes = $obj_alm->lst_Almacenes_Asignados_xUsuario($lstSev[0]['id_su']);
    if(is_array($lstAlmacenes)) {
        if (sizeof($lstAlmacenes) == 1) {
            $nameAlmacen = "Control de EPPS : ".$lstAlmacenes[0]['titulo_alm'];
        }
    }
}
$obj_pf = new PerfilModel();
$dtllePerfil = $obj_pf->detalle_Perfil_xID($user['perfil']);


?>
<style>

    .img{
        margin: 10px auto;
        border-radius: 5px;
        border: 1px solid #ccc;
        padding:  13px;
        width: 30%;
        background-size:  100% 100%;
    }
    .imgFinger{
        width: 97%;
    }

    .ct2{
        margin:  10px auto;
        border-radius:  5px;
        border: 1px solid #ccc;
        padding: 5px 7px;
        width: 270px;
        height:  30px;
        background-size:  100% 100%;
    }
    .dataUser{
        margin:  10px auto;
        border-radius:  5px;
        border: 1px solid #ccc;
        padding: 5px 7px;
        width: 270px;
        height: 58px;
        background-size:  100% 100%;
    }

</style>
<input type="hidden" id="acc_edit" value="<?=$dtllePerfil['editar_perfil']?>">
<input type="hidden" id="acc_del" value="<?=$dtllePerfil['eliminar_perfil']?>">
<input type="hidden" id="acc_import" value="<?=$dtllePerfil['importar_perfil']?>">

<input type="hidden" id="inv_new" value="<?=$dtllePerfil['nuevo_perfil']?>">
<input type="hidden" id="inv_trans" value="<?=$dtllePerfil['transferir_perfil']?>">
<input type="hidden" id="inv_reti" value="<?=$dtllePerfil['retirar_perfil']?>">
<input type="hidden" id="inv_devol" value="<?=$dtllePerfil['devolver_perfil']?>">
<div class="container-fluid" id="divHead">
    <div class="page-title pl-0 pr-0 pb-10">
        <h4 class="mb-0">
            <span id="txtAlmacen"><?=$nameAlmacen?></span>
        </h4>
        <ol class="breadcrumb mb-0 pl-0 pt-1 pb-0">
            <li class="breadcrumb-item text-muted">Genere ingresos y salidas de EPPS para el personal.</li>
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
                <input type="hidden" id="VistaAlmacen" value="<?= $lstAlmacenes[0]['vista_alm'] ?>">
                <?php
            }
            else{?>
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-10">
                        <input type="hidden" id="VistaAlmacen" value="0">
                        <select name="IdAlmacen" id="IdAlmacen" class="form-control selectClass" data-placeholder="Seleccione...">
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
                <select id="IdServicioUsuario" class="form-control input-md selectSearch" data-placeholder="Servicio">
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
            <div class="col-lg-5 col-md-6 col-sm-12 col-xs-12 mb-10">
                <select id="IdAlmacen" class="form-control selectClass" data-placeholder="Almacen..." disabled>
                    <option></option>
                </select>
            </div>
        </div>
        <?php
    }
    ?>
</div>
<div class="container" id="divSearching"></div>
<div class="container-fluid pt-30" id="divResponse"></div>


<div class="scrollToTop"><i class="fa fa-arrow-up"></i></div>
<?php
include('footer.php');
?>
<!-- Formatter -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/formatter.js/0.1.5/jquery.formatter.min.js"></script>
<!-- html2pdf -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
<!-- Bootstrap  Table -->
<link href="https://unpkg.com/bootstrap-table@1.18.1/dist/bootstrap-table.min.css" rel="stylesheet">
<script src="https://unpkg.com/bootstrap-table@1.18.1/dist/bootstrap-table.min.js"></script>
<script src="https://unpkg.com/bootstrap-table@1.18.1/dist/bootstrap-table-locale-all.min.js"></script>
<!--Timer-->

<!--toastr-->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css<?=$version?>" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>
<script src="../assets/ajax/despacho.js<?=$version?>"></script>