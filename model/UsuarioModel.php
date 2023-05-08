<?php
require_once '../dao/UsuarioDAO.php';

class UsuarioModel{

    public function lst_Usuarios_All(){
        try {
            $obj_dao = new UsuarioDAO();
            $lista = $obj_dao->lst_Usuarios_All();
            return $lista;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function listar_Usuarios_Visualizacion_All(){
        try {
            $obj_dao = new UsuarioDAO();
            $lista = $obj_dao->listar_Usuarios_Visualizacion_All();
            return $lista;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function Registrar_Usuario($datos){
        try { $obj_model = new UsuarioDAO();
            $register = $obj_model->Registrar_Usuario($datos);
            return $register;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function Registrar_Usuario_lastID($datos){
        try { $obj_model = new UsuarioDAO();
            $register = $obj_model->Registrar_Usuario_lastID($datos);
            return $register;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function update_Estado_Usuario($datos){
        try { $obj_model = new UsuarioDAO();
            $update = $obj_model->update_Estado_Usuario($datos);
            return $update;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function detalle_Usuario_xID($id){
        try {
            $obj_dao = new UsuarioDAO();
            $detalle = $obj_dao->detalle_Usuario_xID($id);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function actualizar_Usuario($datos){
        try { $obj_model = new UsuarioDAO();
            $update = $obj_model->actualizar_Usuario($datos);
            return $update;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function actualizar_Credenciales($datos){
        try { $obj_model = new UsuarioDAO();
            $update = $obj_model->actualizar_Credenciales($datos);
            return $update;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function Change_Password_Usuario_Default($datos){
        try {  $obje_dao = new UsuarioDAO();
            $update = $obje_dao->Change_Password_Usuario_Default($datos);
            return $update;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function renovar_password_xUsuario($datos){
        try { $obj_model = new UsuarioDAO();
            $update = $obj_model->renovar_password_xUsuario($datos);
            return $update;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_Usuario_Almacen($datos){
        try {
            $obj_dao = new UsuarioDAO();
            $register = $obj_dao->registrar_Usuario_Almacen($datos);
            return $register;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function detalle_UsuarioServicio_xID($id){
        try {
            $obj_dao = new UsuarioDAO();
            $detalle = $obj_dao->detalle_UsuarioServicio_xID($id);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function update_Estado_UsuarioServicio($datos){
        try { $obj_model = new UsuarioDAO();
            $update = $obj_model->update_Estado_UsuarioServicio($datos);
            return $update;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function delete_UsuarioServicio_xID($id){
        try {
            $obj_dao = new UsuarioDAO();
            $delete = $obj_dao->delete_UsuarioServicio_xID($id);
            return $delete;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function add_UsuarioServicio($datos){
        try { $obj_model = new UsuarioDAO();
            $agregar = $obj_model->add_UsuarioServicio($datos);
            return $agregar;
        } catch (PDOException $e) {
            throw $e;
        }
    }
}
