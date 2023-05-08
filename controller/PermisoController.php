<?php
session_start();
require_once '../model/PermisoModel.php';
require_once '../assets/util/Session.php';

$action = $_REQUEST["action"];
$controller = new PermisoController();
call_user_func(array($controller,$action));

class PermisoController {

    public function listar_Permisos_All_Ajax(){
        try {
            $wheree ="";
            $idperfil = (int)$_GET['id_perfil'];
            $idmodopcion = (int)$_GET['id_modulo'];
            $obj_pf = new PermisoModel();
            $Opciones = $obj_pf->listar_modulo_opcion($idmodopcion, $wheree, 'mo.indiceh');?>
            <div class="card card-body card-shadow">
                <div class="row">
                    <div class="col-12" id="mensajes_actions"></div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-sm table-bordered">
                        <thead>
                            <tr class="border-double">
                                <th width="90%" class="text-left">Formulario</th>
                                <th width="10%" class="text-center">
                                    Acceso
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        if(is_array($Opciones) && (int)sizeof($Opciones) > 0){
                            foreach($Opciones as $fila){
                                $OptionPermiso = $obj_pf->detalle_Permiso_xIDs($idperfil,$fila['id_modulo_opcion']);?>
                                <tr>
                                    <td class="text-left">
                                        <?=$fila['abreviado']?>
                                    </td>
                                    <td class="text-center">
                                        <label class="control control-outline control-outline-info control--checkbox">
                                            <input type="checkbox" name="chkoption" value="1" data-idmodulo="<?=$fila['id_modulo_opcion']?>"
                                                   class="chkPermiso_modulo" <?php if ($OptionPermiso['acceso']=="1") echo "checked"; ?>
                                                   data-tipo="h" data-padre="<?=$idmodopcion?>" data-perfil="<?=$idperfil?>">
                                            <span class="control__indicator"></span>
                                        </label>
                                    </td>
                                </tr>
                            <?php }
                        }
                        else{
                            $dtlleModulo = $obj_pf->detalle_moduloOpcion_xID($idmodopcion);
                            if((int)$dtlleModulo['padre'] == 0 && $dtlleModulo['url'] != "#"  && empty($dtlleModulo['estilo'])) {
                                $OptionPermiso = $obj_pf->detalle_Permiso_xIDs($idperfil,$idmodopcion);?>

                                <tr>
                                    <td class="text-left">
                                        <?=$dtlleModulo['abreviado']?>
                                    </td>
                                    <td class="text-center">
                                        <label class="control control-outline control-outline-info control--checkbox">
                                            <input type="checkbox" name="chkoption" value="1" data-idmodulo="<?=$idmodopcion?>"
                                                   class="chkPermiso_modulo" <?php if ($OptionPermiso['acceso']=="1") echo "checked"; ?>
                                                   data-tipo="p" data-padre="<?=$idmodopcion?>" data-perfil="<?=$idperfil?>">
                                            <span class="control__indicator"></span>
                                        </label>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function update_Permiso_Modulo_JSON(){
        try {
            $obj_pf = new PermisoModel();

            $idhijo = (int)$_POST['idmodulo'];
            $tipo = trim($_POST['tipo']);
            $idpadre = (int)$_POST['padre'];
            $idperfil = (int)$_POST['perfil'];
            $cheked = (int)$_POST['chekedd'];
            $val = 0;
            if($tipo == "h"){ // Modulo Hijo
                if($cheked == 1){ // registra
                    $dtllePadre = $obj_pf->detalle_Padre_permiso($idperfil,$idpadre);
                    if(is_null($dtllePadre)){
                        $datesPerm[0] = $idperfil;
                        $datesPerm[1] = $idpadre;
                        $datesPerm[2] = 0;
                        $obj_pf->registrar_permiso($datesPerm);
                    }
                    $datesPHijo[0] = $idperfil;
                    $datesPHijo[1] = $idhijo;
                    $datesPHijo[2] = 1;
                    $insertDel = $obj_pf->registrar_permiso($datesPHijo);
                    if($insertDel){ $val = 1; }
                }
                else if($cheked == 0){//Elimina
                    //Eliminamos hijo
                    $dtlleHijo = $obj_pf->detalle_Permiso_xIDs($idperfil,$idhijo);
                    if(is_array($dtlleHijo)){
                        $insertDel = $obj_pf->delete_Permiso_xID($dtlleHijo['id_permiso']);
                        if($insertDel){ $val = 1; }
                    }

                    $lstModulosHijos = $obj_pf->lst_modulosHijos_xIdPadrea($idpadre);
                    $existeHijo = 0;
                    if(is_array($lstModulosHijos)){
                        foreach ($lstModulosHijos as $modulo){
                            $dtlleHijoPermiso = $obj_pf->detalle_Permiso_xIDs($idperfil,$modulo['id_modulo_opcion']);
                            if(is_array($dtlleHijoPermiso)){
                                $existeHijo++;
                            }
                        }
                    }

                    if((int)$existeHijo == 0){//Eliminamos Padre
                        $dtllePadre = $obj_pf->detalle_Permiso_xIDs($idperfil,$idpadre);
                        $obj_pf->delete_Permiso_xID($dtllePadre['id_permiso']);
                    }
                }
            }
            else if($tipo == "p"){//Modulo padre
                if($cheked == 1){ // registra
                    $datesPerm[0] = $idperfil;
                    $datesPerm[1] = $idpadre;
                    $datesPerm[2] = 1;
                    $insertDel = $obj_pf->registrar_permiso($datesPerm);
                    if($insertDel){ $val = 1; }
                }
                else if($cheked == 0){//Elimina
                    $dtllePadre = $obj_pf->detalle_Permiso_xIDs($idperfil,$idpadre);
                    if(is_array($dtllePadre)){
                        $insertDel = $obj_pf->delete_Permiso_xID($dtllePadre['id_permiso']);
                        if($insertDel){ $val = 1; }
                    }
                }
            }

            echo  json_encode(array('status'=>$val));

        } catch (PDOException $e) {
            throw $e;
        }
    }
}


