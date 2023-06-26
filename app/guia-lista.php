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
if (sizeof($lstSev) == 1) {
    $lstAlmacenes = $obj_alm->lst_Almacenes_Asignados_xUsuario($lstSev[0]['id_su']);
    if (is_array($lstAlmacenes)) {
        if (sizeof($lstAlmacenes) == 1) {
            $nameAlmacen = "Control de EPPS : " . $lstAlmacenes[0]['titulo_alm'];
        }
    }
}
$obj_pf = new PerfilModel();
$dtllePerfil = $obj_pf->detalle_Perfil_xID($user['perfil']);


/**
 * start code
 */
// $idMovimiento = $_REQUEST['idMovimiento'];
// require_once '../model/MovimientoModel.php';
// $obj_mov = new MovimientoModel();
// $movement = $obj_mov->detalle_MovimientoTransito_xID($idMovimiento);
// $movement_detail = $obj_mov->lista_MovimientoTransitoDetalle_xIdMovimiento($idMovimiento);

//detalle_MovimientoTransito_xID($id)
//lista_MovimientoTransitoDetalle_xIdMovimiento($id)

/**
 * end code
 */
?>
<!-- <link rel="stylesheet" href="https://unpkg.com/vee-validate/dist/style.css"> -->
<style>
    .img {
        margin: 10px auto;
        border-radius: 5px;
        border: 1px solid #ccc;
        padding: 13px;
        width: 30%;
        background-size: 100% 100%;
    }

    .imgFinger {
        width: 97%;
    }

    .ct2 {
        margin: 10px auto;
        border-radius: 5px;
        border: 1px solid #ccc;
        padding: 5px 7px;
        width: 270px;
        height: 30px;
        background-size: 100% 100%;
    }

    .dataUser {
        margin: 10px auto;
        border-radius: 5px;
        border: 1px solid #ccc;
        padding: 5px 7px;
        width: 270px;
        height: 58px;
        background-size: 100% 100%;
    }
</style>

<input type="hidden" id="acc_edit" value="<?= $dtllePerfil['editar_perfil'] ?>">
<input type="hidden" id="acc_del" value="<?= $dtllePerfil['eliminar_perfil'] ?>">
<input type="hidden" id="acc_import" value="<?= $dtllePerfil['importar_perfil'] ?>">

<input type="hidden" id="inv_new" value="<?= $dtllePerfil['nuevo_perfil'] ?>">
<input type="hidden" id="inv_trans" value="<?= $dtllePerfil['transferir_perfil'] ?>">
<input type="hidden" id="inv_reti" value="<?= $dtllePerfil['retirar_perfil'] ?>">
<input type="hidden" id="inv_devol" value="<?= $dtllePerfil['devolver_perfil'] ?>">
<div class="container-fluid" id="divHead">
    <div id="app">
        <div class="page-title pl-0 pr-0 pb-10">
            <h4 class="mb-0">
                <span>Lista GRE</span>
            </h4>
            <ol class="breadcrumb mb-0 pl-0 pt-1 pb-0">
                <li class="breadcrumb-item text-muted">Lista de GRE registradas</li>
            </ol>
        </div>


        <div class="card">
            <div class="card-body">
                <div class="mb-10">
                    <div class="row">
                        <div class="col-12 col-sm-4">
                            <label class="col-form-label">Busqueda </label>
                            <input v-model="filters.q" placeholder="Escriba el Código" type="text" class="form-control" @input="listenFilter">
                        </div>
                        <div class="col-12 col-sm-4">
                            <label class="col-form-label">Rango de fecha </label>
                            <div class="d-flex">
                                <input v-model="filters.date_from" :max="filters.date_to" placeholder="Fecha inicio" type="date" class="form-control" @input="listenFilter">
                                <input v-model="filters.date_to" :min="filters.date_from" placeholder="Fecha fin" type="date" class="form-control" @input="listenFilter">
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table datatable-responsive-row-control">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Fecha</th>
                                <th>Nombre</th>
                                <th>Remitente</th>
                                <th>Destinatario</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(guide, index) in paginatedItems" :key="index + '-item-guide'">
                                <td>
                                    <span v-if="guide?.serie && guide?.number">{{guide.serie + '-' + guide.number}}</span>
                                </td>
                                <td>{{guide.date_issue}}</td>
                                <td>{{guide.name}}</td>
                                <td>{{guide.start_store.company.name}}</td>
                                <td>{{guide.end_store.company.name}}</td>
                                <td>
                                    <span :class="'py-2 d-block badge ' + (getStatusProperty(guide.tci_response_description).class)" style="max-width: 140px;">
                                        {{guide.tci_response_description}}
                                    </span>
                                </td>
                                <td>
                                    <a :href="'guia-editar.php?idMovimiento=' + guide.id" class="btn btn-primary btn-sm">Ver</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <nav aria-label="Page navigation example">
                    <ul class="pagination justify-content-center">
                        <li class="page-item" :class="{ 'disabled': currentPage === 1 }">
                            <a class="page-link" href="#" @click="setCurrentPage(currentPage - 1)">Anterior</a>
                        </li>
                        <li class="page-item" v-for="page in totalPages" :key="page" :class="{ 'active': currentPage === page }">
                            <a class="page-link" href="#" @click="setCurrentPage(page)">{{ page }}</a>
                        </li>
                        <li class="page-item" :class="{ 'disabled': currentPage === totalPages }">
                            <a class="page-link" href="#" @click="setCurrentPage(currentPage + 1)">Siguiente</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="count_servicio" value="<?= sizeof($lstSev) ?>">
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
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css<?= $version ?>" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>
<script src="../assets/ajax/despacho.js<?= $version ?>"></script>

<!-- vue -->
<script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
<!--<script src="https://cdn.jsdelivr.net/npm/@vee-validate/rules@4.4.7/dist/vee-validate-rules.min.js"></script> -->
<script src="https://cdn.jsdelivr.net/npm/vee-validate@2.2.15/dist/vee-validate.js"></script>
<!-- <script src="https://cdn.jsdelivr.net/npm/vee-validate@<3.0.0/dist/vee-validate.js"></script> -->
<script src="https://cdn.jsdelivr.net/npm/vee-validate@2/dist/locale/es.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script src="../assets/ajax/apis.js<?= $version ?>"></script>
<script src="../assets/ajax/guide_list.js<?= $version ?>"></script>