<?php
include('header.php');
require_once '../assets/util/Session.php';
require_once '../controller/ControlSesion.php';
require_once '../model/PerfilModel.php';
require_once '../model/AlmacenModel.php';
require_once '../model/GerenciaModel.php';
require_once '../model/ServicioModel.php';
$obj_serv = new ServicioModel();
$lstSev = $obj_serv->lst_servicios_Asignados_Activos_xIDUS($user['id_us']);
$obj_alm = new AlmacenModel();
$obj_pf = new PerfilModel();
$dtllePerfil = $obj_pf->detalle_Perfil_xID($user['perfil']); ?>
<input type="hidden" id="acc_edit" value="<?=$dtllePerfil['editar_perfil']?>">
<input type="hidden" id="acc_del" value="<?=$dtllePerfil['eliminar_perfil']?>">
<div class="container" id="divMatricula" style="display: inline">
    <div class="page-title pb-10">
        <h4 class="mb-0 text-brown-800 font-weight-bold text-center ">
            Ingreso Personal
        </h4>
        <p class="text-center">Digite el número de : <code class="highlighter-rouge">DNI</code> o <code class="highlighter-rouge">.Carnet de extranjería</code>.</p>
        <form id="frm_searchPersonal">
            <div class="row">
                <div class="col-xl-6 offset-xl-3 col-lg-6 offset-lg-3 col-md-6 offset-md-3 col-sm-12">
                    <div class="input-group">
                    <input class="form-control text-center" name="ndoc_col" id="ndoc_col" maxlength="12"
                           type="text" placeholder="# documento" required autocomplete="off" onkeypress="return sga.funcion.valideKey(event);">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-info">
                                <i class="ti-search fz-18 position-left"></i>
                                Buscar
                            </button>
                            <a class="btn btn-warning btn-sm" id="ImportPersonal"
                                    title="Importar">
                                <i class="icon-cloud-upload fz-18 position-left"></i>
                                Importar
                            </a>
                            <a class="btn btn-secondary btn-sm text-white" id="viewListaPersonal"
                                    title="lista personal">
                                <i class="icon-list fz-18"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="container-fluid" id="divTabla" style="display: none">
    <div class="page-title py-10">
        <h4 class="mb-0 text-brown-800 font-weight-bold text-center ">
            <a id="btn_CancelLista" class="btn btn-light cursor-pointer mr-10"><i class="ti-angle-left"></i></a>
            Lista de personal
        </h4>
        <p class="breadcrumb-item text-muted mb-0 text-center ">Seleccione un servicio.</p>
        <input type="hidden" id="count_servicio" value="<?= sizeof($lstSev) ?>">
        <?php
        $lstAlmacenes = array();
        if(count($lstSev) == 1) {
            $lstAlmacenes = $obj_alm->lst_Almacenes_Asignados_xUsuario($lstSev[0]['id_su']);?>
            <input type="hidden" id="IdServicioSearch" value="<?= $lstSev[0]['id_su'] ?>">
            <?php
        }
        else{
            $obj_ge = new GerenciaModel();
            $lstGerencias = $obj_ge->lst_Gerencia_Activas();?>
            <div class="row">
                <div class="col-xl-6 col-lg-6 col-md-8 col-sm-12 mb-10 offset-xl-3 offset-lg-3 offset-md-2">
                    <select id="IdServicioSearch" class="form-control input-md classSelect" data-placeholder="Servicio...">
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
        <div class="card shadow">
            <table id="Tbl_Personal" class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th class="text-center text-uppercase align-middle">Servicio</th>
                    <th class="text-center text-uppercase align-middle">Área Operativa</th>
                    <th class="text-center text-uppercase align-middle">Apellidos y nombres</th>
                    <th class="text-center text-uppercase align-middle">Nro.Documento</th>
                    <th class="text-center text-uppercase align-middle">Biometria</th>
                    <th class="text-center text-uppercase align-middle" >Estado</th>
                    <th class="text-center text-uppercase align-middle"></th>
                </tr>
                </thead>
            </table>
        </div>
        <br><br><br>
    </div>
</div>

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
<script src="../assets/ajax/ingreso-personal.js<?=$version?>"></script>