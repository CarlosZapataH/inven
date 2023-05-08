<?php
include('header.php');
require_once("../model/PerfilModel.php");
require_once("../model/PermisoModel.php");
$obj_pf = new PerfilModel();
$obj_pm = new PermisoModel();

$where[] = "padre='0'";
if(is_array($where)){ $where = "WHERE ".implode(" AND ",$where);}
else { $where = ""; }

$lstPerfil = $obj_pf->lst_Perfil_Activos_All();
$lstModulos = $obj_pm->listar_modulos($where,'indiceh');?>
<div class="container-fluid">
    <div class="page-title pl-0 pr-0 pb-10">
        <h4 class="mb-0">
            Permisos Módulos
        </h4>
        <ol class="breadcrumb mb-0 pl-0 pt-1 pb-0">
            <li class="breadcrumb-item text-muted">Agrege o actualize el acceso a los módulos en base al perfil correspondiente.</li>
        </ol>
    </div>
    <div class="card card-body card-shadow">
        <div class="card-body">
            <div class="form-group row">
                <label for="id_perfil" class="col-lg-4 col-md-4 col-sm-12 col-xs-12 col-form-label text-lg-right text-md-right text-sm-left text-xs-left">Perfil</label>
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <select id="id_perfil" name="id_perfil" class="form-control selectIclass" data-placeholder="Seleccione...">
                        <option></option>
                        <?php
                        if(is_array($lstPerfil)){
                            $i=1;
                            foreach ($lstPerfil as $perfil) {?>
                                <option value="<?= $perfil['id_perfil'] ?>">
                                    <?= $i . " - " . $perfil['titulo_perfil'] ?>
                                </option>
                                <?php
                                $i++;
                            }
                        } ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <label for="inputEmail3" class="col-lg-4 col-md-4 col-sm-12 col-xs-12 col-form-label text-lg-right text-md-right text-sm-left text-xs-left">Módulo</label>
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <select id="id_modulo_opcion" name="id_modulo_opcion" class="form-control selectIclass" data-placeholder="Seleccione...">
                        <option></option>
                        <?php
                        if(is_array($lstModulos)){
                            $i=1;
                            foreach($lstModulos as $modulo) {?>
                                <option value="<?= $modulo['id_modulo_opcion'] ?>">
                                    <?= $i . " - " . $modulo['abreviado'] ?>
                                </option>
                                <?php
                                $i++;
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid" id="divResponse"></div>
<?php
include('footer.php');
?>
<script src="../assets/ajax/permisos.js<?=$version?>"></script>

