<?php
include('header.php');
require_once '../model/PerfilModel.php';
$obj_pf = new PerfilModel();
$dtllePerfil = $obj_pf->detalle_Perfil_xID($user['perfil']);?>
<input type="hidden" id="acc_nuevo" value="<?=$dtllePerfil['nuevo_perfil']?>">
<input type="hidden" id="acc_edit" value="<?=$dtllePerfil['editar_perfil']?>">
<input type="hidden" id="acc_del" value="<?=$dtllePerfil['eliminar_perfil']?>">
<input type="hidden" id="acc_active" value="<?=$dtllePerfil['activasusp_perfil']?>">
<div class="container-fluid" id="divTabla" style="display:inline-block">
    <div class="page-title pl-0 pr-0 pb-10">
        <h4 class="mb-0">
            Perfil de usuario
        </h4>
        <ol class="breadcrumb mb-0 pl-0 pt-1 pb-0">
            <li class="breadcrumb-item text-muted">Agrege o actualize los datos de los perfiles de usuarios.</li>
        </ol>
    </div>
    <div class="row">
        <div class="col-12 mb-20">
            <div class="card card-body card-shadow">
                <div id="mensajes_actions_pf"></div>
                <div class="table-responsive">
                    <table id="Tbl_Perfil" class="table datatable-responsive-row-control ">
                        <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>Titulo</th>
                            <th>Nuevo</th>
                            <th>Editar</th>
                            <th>Eliminar</th>
                            <th>Visual.</th>
                            <th>Reporte</th>
                            <th>Importar/Exportar</th>
                            <th>Act/Susp.</th>
                            <th>Transf.</th>
                            <th>Retirar</th>
                            <th>Devolver</th>
                            <th>Estado</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid" id="divResponse" style="display:none"></div>
<?php
include('footer.php');
?>
<!--
<link href="https://unpkg.com/bootstrap-table@1.18.1/dist/bootstrap-table.min.css" rel="stylesheet">
<script src="https://unpkg.com/bootstrap-table@1.18.1/dist/bootstrap-table.min.js"></script>
<script src="https://unpkg.com/bootstrap-table@1.18.1/dist/bootstrap-table-locale-all.min.js"></script>
-->
<script src="../assets/ajax/perfil.js<?=$version?>"></script>