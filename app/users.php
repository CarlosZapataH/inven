<?php
include('header.php');
require_once '../assets/util/Session.php';
require_once '../controller/ControlSesion.php';?>
<div class="page-title" id="divTitle_Tab" style="display:inline-block">
    <h4 class="mb-0 text-info">
        Usuarios
    </h4>
    <ol class="breadcrumb mb-0 pl-0 pt-1 pb-0">
        <li class="breadcrumb-item text-muted">Agrege o actualize los datos de los usuarios.</li>
    </ol>
</div>
<div class="container-fluid" id="divTabla_Tab" style="display:inline-block">
    <div class="card card-body shadow mb-40">
        <div id="mensajes_actions_tab"></div>
        <div class="table-responsive">
            <table id="Tbl_Usuario" class="table datatable-responsive-row-control">
                <thead>
                <tr>
                    <th></th>
                    <th></th>
                    <th>Servicio/Área</th>
                    <th>Apellidos y nombres</th>
                    <th>Nro.Documento</th>
                    <th>Perfil</th>
                    <th>Email</th>
                    <th>Estado</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div class="container-fluid" id="divResponse_Tab" style="display:none"></div>
<div class="scrollToTop"><i class="fa fa-arrow-up"></i></div>
<?php
include('footer.php');
?>
<!-- bootstrap-select-->
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css?<?=$version?>" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"></script>
<!-- Inputfiles Bootstrap-->
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.0.1/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.0.1/js/fileinput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.0.1/js/locales/es.min.js"></script>
<script src="../assets/ajax/usuarios.js<?=$version?>"></script>

