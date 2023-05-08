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
<div class="container" id="divTabla">
    <div class="page-title pb-10 text-center">
        <h4 class="mb-10">
            Reporte de Depreciación de Activos
        </h4>
        <p class="breadcrumb-item text-muted ">Obtenga el valor de depreciación de los activos de un determinado servicio.</p>
    </div>
    <div class="card mb-20 card-shadow">
        <div class="card-header text-white bg-success border-0">
            <div class="media">
                <div class="media-body text-center">
                    <p class="fz-25 mb-0"><strong class="">REPORTE</strong></p>
                    <p>Elija un servicio y seleccione el almacén del cual se obtendra el reporte, para ello dele click en el icono para generar el reporte.</p>
                </div>
            </div>
        </div>
        <div class="card-body">
            <input type="hidden" id="count_servicio" value="<?= sizeof($lstSev) ?>">
            <?php
            if(sizeof($lstSev) == 1) {
                $dtlleServicio = $obj_serv->detalle_Servicio_xID($lstSev[0]['id_serv']);?>
                <input type="hidden" id="IdServicioUsuario" value="<?= $lstSev[0]['id_su'] ?>">
                <div class="row form-group">
                    <div class="col-12">
                        <input type="text" class="form-control" value="<?= $dtlleServicio['des_serv'] ?>" readonly>
                    </div>
                </div>
                <?php
                $lstAlmacenes = $obj_alm->lst_Almacenes_Asignados_xUsuario($lstSev[0]['id_su']);?>
                <input type="hidden" id="count_almacen" value="<?= sizeof($lstAlmacenes) ?>">
                <?php
                if(is_array($lstAlmacenes)){?>
                    <div class="row form-group">
                        <div class="col-12">
                            <?php
                            if(sizeof($lstAlmacenes) == 1){?>
                                <input type="hidden" id="IdAlmacen" value="<?= $lstAlmacenes[0]['id_alm'] ?>">
                                <input type="text" class="form-control" value="<?= $lstAlmacenes[0]['titulo_alm'] ?>" readonly>
                                <?php
                            }
                            else{?>
                                <select name="IdAlmacen" id="IdAlmacen" class="form-control selectClass" data-placeholder="Seleccione...">
                                    <option></option>
                                    <?php
                                    foreach ($lstAlmacenes as $almacen){?>
                                        <option value="<?=$almacen['id_alm']?>" data-vista="<?=$almacen['vista_alm']?>"><?=$almacen['titulo_alm']?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                }
            }
            else{
                $obj_ge = new GerenciaModel();
                $lstGerencias = $obj_ge->lst_Gerencia_Activas();?>
                <input type="hidden" id="count_almacen" value="0">
                <div class="row form-group">
                    <div class="col-12">
                        <label class="text-muted mb-0">Servicio</label>
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
                <div class="row form-group">
                    <div class="col-12">
                        <label class="text-muted mb-0">Almacén</label>
                        <select id="IdAlmacen" class="form-control selectClass" data-placeholder="Seleccione..." disabled>
                            <option></option>
                        </select>
                    </div>
                </div>
                <?php
            }
            ?>
            <div class="row">
                <div class="col-12" id="mensaje_action_dp"></div>
            </div>
        </div>
        <div class="card-footer text-center bg-white p-4">
            <div class="row">
                <div class="col-12">
                    <div class="quick-links-grid">
                        <a class="ql-grid-item" id="btnDepreciacion">
                            <i class="icon-cloud-download text-primary fz-50 cursor-pointer" title="Descargar reporte"></i>
                            <span class="ql-grid-title">Reporte</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include('footer.php');
?>

<script src="../assets/ajax/rpte_depreciacion.js<?=$version?>"></script>