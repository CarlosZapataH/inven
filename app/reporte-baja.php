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
?>
<div class="container-fluid" id="divTabla">
    <div class="page-title pl-0 pr-0 pb-10">
        <h4 class="mb-0">
            Reporte Bajas
        </h4>
        <ol class="breadcrumb mb-0 pl-0 pt-1 pb-0">
            <li class="breadcrumb-item text-muted">Visualice las bajas realizadas en un determinado Almacén.</li>
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
                <div class="input-group">
                    <div class="input-group-append">
                        <span class="input-group-text bg-slate-800">Servicio</span>
                    </div>
                    <select id="IdServicioUsuario" class="form-control input-md">
                        <option value="">Seleccione...</option>
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
            <div class="col-lg-5 col-md-6 col-sm-12 col-xs-12 mb-10">
                <select id="IdAlmacen" class="form-control selectClass" data-placeholder="Seleccione..." disabled>
                    <option></option>
                </select>
            </div>
        </div>
        <?php
    }
    ?>
</div>
<div class="container-fluid" id="divResponse"></div>
<div class="scrollToTop"><i class="fa fa-arrow-up"></i></div>
<?php
include('footer.php');
?>
<!-- PDFObject -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfobject/2.1.1/pdfobject.min.js"></script>

<script src="../assets/ajax/bajas.js<?=$version?>"></script>