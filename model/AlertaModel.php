<?php
require_once '../dao/AlertaDAO.php';

class AlertaModel{

    public function registrar_Alerta_lastID($datos){
        try {
            $obj_dao = new AlertaDAO();
            $register = $obj_dao->registrar_Alerta_lastID($datos);
            return $register;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_Alerta_Detalle($datos){
        try {
            $obj_dao = new AlertaDAO();
            $register = $obj_dao->registrar_Alerta_Detalle($datos);
            return $register;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function maximo_ID_Alerta_Almacen($id){
        try {
            $obj_dao = new AlertaDAO();
            $detalle = $obj_dao->maximo_ID_Alerta_Almacen($id);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function busca_inventario_AlertaDetalle($idinv,$idalt){
        try {
            $obj_dao = new AlertaDAO();
            $detalle = $obj_dao->busca_inventario_AlertaDetalle($idinv,$idalt);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }

}
