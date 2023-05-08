<?php
require_once '../dao/MaterialDAO.php';

class MaterialModel{

    public function buscar_Material_xCodigo($id,$cod){
        try {
            $obj_dao = new MaterialDAO();
            $detalle = $obj_dao->buscar_Material_xCodigo($id,$cod);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_Despacho_lastID($datos){
        try { $obj_model = new MaterialDAO();
            $register = $obj_model->registrar_Despacho_lastID($datos);
            return $register;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function detalle_Despachos_xNumOperacion($id,$cod){
        try {
            $obj_dao = new MaterialDAO();
            $detalle = $obj_dao->detalle_Despachos_xNumOperacion($id,$cod);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }


    public function registrar_Despacho_Detalle($datos){
        try { $obj_model = new MaterialDAO();
            $register = $obj_model->registrar_Despacho_Detalle($datos);
            return $register;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_Despachos_xColaborador($id){
        try {
            $obj_dao = new MaterialDAO();
            $lista = $obj_dao->lista_Despachos_xColaborador($id);
            return $lista;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_Despachos_Rango_xColaborador($datos){
        try {
            $obj_dao = new MaterialDAO();
            $lista = $obj_dao->lista_Despachos_Rango_xColaborador($datos);
            return $lista;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_Consumos_Rango_xAlmacen($datos){
        try {
            $obj_dao = new MaterialDAO();
            $lista = $obj_dao->lista_Consumos_Rango_xAlmacen($datos);
            return $lista;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function detalle_Despacho_xID($id){
        try {
            $obj_dao = new MaterialDAO();
            $detalle = $obj_dao->detalle_Despacho_xID($id);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_Materiales_xIdDespacho($id){
        try {
            $obj_dao = new MaterialDAO();
            $lista = $obj_dao->lista_Materiales_xIdDespacho($id);
            return $lista;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_Despachos_Detalle_Historial_xColaborador($id){
        try {
            $obj_dao = new MaterialDAO();
            $lista = $obj_dao->lista_Despachos_Detalle_Historial_xColaborador($id);
            return $lista;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_Despachos_Detalle_xColaborador($id){
        try {
            $obj_dao = new MaterialDAO();
            $lista = $obj_dao->lista_Despachos_Detalle_xColaborador($id);
            return $lista;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_Despachos_Detalle_Rango_xColaborador($datos){
        try {
            $obj_dao = new MaterialDAO();
            $lista = $obj_dao->lista_Despachos_Detalle_Rango_xColaborador($datos);
            return $lista;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function anula_Despacho_xID($datos){
        try { $obj_model = new MaterialDAO();
            $update = $obj_model->anula_Despacho_xID($datos);
            return $update;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function anula_Despacho_Detalle_xIDDespacho($id){
        try { $obj_model = new MaterialDAO();
            $update = $obj_model->anula_Despacho_Detalle_xIDDespacho($id);
            return $update;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_Material_xAlmacen($id){
        try {
            $obj_dao = new MaterialDAO();
            $lista = $obj_dao->lista_Material_xAlmacen($id);
            return $lista;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function update_Material_Estado_xID($datos){
        try { $obj_model = new MaterialDAO();
            $update = $obj_model->update_Material_Estado_xID($datos);
            return $update;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function detalle_Material_xID($id){
        try {
            $obj_dao = new MaterialDAO();
            $detalle = $obj_dao->detalle_Material_xID($id);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function actualizar_actionDelete_Material_xID($id,$opc){
        try { $obj_model = new MaterialDAO();
            $update = $obj_model->actualizar_actionDelete_Material_xID($id,$opc);
            return $update;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function eliminar_Material_xID($datos){
        try { $obj_model = new MaterialDAO();
            $delete = $obj_model->eliminar_Material_xID($datos);
            return $delete;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function busca_existencia_codMaterial_xAlmacen($datos){
        try { $obj_model = new MaterialDAO();
            $detalle = $obj_model->busca_existencia_codMaterial_xAlmacen($datos);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function update_Material_xID($datos){
        try { $obj_model = new MaterialDAO();
            $update = $obj_model->update_Material_xID($datos);
            return $update;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function busca_codMaterial_xAlmacen($datos){
        try { $obj_model = new MaterialDAO();
            $detalle = $obj_model->busca_codMaterial_xAlmacen($datos);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_Material($datos){
        try { $obj_model = new MaterialDAO();
            $register = $obj_model->registrar_Material($datos);
            return $register;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_Material_xID_All($where){
        try {
            $obj_dao = new MaterialDAO();
            $lista = $obj_dao->lista_Material_xID_All($where);
            return $lista;
        } catch (PDOException $e) {
            throw $e;
        }
    }
}
