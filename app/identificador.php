<?php
include('header.php');
require_once '../assets/util/Session.php';
require_once '../controller/ControlSesion.php';
?>
<div class="container-fluid">
    <div class="page-title pl-0 pr-0">
        <h4 class="mb-0 text-info">Identificador por Código</h4>
        <ol class="breadcrumb mb-0 pl-0 pt-1 pb-0">
            <li class="breadcrumb-item text-muted">Genere un código identificador para alguna transacción, en el caso que la validación biométrica presente errores.</li>
        </ol>
    </div>

    <div class="form-group row">
        <div class="col-12 text-center">
            <form id="frm_genIdentify" class="form-inline" style="display: block">
                <input class="form-control form-control-lg text-center text-lg-search-mat mr-2 fz-20" required
                       id="numberdoc" name="numberdoc" type="text" placeholder="# documento" autocomplete="off">
                <button type="submit" class="btn btn-primary btn-lg btn-hover-transform">
                    <i class="fa fa-check position-left"></i>
                    Generar
                </button>
            </form>
        </div>
    </div>

    <div class="card card-body shadow mb-40">
        <div id="mensajes_actions_tab"></div>
        <div class="table-responsive">
            <table id="Tbl_Identificador" class="table datatable-responsive-row-control">
                <thead>
                    <tr>
                        <th>Documento</th>
                        <th>Personal</th>
                        <th>Code</th>
                        <th>Creado el</th>
                        <th>Asignado a</th>
                        <th>Asignado el</th>
                        <th>Servicio</th>
                        <th>Code Transac.</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>



<div class="scrollToTop"><i class="fa fa-arrow-up"></i></div>
<?php
include('footer.php');
?>
<!--toastr-->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css<?=$version?>" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>
<script src="../assets/ajax/identificador.js<?=$version?>"></script>