<?php
include('header.php');
require_once __DIR__ . '/Helpers/LoadEnv.php';
require_once '../model/AlmacenModel.php';
require_once '../model/GerenciaModel.php';
require_once '../model/ServicioModel.php';
require_once '../model/PerfilModel.php';
$obj_serv = new ServicioModel();
$obj_alm = new AlmacenModel();
$lstSev = $obj_serv->lst_servicios_Asignados_Activos_xIDUS($user['id_us']);
$obj_pf = new PerfilModel();
$dtllePerfil = $obj_pf->detalle_Perfil_xID($user['perfil']);
?>
<input type="hidden" id="acc_report" value="<?=$dtllePerfil['reporte_perfil']?>">
<input type="hidden" id="acc_importExport" value="<?=$dtllePerfil['importar_perfil']?>">
<div class="container-fluid" id="divTabla" style="display:inline-block">
    <div class="page-title pl-0 pr-0 pb-10">
        <h4 class="mb-0">
            Reporte Movimiento Externo
        </h4>
        <ol class="breadcrumb mb-0 pl-0 pt-1 pb-0">
            <li class="breadcrumb-item text-muted">Descargue los movimientos realizados en el Almacén.</li>
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
                            <select name="IdAlmacen" id="IdAlmacen" class="form-control selectIClass" data-placeholder="Almacen...">
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
                <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12 mb-10">
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
                    <select id="IdAlmacen" class="form-control selectIClass" data-placeholder="Almacen..." disabled>
                        <option></option>
                    </select>
                </div>
                <?php
            }
            ?>
            <div class="col-lg-3 col-md-6 col-sm-12 col-xs-12">
                <select id="transacciones" name="selectorMultipleVal" required
                        data-style="form-control" multiple class="selectpicker selectMultiple"
                        data-live-search="true" data-live-search-placeholder="Escriba para buscar.."
                        data-none-selected-text="Transacción..."  data-width="100%" data-size="auto"
                        data-selected-text-format="count>1">
                    <option value="TRA">Transferencia</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-12 col-xs-12">
                <input type="text" class="form-control" placeholder="dd/mm/yyyy a dd/mm/yyyy" maxlength="24"
                       autocomplete="off" id="fecha" style="background-color:#FFFFFF">
            </div>
            <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12 text-lg-right text-md-right text-sm-right text-xs-center">
                <button type="button" class="btn btn-warning btn-hover-transform" id="btnAction_Search">
                    <i class="ti-search mr-10"></i>
                    Buscar
                </button>
            </div>
            <?php
                if((int)$dtllePerfil['create_guide'] == 1){
                    ?>
                        <div class="col-lg-2 col-md-3 col-sm-12">
                            <button type="button" class="btn btn-primary btn-hover-transform" id="openCosolidate">
                                <i class="ti-pencil-alt mr-10"></i>
                                Generar Guía
                            </button>
                        </div>
                    <?php
                }
            ?>
        </div>
    </div>
    <hr class="no-padding mb-0 mt-0">
    <div class="row">
        <div class="col-12 mb-20">
            <div class="card card-shadow">
                <div class="card-header">
                    <div class="card-title">
                        Lista de movimientos generados del almacén
                    </div>
                </div>
                <div id="mensajes_actions_rpte"></div>
                <div class="table-responsive">
                    <table id="Tbl_Reporte" class="table table-striped table-bordered table-sm">
                        <thead>
                        <tr>
                            <th class="text-center">Nro.Mov</th>
                            <th class="text-center">F.Registro</th>
                            <th class="text-center">Antención a</th>
                            <th class="text-center">Motivo</th>
                            <th class="text-center">Almacén Origen</th>
                            <th class="text-center">Almacén Destino</th>                            
                            <!-- <th class="text-center">Nro.Guia</th> -->
                            <th class="text-center">Estado</th>
                            <th class="text-center"></th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="scrollToTop"><i class="fa fa-arrow-up"></i></div>
<?php
include('footer.php');
?>
<!-- Formatter -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/formatter.js/0.1.5/jquery.formatter.min.js"></script>
<!-- Calendar  flatpickr -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/2.4.8/flatpickr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/2.4.8/l10n/es.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/2.4.8/flatpickr.min.css"/>
<!-- bootstrap-select-->
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css?<?=$version?>" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"></script>
<!-- Bootstrap  Table -->
<link href="https://unpkg.com/bootstrap-table@1.18.1/dist/bootstrap-table.min.css" rel="stylesheet">
<script src="https://unpkg.com/bootstrap-table@1.18.1/dist/bootstrap-table.min.js"></script>
<script src="https://unpkg.com/bootstrap-table@1.18.1/dist/bootstrap-table-locale-all.min.js"></script>
<script src="../assets/ajax/movimiento_externo.js<?=$version?>"></script>
