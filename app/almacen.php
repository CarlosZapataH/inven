<?php
include('header.php');
require_once '../assets/util/Session.php';
require_once '../controller/ControlSesion.php';
require_once '../model/GerenciaModel.php';
require_once '../model/ServicioModel.php';
require_once '../model/PerfilModel.php';
$obj_serv = new ServicioModel();
$lstSev = $obj_serv->lst_servicios_Asignados_Activos_xIDUS($user['id_us']);
$obj_pf = new PerfilModel();
$dtllePerfil = $obj_pf->detalle_Perfil_xID($user['perfil']);?>
<input type="hidden" id="acc_nuevo" value="<?=$dtllePerfil['nuevo_perfil']?>">
<input type="hidden" id="acc_edit" value="<?=$dtllePerfil['editar_perfil']?>">
<input type="hidden" id="acc_del" value="<?=$dtllePerfil['eliminar_perfil']?>">
<input type="hidden" id="acc_active" value="<?=$dtllePerfil['activasusp_perfil']?>">
<div class="container-fluid" id="divTabla" style="display:inline-block">
    <div class="page-title pl-0 pr-0 pb-10">
        <h4 class="mb-0">
            Almacén
        </h4>
        <ol class="breadcrumb mb-0 pl-0 pt-1 pb-0">
            <li class="breadcrumb-item text-muted">Agrege o actualize los datos de los almacenes creados para su servicio.</li>
        </ol>
    </div>
    <input type="hidden" id="count_servicio" value="<?= sizeof($lstSev) ?>">
    <?php

    if(sizeof($lstSev) == 1) {?>
        <input type="hidden" id="IdServicio" value="<?= $lstSev[0]['id_serv'] ?>">
        <?php
    }
    else{
        $obj_ge = new GerenciaModel();
        $lstGerencias = $obj_ge->lst_Gerencia_Activas();?>
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-10">
                <select id="IdServicio" class="form-control input-md selectedClass" data-placeholder="Seleccione...">
                    <option></option>
                    <?php
                    // Solo para almacen se utiliza el ID Servicio
                    if (!is_null($lstGerencias)) {
                        $obj_serv_b = new ServicioModel();
                        foreach ($lstGerencias as $gerencia) {
                            $lstServiciosUS = $obj_serv_b->lst_Servicio_xGerencia_Usuario($gerencia['id_ge'], $user['id_us']);
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
        </div>
        <?php
    }
    ?>
    <div class="card card-body shadow mb-40">
        <div id="mensajes_actions_alm"></div>
        <div class="table-responsive">
            <table id="Tbl_Almacen" class="table datatable-responsive-row-control">
                <thead>
                <tr>
                    <th></th>
                    <th></th>
                    <th>Descripción</th>
                    <th>Creado por</th>
                    <th>Fecha publicación</th>
                    <th>Estado</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<div class="container-fluid" id="divResponse" style="display:none"></div>
<?php
include('footer.php');
?>
<!-- OWL CAROUSEL -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<!--Email multiple-->
<link href="../assets/plugins/emailMultiple/email.multiple.css<?=$version?>" rel="stylesheet">
<script src="../assets/plugins/emailMultiple/jquery.email.multiple.js<?=$version?>"></script>
<script src="../assets/plugins/emailMultiple/jquery.email.multiple_edt.js<?=$version?>"></script>
<script src="../assets/ajax/almacen.js<?=$version?>"></script>