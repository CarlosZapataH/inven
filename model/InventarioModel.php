<?php
require_once '../dao/InventarioDAO.php';

class InventarioModel {

    public function listar_Inventario_xIDAlmacen_All($id){
        try { $obj_model = new InventarioDAO();
            $listar = $obj_model->listar_Inventario_xIDAlmacen_All($id);
            return $listar;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function nRegistros_Inventario_xAlmacen_All($id){
        try { $obj_model = new InventarioDAO();
            $detalle = $obj_model->nRegistros_Inventario_xAlmacen_All($id);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lst_ubicacion_Inventario_xIdAlm($id){
        try { $obj_model = new InventarioDAO();
            $listar = $obj_model->lst_ubicacion_Inventario_xIdAlm($id);
            return $listar;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function busca_existencia_Item_xDatos($datos){
        try { $obj_model = new InventarioDAO();
            $detalle = $obj_model->busca_existencia_Item_xDatos($datos);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function busca_existencia_Item_xCodMaterial($datos){
        try { $obj_model = new InventarioDAO();
            $detalle = $obj_model->busca_existencia_Item_xCodMaterial($datos);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function busca_existencia_codMaterial_xItem($datos){
        try { $obj_model = new InventarioDAO();
            $detalle = $obj_model->busca_existencia_codMaterial_xItem($datos);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_Item($datos){
        try { $obj_model = new InventarioDAO();
            $register = $obj_model->registrar_Item($datos);
            return $register;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_Item_calibracion($datos){
        try { $obj_model = new InventarioDAO();
            $register = $obj_model->registrar_Item_calibracion($datos);
            return $register;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_Item_lastID($datos){
        try { $obj_model = new InventarioDAO();
            $register = $obj_model->registrar_Item_lastID($datos);
            return $register;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function detalle_Item_xID($id){
        try {
            $obj_dao = new InventarioDAO();
            $detalle = $obj_dao->detalle_Item_xID($id);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function update_Item($datos){
        try { $obj_model = new InventarioDAO();
            $update = $obj_model->update_Item($datos);
            return $update;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_log_Actualizacion_Inventario($datos){
        try { $obj_model = new InventarioDAO();
            $register = $obj_model->registrar_log_Actualizacion_Inventario($datos);
            return $register;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_log_delete_Inventario($datos){
        try { $obj_model = new InventarioDAO();
            $register = $obj_model->registrar_log_delete_Inventario($datos);
            return $register;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function delete_Inventario_xID($id){
        try { $obj_model = new InventarioDAO();
            $delete = $obj_model->delete_Inventario_xID($id);
            return $delete;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function update_Condicion_Item($datos){
        try { $obj_model = new InventarioDAO();
            $update = $obj_model->update_Condicion_Item($datos);
            return $update;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_inventario_xIdAlmacen($id){
        try { $obj_model = new InventarioDAO();
            $listar = $obj_model->lista_inventario_xIdAlmacen($id);
            return $listar;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function actualizar_Stock_Item($datos){
        try { $obj_model = new InventarioDAO();
            $update = $obj_model->actualizar_Stock_Item($datos);
            return $update;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function listar_Inventario_xAlmacen_Rpte($id){
        try { $obj_model = new InventarioDAO();
            $listar = $obj_model->listar_Inventario_xAlmacen_Rpte($id);
            return $listar;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function cantidad_Repuesto_Inventario_xAlmacen_OM($id,$om){
        try {
            $obj_model = new InventarioDAO();
            $detalle = $obj_model->cantidad_Repuesto_Inventario_xAlmacen_OM($id,$om);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function listar_Inventario_xCorte_Rpte($id){
        try { $obj_model = new InventarioDAO();
            $listar = $obj_model->listar_Inventario_xCorte_Rpte($id);
            return $listar;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function cantidad_Repuesto_InventarioBK_xAlmacen_OM($id,$om){
        try { $obj_model = new InventarioDAO();
            $listar = $obj_model->cantidad_Repuesto_InventarioBK_xAlmacen_OM($id,$om);
            return $listar;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function listar_cortes_Inventario_backup_xAlmacen($datos){
        try { $obj_model = new InventarioDAO();
            $listar = $obj_model->listar_cortes_Inventario_backup_xAlmacen($datos);
            return $listar;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_inventario_backup($datos){
        try { $obj_model = new InventarioDAO();
            $register = $obj_model->registrar_inventario_backup($datos);
            return $register;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_inventario_xIdMovimiento($id){
        try { $obj_model = new InventarioDAO();
            $listar = $obj_model->lista_inventario_xIdMovimiento($id);
            return $listar;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_Clasificacion_Activos_All(){
        try { $obj_model = new InventarioDAO();
            $listar = $obj_model->lista_Clasificacion_Activos_All();
            return $listar;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function detalle_Clasificacion_xID($id){
        try { $obj_model = new InventarioDAO();
            $detalle = $obj_model->detalle_Clasificacion_xID($id);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_Transitos_Ingresos_Activos_xAlmacen($id){
        try { $obj_model = new InventarioDAO();
            $listar = $obj_model->lista_Transitos_Ingresos_Activos_xAlmacen($id);
            return $listar;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_Transitos_Salida_Activos_xAlmacen($de,$a){
        try { $obj_model = new InventarioDAO();
            $listar = $obj_model->lista_Transitos_Salida_Activos_xAlmacen($de,$a);
            return $listar;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function detalle_Transferencia_Transito_xID($id){
        try { $obj_model = new InventarioDAO();
            $detalle = $obj_model->detalle_Transferencia_Transito_xID($id);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_Transitos_Detalle_xAlmacen($id){
        try { $obj_model = new InventarioDAO();
            $listar = $obj_model->lista_Transitos_Detalle_xAlmacen($id);
            return $listar;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function actualizar_Transferencia_Transito_Estado_xID($datos){
        try { $obj_model = new InventarioDAO();
            $update = $obj_model->actualizar_Transferencia_Transito_Estado_xID($datos);
            return $update;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_inventarios_xCalibrar_xIdAlmacen($id){
        try { $obj_model = new InventarioDAO();
            $listar = $obj_model->lista_inventarios_xCalibrar_xIdAlmacen($id);
            return $listar;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_newFecha_Calibracion($datos){
        try { $obj_model = new InventarioDAO();
            $register = $obj_model->registrar_newFecha_Calibracion($datos);
            return $register;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function actualizar_fechaCalibracion_Inventario_xID($datos){
        try { $obj_model = new InventarioDAO();
            $update = $obj_model->actualizar_fechaCalibracion_Inventario_xID($datos);
            return $update;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_Calibraciones_xIdInventario($id){
        try { $obj_model = new InventarioDAO();
            $listar = $obj_model->lista_Calibraciones_xIdInventario($id);
            return $listar;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_Inventario_Baja_xID($datos){
        try { $obj_model = new InventarioDAO();
            $register = $obj_model->registrar_Inventario_Baja_xID($datos);
            return $register;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function actualizar_Estado_Inventario_xID($datos){
        try { $obj_model = new InventarioDAO();
            $update = $obj_model->actualizar_Estado_Inventario_xID($datos);
            return $update;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_Items_xBaja_xIdAlmacen($id){
        try { $obj_model = new InventarioDAO();
            $listar = $obj_model->lista_Items_xBaja_xIdAlmacen($id);
            return $listar;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_Depreciacion_Activo_xIdAlmacen($id){
        try { $obj_model = new InventarioDAO();
            $listar = $obj_model->lista_Depreciacion_Activo_xIdAlmacen($id);
            return $listar;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_Depreciacion_Activo_xAll(){
        try { $obj_model = new InventarioDAO();
            $listar = $obj_model->lista_Depreciacion_Activo_xAll();
            return $listar;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function actualizar_Depreciacion_xIdInventario($datos){
        try { $obj_model = new InventarioDAO();
            $update = $obj_model->actualizar_Depreciacion_xIdInventario($datos);
            return $update;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function update_Estado_Item_Transito($datos){
        try { $obj_model = new InventarioDAO();
            $update = $obj_model->update_Estado_Item_Transito($datos);
            return $update;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function update_Inventario_Reversa($datos){
        try { $obj_model = new InventarioDAO();
            $update = $obj_model->update_Inventario_Reversa($datos);
            return $update;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function listar_Inventario_Detail_Count($id){
        try { $obj_model = new InventarioDAO();
            $listar = $obj_model->listar_Inventario_Detail_Count($id);
            return $listar;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function listar_Inventario_Detail_All($id, $offset, $itemsPerPage, $search = null, $pagination = false){
        try { $obj_model = new InventarioDAO();
            $listar = $obj_model->listar_Inventario_Detail_All($id, $offset, $itemsPerPage, $search, $pagination);
            return $listar;
        } catch (PDOException $e) {
            throw $e;
        }
    }
}
