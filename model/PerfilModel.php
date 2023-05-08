<?php
require_once '../dao/PerfilDAO.php';

class PerfilModel {

    public function lst_Perfil_All(){
        try { $obj_dao = new PerfilDAO();
            $listar = $obj_dao->lst_Perfil_All();
            return $listar;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_Perfil($datos){
        try { $obj_dao = new PerfilDAO();
            $reg = $obj_dao->registrar_Perfil($datos);
            return $reg;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function update_Estado_Perfil($datos){
        try { $obj_dao = new PerfilDAO();
            $update = $obj_dao->update_Estado_Perfil($datos);
            return $update;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function delete_Perfil_xID($id){
        try { $obj_dao = new PerfilDAO();
            $delete = $obj_dao->delete_Perfil_xID($id);
            return $delete;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function detalle_Perfil_xID($id){
        try { $obj_dao = new PerfilDAO();
            $detalle = $obj_dao->detalle_Perfil_xID($id);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function update_Perfil($datos){
        try { $obj_dao = new PerfilDAO();
            $update = $obj_dao->update_Perfil($datos);
            return $update;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lst_Perfil_Activos_All(){
        try { $obj_dao = new PerfilDAO();
            $listar = $obj_dao->lst_Perfil_Activos_All();
            return $listar;
        } catch (PDOException $e) {
            throw $e;
        }
    }

}
