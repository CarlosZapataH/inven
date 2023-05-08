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
$obj_pf = new PerfilModel();
$dtllePerfil = $obj_pf->detalle_Perfil_xID($user['perfil']);
$opImporExport = 0;
if(!is_null($dtllePerfil)){ $opImporExport = (int)$dtllePerfil['importar_perfil']; }
?>
<br>
<input type="hidden" id="acc_import" value="<?=$opImporExport?>">
<div class="container-fluid">
    <div class="card card-shadow mb-4 card-body">
        <ul class="nav nav-tabs nav-fill mb-4" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#tab-despacho">
                    <i class="fas fa-cart-arrow-down display-5 op-3"></i>
                    <div class="card-title fz-15 mb-0">Despacho</div>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tab-consumo">
                    <i class="fa fa-users display-5 op-3 fa-flip-horizontal"></i>
                    <div class="card-title fz-15 mb-0">Consumo</div>
                </a>
            </li>
        </ul>
    </div>
    <div class="tab-content">
        <div class="tab-pane active" id="tab-despacho" role="tabpanel">
            <div class="page-title">
                <h4 class="page-title text-center no-margin no-padding">
                    Despacho Personal
                </h4>
            </div>
            <p class="text-center mb-10 ">
                Digite el número de :
                <code class="highlighter-rouge">DNI</code> o <code class="highlighter-rouge">.Carnet de extranjería</code>.
            </p>
            <div class="row">
                <div class="col-xl-10 col-lg-10 col-md-10 col-sm-12 offset-xl-1 offset-lg-1 offset-md-1">
                    <div class="card card-shadow mb-4 border-2-dashed">
                        <div class="card-body">
                            <form class="form-inline" id="formDespacho">
                                <label class="form-check-label mr-sm-2">
                                    <input type="checkbox" class="form-check-input scale-chk-1-5" id="chkRango" title="Activar rango">
                                </label>
                                <div class="input-group mb-2 mr-sm-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text" style="height: 41px">
                                            De
                                        </div>
                                    </div>
                                    <label class="sr-only" for="fDesde_his">Desde</label>
                                    <input type="text" class="form-control mb-2 mr-sm-2 inputFecha" required disabled
                                           name="fDesde_his" id="fDesde_his" placeholder="--/--/----" autocomplete="off">
                                </div>
                                <div class="input-group mb-2 mr-sm-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text" style="height: 41px">
                                            a
                                        </div>
                                    </div>
                                    <input type="text" class="form-control mb-2 mr-sm-2 inputFecha" required disabled
                                           name="fHasta_his" id="fHasta_his" placeholder="--/--/----" autocomplete="off">
                                </div>
                                <label class="sr-only" for="ndoc_his">Documento</label>
                                <div class="input-group mb-2 mr-sm-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">#</div>
                                    </div>
                                    <input type="text" class="form-control text-center" name="ndoc_his" id="ndoc_his" maxlength="12" required
                                           placeholder="--------" autocomplete="off" onkeypress="return sga.funcion.valideKey(event);">
                                </div>
                                <button type="submit" class="btn btn-primary mb-2">
                                    <i class="fa fa-search position-left"></i>
                                    Buscar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid" id="divResponse"></div>
        </div>
        <div class="tab-pane" id="tab-consumo" role="tabpanel">
            <div class="container-fluid" id="divTabla" style="display:inline-block">
                <div class="page-title">
                    <h4 class="page-title text-center no-margin no-padding">
                        Reporte de Consumo de Stock
                    </h4>
                    <p class="no-margin no-padding text-muted text-center">
                        Descargue los consumos realizados en el Almacén.
                    </p>
                </div>
                <div class="card card-body card-shadow border-2-dashed">
                    <form id="formReporte">
                        <div class="row">
                            <input type="hidden" id="count_servicio" value="<?= sizeof($lstSev) ?>">
                            <?php
                            if(sizeof($lstSev) == 1) {?>
                                <input type="hidden" name="IdServicioUsuario" id="IdServicioUsuario" value="<?= $lstSev[0]['id_su'] ?>">
                                <?php
                                $lstAlmacenes = $obj_alm->lst_Almacenes_Asignados_xUsuario($lstSev[0]['id_su']);?>
                                <input type="hidden" id="count_almacen" value="<?= sizeof($lstAlmacenes) ?>">
                                <?php
                                if(is_array($lstAlmacenes)){
                                    if(sizeof($lstAlmacenes) == 1){?>
                                        <input type="hidden" name="IdAlmacen"  id="IdAlmacen" value="<?= $lstAlmacenes[0]['id_alm'] ?>">
                                        <?php
                                    }
                                    else{?>
                                        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12 mb-10">
                                            <select name="IdAlmacen" id="IdAlmacen" class="form-control selectIClass" data-placeholder="Almacen..." required>
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
                                    <select id="IdServicioUsuario" name="IdServicioUsuario" class="form-control selectSearch" data-placeholder="Servicio..." required>
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
                                    <select id="IdAlmacen" name="IdAlmacen"  class="form-control selectIClass" data-placeholder="Almacen..." disabled required>
                                        <option></option>
                                    </select>
                                </div>
                                <?php
                            }
                            ?>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-12 col-xs-12 mb-10">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text" style="height: 41px">
                                            De
                                        </div>
                                    </div>
                                    <input type="text" class="form-control mb-2 mr-sm-2 inputFecha" required maxlength="10"
                                           name="fechaini" id="fechaini" placeholder="--/--/----" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-12 col-xs-12 mb-10">
                                <div class="input-group mb-2 mr-sm-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text" style="height: 41px">
                                            a
                                        </div>
                                    </div>
                                    <input type="text" class="form-control mb-2 mr-sm-2 inputFecha" required maxlength="10"
                                           name="fechafin" id="fechafin" placeholder="--/--/----" autocomplete="off">
                                </div>
                            </div>
                            <div class="col">
                                <button type="submit" class="btn btn-primary btn-hover-transform">
                                    <i class="ti-search position-left"></i>
                                    Buscar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
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
                                        <th class="text-center">Colaborador</th>
                                        <th class="text-center">Área</th>
                                        <th class="text-center">Fecha/Hora Transac.</th>
                                        <th class="text-center">Despachado por</th>
                                        <th class="text-center"># Registro</th>
                                        <th class="text-center">Código</th>
                                        <th class="text-center">Descripción</th>
                                        <th class="text-center">UM</th>
                                        <th class="text-center">Cantidad</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
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
<!--toastr-->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css<?=$version?>" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>
<script src="../assets/ajax/reporte.js<?=$version?>"></script>