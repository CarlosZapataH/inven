<?php
require_once '../dao/AlmacenDAO.php';

class AlmacenModel{

    public function lst_Almacenes_All_Activos(){
        try {
            $obj_dao = new AlmacenDAO();
            $lista = $obj_dao->lst_Almacenes_All_Activos();
            return $lista;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function details_ultimo_carga_Almacen_xIDAlm($id){
        try { $obj_model = new AlmacenDAO();
            $detail = $obj_model->details_ultimo_carga_Almacen_xIDAlm($id);
            return $detail;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lst_Almacenes_Asignados_xUsuario($id){
        try {
            $obj_dao = new AlmacenDAO();
            $lista = $obj_dao->lst_Almacenes_Asignados_xUsuario($id);
            return $lista;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function detalle_Almacen_xID($id){
        try {
            $obj_dao = new AlmacenDAO();
            $detalle = $obj_dao->detalle_Almacen_xID($id);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_almacenes_Activos_xServicio_menosAlmActual($idserv,$idalm){
        try {
            $obj_dao = new AlmacenDAO();
            $lista = $obj_dao->lista_almacenes_Activos_xServicio_menosAlmActual($idserv,$idalm);
            return $lista;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function detalle_correlativo_Almacen($id){
        try {
            $obj_dao = new AlmacenDAO();
            $detalle = $obj_dao->detalle_correlativo_Almacen($id);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function actualizar_Correlativo_Almacen($datos){
        try { $obj_model = new AlmacenDAO();
            $update = $obj_model->actualizar_Correlativo_Almacen($datos);
            return $update;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function actualizar_Correlativo_NroVale_Autogenerado($id,$valor){
        try { $obj_model = new AlmacenDAO();
            $update = $obj_model->actualizar_Correlativo_NroVale_Autogenerado($id,$valor);
            return $update;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function actualizar_Correlativo_NroDespacho($id,$valor){
        try { $obj_model = new AlmacenDAO();
            $update = $obj_model->actualizar_Correlativo_NroDespacho($id,$valor);
            return $update;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_log_upload_Almacen($datos){
        try { $obj_model = new AlmacenDAO();
            $register = $obj_model->registrar_log_upload_Almacen($datos);
            return $register;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lst_almacenes_All_xServicio($idserv){
        try {
            $obj_dao = new AlmacenDAO();
            $lista = $obj_dao->lst_almacenes_All_xServicio($idserv);
            return $lista;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lst_almacenes_Activos_All_xServicio($idserv){
        try {
            $obj_dao = new AlmacenDAO();
            $lista = $obj_dao->lst_almacenes_Activos_All_xServicio($idserv);
            return $lista;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function update_Estado_Almacen($datos){
        try { $obj_model = new AlmacenDAO();
            $update = $obj_model->update_Estado_Almacen($datos);
            return $update;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function delete_Almacen_xID($id){
        try { $obj_model = new AlmacenDAO();
            $delete = $obj_model->delete_Almacen_xID($id);
            return $delete;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_Almacen($datos){
        try { $obj_model = new AlmacenDAO();
            $register = $obj_model->registrar_Almacen($datos);
            return $register;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_Almacen_Correlativo($id){
        try { $obj_model = new AlmacenDAO();
            $register = $obj_model->registrar_Almacen_Correlativo($id);
            return $register;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function update_Almacen($datos){
        try { $obj_model = new AlmacenDAO();
            $update = $obj_model->update_Almacen($datos);
            return $update;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_Almacen_Backup_lastID($datos){
        try { $obj_model = new AlmacenDAO();
            $register = $obj_model->registrar_Almacen_Backup_lastID($datos);
            return $register;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lst_Almacenes_All_Asignados_xUsuario($id){
        try {
            $obj_dao = new AlmacenDAO();
            $lista = $obj_dao->lst_Almacenes_All_Asignados_xUsuario($id);
            return $lista;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function delete_AlmacenUsuario_xID($id){
        try { $obj_model = new AlmacenDAO();
            $delete = $obj_model->delete_AlmacenUsuario_xID($id);
            return $delete;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function add_UsuarioAlmacen($datos){
        try { $obj_model = new AlmacenDAO();
            $agregar = $obj_model->add_UsuarioAlmacen($datos);
            return $agregar;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function busca_almacen_asignado($idsu,$idalm){
        try {
            $obj_dao = new AlmacenDAO();
            $busca = $obj_dao->busca_almacen_asignado($idsu,$idalm);
            return $busca;
        } catch (PDOException $e) {
            throw $e;
        }
    }

}
