<?php
require_once '../dao/MovimientoDAO.php';

class MovimientoModel {

    public function registrar_Movimiento_Item_lastID($datos){
        try { $obj_model = new MovimientoDAO();
            $register = $obj_model->registrar_Movimiento_Item_lastID($datos);
            return $register;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_Movimiento_Item_Detalle($datos){
        try { $obj_model = new MovimientoDAO();
            $register = $obj_model->registrar_Movimiento_Item_Detalle($datos);
            return $register;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_MovimientoTransito_Item_lastID($datos){
        try { $obj_model = new MovimientoDAO();
            $register = $obj_model->registrar_MovimientoTransito_Item_lastID($datos);
            return $register;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_MovimientoTransito_Item_Detalle($datos){
        try { $obj_model = new MovimientoDAO();
            $register = $obj_model->registrar_MovimientoTransito_Item_Detalle($datos);
            return $register;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function listar_Movimientos_xAlmacen($where){
        try { $obj_model = new MovimientoDAO();
            $listar = $obj_model->listar_Movimientos_xAlmacen($where);
            return $listar;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function listar_Movimientos_xAlmacen_TRAExterno($where){
        try { $obj_model = new MovimientoDAO();
            $listar = $obj_model->listar_Movimientos_xAlmacen_TRAExterno($where);
            return $listar;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function detalle_Movimiento_xVale($datos){
        try { $obj_model = new MovimientoDAO();
            $detalle = $obj_model->detalle_Movimiento_xVale($datos);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function detalle_Movimiento_xID($id){
        try { $obj_model = new MovimientoDAO();
            $detalle = $obj_model->detalle_Movimiento_xID($id);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_MovimientoDetalle_xIdMovimiento($id){
        try { $obj_model = new MovimientoDAO();
            $listar = $obj_model->lista_MovimientoDetalle_xIdMovimiento($id);
            return $listar;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function detalle_MovimientoTransito_xID($id){
        try { $obj_model = new MovimientoDAO();
            $detalle = $obj_model->detalle_MovimientoTransito_xID($id);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_MovimientoTransitoDetalle_xIdMovimiento($id){
        try { $obj_model = new MovimientoDAO();
            $listar = $obj_model->lista_MovimientoTransitoDetalle_xIdMovimiento($id);
            return $listar;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_MovimientoDetalle_xIdInventario($id){
        try { $obj_model = new MovimientoDAO();
            $listar = $obj_model->lista_MovimientoDetalle_xIdInventario($id);
            return $listar;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function existe_MovimientoDetalle_xIdInventario($id){
        try { $obj_model = new MovimientoDAO();
            $listar = $obj_model->existe_MovimientoDetalle_xIdInventario($id);
            return $listar;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_MovimientosPT_cmpRecibido_xIdInventario($idAlm,$idinv){
        try { $obj_model = new MovimientoDAO();
            $listar = $obj_model->lista_MovimientosPT_cmpRecibido_xIdInventario($idAlm,$idinv);
            return $listar;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_Inventario_status_Calibracion_xMovimientos($idAlm,$idinv){
        try { $obj_model = new MovimientoDAO();
            $listar = $obj_model->lista_Inventario_status_Calibracion_xMovimientos($idAlm,$idinv);
            return $listar;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_Inventario_status_Calibracion_xMovimientosTRANSITO($idAlm,$idinv){
        try { $obj_model = new MovimientoDAO();
            $listar = $obj_model->lista_Inventario_status_Calibracion_xMovimientosTRANSITO($idAlm,$idinv);
            return $listar;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_Inventario_status_Calibracion_xMovimientosTRANSFER($idAlm,$idinv){
        try { $obj_model = new MovimientoDAO();
            $listar = $obj_model->lista_Inventario_status_Calibracion_xMovimientosTRANSFER($idAlm,$idinv);
            return $listar;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function delete_Movimiento_xID($id){
        try { $obj_model = new MovimientoDAO();
            $delete = $obj_model->delete_Movimiento_xID($id);
            return $delete;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_Movimientos_xNumVale($id,$nvale){
        try { $obj_model = new MovimientoDAO();
            $lista = $obj_model->lista_Movimientos_xNumVale($id,$nvale);
            return $lista;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function cantidad_devuelta_xIDInventario($datos){
        try { $obj_model = new MovimientoDAO();
            $detalle = $obj_model->cantidad_devuelta_xIDInventario($datos);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }
}
