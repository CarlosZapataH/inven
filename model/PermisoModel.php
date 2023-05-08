<?php
require_once '../dao/PermisoDAO.php';
error_reporting(E_ALL & ~E_NOTICE);

class PermisoModel {

    public function obtener_permisos($id,$opcion){
        try {
            $permiso_dao = new PermisoDAO();
            $listar = $permiso_dao->obtener_permisos($id,$opcion);
            return $listar;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function menus($opciones){
        try {
            if (is_array($opciones)){
                foreach ($opciones as $k=>$opcion){
                    if($opcion['estilo']=="menu-list"){
                        echo
                        '<li class="sub-menu dcjq-parent-li">';}
                    else {
                        echo
                        '<li>';}

                    if($opcion['estilo']=="menu-list"){
                            echo
                            '<a href="javascript:;" class="dcjq-parent"';}
                    else{
                            echo
                            '<a ';}

                    if ($opcion['url'] != "#"){
                        $badge = "";
                        if($opcion['badge'] != "") {
                                $badge =
                                '<span class="badge badge-'.$opcion['color'].' right-badge">'.$opcion['badge'].'</span>';}

                                echo
                                ' class="cursor-pointer classActive" href="'.$opcion['url'].'">'.
                                '<i class="'.$opcion['icon'].'"></i>'.
                                $opcion['abreviado'].
                                $badge.
                            '</a>';
                    }
                    else if ($opcion['url'] == "#"){
                                echo '>';
                        if($opcion['estilo']=="menu-list"){
                                echo
                                '<i class="'.$opcion['icon'].'"></i>'.
                                '<span>'.$opcion['abreviado'].'</span>'.
                                '<span class="dcjq-icon"></span>';
                        }
                        else {echo "";};
                        echo
                            '</a>';
                    }

                    if (is_array($opcion['opciones'])){
                        echo
                        '<ul class="sub" style="display: block;">';
                            $this->menus($opcion['opciones']);
                        echo
                        '</ul>';
                    }
                    echo '</li>';
                }
            }
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function listar_modulos($condicion=NULL,$order=NULL){
        try { $permi_dao = new PermisoDAO();
            $permi = $permi_dao->listar_modulos($condicion,$order);
            return $permi;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function listar_modulo_opcion($id_modulo_opcion,$condicion,$order){
        try { $LisModulo_dao = new PermisoDAO();
            $opcion = $LisModulo_dao->listar_modulo_opcion($id_modulo_opcion,$condicion,$order);
            return $opcion;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function eliminarRegistros($tabla,$condicion){
        try { $elimiDao = new PermisoDAO();
            $delete = $elimiDao->eliminarRegistros($tabla,$condicion);
            return $delete;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function actualizar($tabla,$campos,$condicion=''){
        try { $act_Dao = new PermisoDAO();
            $update = $act_Dao->actualizar($tabla,$campos,$condicion);
            return $update;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function listar_modulo_opciones($id_modulo_opcion,$condicion=NULL,$order=NULL){
        try { $lisM_Dao = new PermisoDAO();
            $list = $lisM_Dao->listar_modulo_opciones($id_modulo_opcion,$condicion,$order);
            return $list;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function delete_Permiso_xID($id){
        try { $obj_dao = new PermisoDAO();
            $del = $obj_dao->delete_Permiso_xID($id);
            return $del;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function detalle_Padre_permiso($idperfil,$idpadre){
        try { $obj_dao = new PermisoDAO();
            $detalle = $obj_dao->detalle_Padre_permiso($idperfil,$idpadre);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function detalle_Permiso_xIDs($idperfil,$idpadre){
        try { $obj_dao = new PermisoDAO();
            $detalle = $obj_dao->detalle_Permiso_xIDs($idperfil,$idpadre);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lst_modulosHijos_xIdPadrea($id){
        try { $obj_dao = new PermisoDAO();
            $lista = $obj_dao->lst_modulosHijos_xIdPadrea($id);
            return $lista;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lst_modulosHijos_xIdPadrea_sinhijoActual($idpadre,$hijoactual){
        try { $obj_dao = new PermisoDAO();
            $lista = $obj_dao->lst_modulosHijos_xIdPadrea_sinhijoActual($idpadre,$hijoactual);
            return $lista;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function detalle_moduloOpcion_xID($id){
        try { $obj_dao = new PermisoDAO();
            $detalle = $obj_dao->detalle_moduloOpcion_xID($id);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_permiso($datos){
        try { $obj_dao = new PermisoDAO();
            $insert = $obj_dao->registrar_permiso($datos);
            return $insert;
        } catch (PDOException $e) {
            throw $e;
        }
    }
}
