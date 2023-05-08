<?php
require_once '../dao/ServicioDAO.php';

class ServicioModel{

    public function lst_servicios_Asignados_Activos_xIDUS($id){
        try{
            $dao = new ServicioDAO();
            $listar = $dao->lst_servicios_Asignados_Activos_xIDUS($id);
            return $listar;
        }catch(PDOException $e){
            throw $e;
        }
    }

    public function lst_Servicio_xGerencia_Usuario($idge,$idus){
        try { $obje_dao = new ServicioDAO();
            $listar = $obje_dao->lst_Servicio_xGerencia_Usuario($idge,$idus);
            return $listar;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lst_Servicio_Activos_xGerencia_All($id){
        try { $obje_dao = new ServicioDAO();
            $listar = $obje_dao->lst_Servicio_Activos_xGerencia_All($id);
            return $listar;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function detalle_ServicioUsuario_xIDSU($id){
        try{
            $dao = new ServicioDAO();
            $detalle = $dao->detalle_ServicioUsuario_xIDSU($id);
            return $detalle;
        }catch(PDOException $e){
            throw $e;
        }
    }

    public function registrar_Servicio_usuario_lastID($datos){
        try {
            $obj_dao = new ServicioDAO();
            $register = $obj_dao->registrar_Servicio_usuario_lastID($datos);
            return $register;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lst_servicios_noAsignados_xUsuario($id){
        try{
            $dao = new ServicioDAO();
            $listar = $dao->lst_servicios_noAsignados_xUsuario($id);
            return $listar;
        }catch(PDOException $e){
            throw $e;
        }
    }

    public function lista_Servicios_Asignados_All_xIdUsuario($id){
        try {
            $obje_dao = new ServicioDAO();
            $listar = $obje_dao->lista_Servicios_Asignados_All_xIdUsuario($id);
            return $listar;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function detalle_Servicio_xID($id){
        try{  $dao = new ServicioDAO();
            $detalle = $dao->detalle_Servicio_xID($id);
            return $detalle;
        }catch(PDOException $e){
            throw $e;
        }
    }

    public function detalle_Servicio_xIDUS($id){
        try{  $dao = new ServicioDAO();
            $detalle = $dao->detalle_Servicio_xIDUS($id);
            return $detalle;
        }catch(PDOException $e){
            throw $e;
        }
    }
}
