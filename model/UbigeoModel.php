<?php
require_once '../dao/UbigeoDAO.php';

class UbigeoModel{

    public function detalle_ubigeo_xId($id){
        try {
            $obj_dao = new UbigeoDAO();
            $detalle = $obj_dao->detalle_ubigeo_xId($id);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function listar_departamentos_all(){
        try {
            $obj_dao = new UbigeoDAO();
            $list = $obj_dao->listar_departamentos_all();
            return $list;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function listar_hijos_IdPadre($id){
        try {
            $obj_dao = new UbigeoDAO();
            $list = $obj_dao->listar_hijos_IdPadre($id);
            return $list;
        } catch (PDOException $e) {
            throw $e;
        }
    }
}
