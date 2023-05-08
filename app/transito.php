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
<div class="container-fluid" id="divTabla">
    <form id="transitoSearching">
        <div class="page-title pl-0 pr-0 pb-10">
            <div class="row">
                <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                    <h4 class="mb-0">
                        Transferencia a Transito
                    </h4>
                    <ol class="breadcrumb mb-0 pl-0 pt-1 pb-0">
                        <li class="breadcrumb-item text-muted">Visualice el transito de las transferencias realizadas.</li>
                    </ol>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-12">
                    <select id="IdTipoTransito" class="form-control" data-placeholder="Tipo..." required>
                        <option></option>
                        <option value="SAL">Salida</option>
                        <option value="IN">Ingreso</option>
                    </select>
                </div>
                <div class="col-xl-2 col-lg-2 col-md-2 col-sm-12">
                    <button type="submit" class="btn btn-primary" id="btnSearching">
                        <i class="icon-check pr-2"></i> Buscar
                    </button>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                <input type="hidden" id="count_servicio" value="<?= sizeof($lstSev) ?>">
                <div class="card card-shadow border-1-dashed">
                    <div class="card-body pt-10">
                        <h5 class="card-title">De</h5>
                        <?php
                        if(sizeof($lstSev) == 1) {
                            $dtlleServicio = $obj_serv->detalle_Servicio_xID($lstSev[0]['id_serv']);?>
                           <div class="row">
                               <div class="col-12 mb-10">
                                   <input type="hidden" id="IdServicioUsuario" value="<?= $lstSev[0]['id_su'] ?>">
                                   <input type="text" class="form-control" value="<?= $dtlleServicio['des_serv'] ?>" readonly>
                               </div>
                               <div class="col-12">
                                   <?php
                                   $lstAlmacenes = $obj_alm->lst_Almacenes_Asignados_xUsuario($lstSev[0]['id_su']);?>
                                   <input type="hidden" id="count_almacen" value="<?= sizeof($lstAlmacenes) ?>">
                                   <?php
                                   if(is_array($lstAlmacenes)){
                                       if(sizeof($lstAlmacenes) == 1){?>
                                           <input type="hidden" id="IdAlmacen" value="<?= $lstAlmacenes[0]['id_alm'] ?>">
                                           <input type="hidden" id="VistaAlmacen" value="<?= $lstAlmacenes[0]['vista_alm'] ?>">
                                           <input type="text" class="form-control"  value="<?= $lstAlmacenes[0]['titulo_alm'] ?>" readonly>
                                           <?php
                                       }
                                       else{?>
                                           <select name="IdAlmacen" id="IdAlmacen" class="form-control selectClass" data-placeholder="Seleccione..." required>
                                               <option></option>
                                               <?php
                                               foreach ($lstAlmacenes as $almacen){?>
                                                   <option value="<?=$almacen['id_alm']?>" data-vista="<?=$almacen['vista_alm']?>">
                                                       <?=$almacen['titulo_alm']?>
                                                   </option>
                                                   <?php
                                               }
                                               ?>
                                           </select>
                                       <?php
                                       }
                                   }
                                   ?>
                               </div>
                           </div>
                        <?php
                        }
                        else{
                            $obj_ge = new GerenciaModel();
                            $lstGerencias = $obj_ge->lst_Gerencia_Activas();?>
                            <input type="hidden" id="count_almacen" value="0">
                            <div class="row">
                                <div class="col-12 mb-10">
                                    <select id="IdServicioUsuario" class="form-control input-md selectClass" required>
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
                                <div class="col-12">
                                    <select id="IdAlmacen" class="form-control selectClass" data-placeholder="Almacén..." disabled required>
                                        <option></option>
                                    </select>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                <div class="card card-shadow border-1-dashed">
                    <div class="card-body pt-10">
                        <h5 class="card-title">A</h5>
                        <?php
                        $obj_ge = new GerenciaModel();
                        $lstGerencias = $obj_ge->lst_Gerencia_Activas();?>
                        <div class="row">
                            <div class="col-12 mb-10">
                                <select id="IdServicioUsuario_a" class="form-control input-md selectClass" disabled required>
                                    <option value="">Servicio...</option>
                                    <?php
                                    if (!is_null($lstGerencias)) {
                                        $obj_serv_b = new ServicioModel();
                                        foreach ($lstGerencias as $gerencia) {
                                            $lstServiciosUS = $obj_serv_b->lst_Servicio_Activos_xGerencia_All($gerencia['id_ge']);
                                            if (!is_null($lstServiciosUS)) {?>
                                                <optgroup label="<?= $gerencia['des_ge'] ?>">
                                                    <?php foreach ($lstServiciosUS as $servicio) { ?>
                                                        <option value="<?= $servicio['id_serv'] ?>">
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
                            <div class="col-12">
                                <select id="IdAlmacen_a" class="form-control selectClass" data-placeholder="Almacén..." disabled required>
                                    <option></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="container-fluid" id="divResponse"></div>
<div class="scrollToTop"><i class="fa fa-arrow-up"></i></div>
<?php
include('footer.php');
?>
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
<!--toastr-->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css<?=$version?>" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>
<script src="../assets/ajax/transito.js<?=$version?>"></script>