<?php
include('header.php');
require_once '../model/AlmacenModel.php';
require_once '../model/GerenciaModel.php';
require_once '../model/ServicioModel.php';
require_once '../model/PerfilModel.php';
require_once '../model/FuncionesModel.php';
$obj_serv = new ServicioModel();
$obj_alm = new AlmacenModel();
$lstSev = $obj_serv->lst_servicios_Asignados_Activos_xIDUS($user['id_us']);
$obj_pf = new PerfilModel();
$dtllePerfil = $obj_pf->detalle_Perfil_xID($user['perfil']);
$obj_fn = new FuncionesModel();
$numberMeses = $obj_fn->difMeses('2020-08-03',date("Y-m-d"));
?>
<input type="hidden" id="acc_report" value="<?=$dtllePerfil['reporte_perfil']?>">
<div class="container-fluid" id="divTabla" style="display:inline-block">
    <div class="page-title pl-0 pr-0 pb-10">
        <h4 class="mb-0">
            Reporte Inventario
        </h4>
        <ol class="breadcrumb mb-0 pl-0 pt-1 pb-0">
            <li class="breadcrumb-item text-muted">Descargue el reporte del inventario de su servicio.</li>
        </ol>
    </div>
    <div class="card card-body card-shadow">
        <div class="row">
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
                        <?php
                    }
                    else{?>
                        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 mb-10">
                            <select name="IdAlmacen" id="IdAlmacen" class="form-control selectClass" data-placeholder="Almacen...">
                                <option></option>
                                <?php
                                foreach ($lstAlmacenes as $almacen){?>
                                    <option value="<?=$almacen['id_alm']?>"><?=$almacen['titulo_alm']?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                        <?php
                    }
                }
            }
            else{
                $obj_ge = new GerenciaModel();
                $lstGerencias = $obj_ge->lst_Gerencia_Activas();?>
                <input type="hidden" id="count_almacen" value="0">

                    <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 mb-10">
                        <select id="IdServicioUsuario" class="form-control selectSearch" data-placeholder="Servicio...">
                            <option></option>
                            <?php
                            if (is_array($lstGerencias)) {
                                $obj_serv_b = new ServicioModel();
                                foreach ($lstGerencias as $gerencia) {
                                    $lstServiciosUS = $obj_serv_b->lst_Servicio_xGerencia_Usuario($gerencia['id_ge'], $user['id_us']);
                                    if (is_array($lstServiciosUS)) {?>
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
                    <div class="col-lg-2 col-md-6 col-sm-12 col-xs-12 mb-10">
                        <select id="IdAlmacen" class="form-control selectClass" data-placeholder="Almacen..." disabled>
                            <option></option>
                        </select>
                    </div>

                <?php
            }
            ?>
            <div class="col-lg-2 col-md-6 col-sm-12 col-xs-12">
                <select id="tipoReporte" class="form-control selectClass" data-placeholder="Tipo rpte...">
                    <option></option>
                    <option value="1">Actual</option>
                    <option value="2">backup</option>
                </select>
            </div>

            <div class="col-lg-1 col-md-6 col-sm-12 col-xs-12">
                <select id="periodo" class="form-control selectClass" data-placeholder="Periodo..." disabled>
                    <option></option>
                    <?php
                    for ($y = date("Y"); $y >= 2020; $y-- ) {?>
                        <option value="<?=$y?>"><?=$y?></option>
                        <?php
                    }
                    ?>
                </select>
            </div>
            <div class="col-lg-1 col-md-6 col-sm-12 col-xs-12">
                <select id="mes" class="form-control selectClass" data-placeholder="Mes..." disabled>
                    <option></option>
                </select>
            </div>
            <div class="col-lg-2 col-md-6 col-sm-12 col-xs-12">
                <select id="corte" class="form-control selectClass" data-placeholder="Corte..." disabled>
                    <option></option>
                </select>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-4 col-xs-12 text-lg-right text-md-right text-sm-right text-xs-center">
                <button type="button" class="btn btn-warning btn-hover-transform" id="btnAction_Search">
                    <i class="ti-search mr-10"></i>
                    Buscar
                </button>
            </div>
        </div>
    </div>
    <hr class="no-padding mb-0 mt-0">
    <div class="row">
        <div class="col-12 mb-20">
            <div class="card card-shadow">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-8 col-md-6 col-sm-12 col-xs-12 text-lg-left text-md-left text-sm-center text-xs-center">
                            <div class="card-title" id="divinfo_number">
                                Lista de registros del almacén
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 text-lg-right text-md-right text-sm-center text-xs-center" id="divinfo_btn"></div>
                    </div>
                </div>
                <div id="mensajes_actions_rpte"></div>
                <div class="table-responsive">
                    <table id="Tbl_Reporte" class="table table-striped table-bordered table-sm">
                        <thead>
                        <tr>
                            <th class="text-center">Código</th>
                            <th class="text-center">Cant.</th>
                            <th class="text-center">Und.Medida</th>
                            <th class="text-left">Descripción</th>
                            <th class="text-left">Unidad</th>
                            <th class="text-center">O.Mantto</th>
                            <th class="text-center">Fecha Rec.</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="7" class="text-center">No existen registros</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid" id="divResponse" style="display:none"></div>
<div class="scrollToTop"><i class="fa fa-arrow-up"></i></div>
<?php
include('footer.php');
?>
<script src="../assets/ajax/rpte_inventario.js<?=$version?>"></script>