<?php
require_once '../ds/AccesoDB.php';

class UsuarioDAO{

    public function lst_Usuarios_All(){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM usuario WHERE id_perfil <> 1";
            $stm = $pdo->prepare($query);
            $stm->execute();
            $lista = $stm->fetchAll(PDO::FETCH_ASSOC);
            if(!$lista){$lista = null;}
            $stm = null;
            return $lista;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function listar_Usuarios_Visualizacion_All(){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM usuario WHERE chpass_us = 1 AND tipo_us = 3 AND condicion_us = 1";
            $stm = $pdo->prepare($query);
            $stm->execute();
            $lista = $stm->fetchAll(PDO::FETCH_ASSOC);
            if(!$lista){$lista = null;}
            $stm = null;
            return $lista;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function Registrar_Usuario($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            //$pdo->beginTransaction();
            $query = "INSERT INTO usuario (id_perfil,id_per,pass_us,chpass_us,tipo_us,fechareg_us) VALUES (:perfil,:idper,:passw,:chpass,:tipo,:fecha)";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":perfil",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":idper",$datos[1], PDO::PARAM_INT);
            $stm->bindParam(":passw",$datos[2], PDO::PARAM_STR);
            $stm->bindParam(":chpass",$datos[3], PDO::PARAM_INT);
            $stm->bindParam(":tipo",$datos[4], PDO::PARAM_INT);
            $stm->bindParam(":fecha",$datos[5], PDO::PARAM_STR);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function Registrar_Usuario_lastID($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
            //$pdo->beginTransaction();
            $query = "INSERT INTO usuario (id_perfil,id_per,pass_us,chpass_us,tipo_us,fechareg_us) VALUES (:perfil,:idper,:passw,:chpass,:tipo,:fecha)";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":perfil",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":idper",$datos[1], PDO::PARAM_INT);
            $stm->bindParam(":passw",$datos[2], PDO::PARAM_STR);
            $stm->bindParam(":chpass",$datos[3], PDO::PARAM_INT);
            $stm->bindParam(":tipo",$datos[4], PDO::PARAM_INT);
            $stm->bindParam(":fecha",$datos[5], PDO::PARAM_STR);
            $stm->execute();
            $id = $pdo->lastInsertId();
            return $id;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function update_Estado_Usuario($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
            $pdo->beginTransaction();
            $query = "UPDATE usuario SET condicion_us = :estd WHERE id_us = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":estd",$datos[1], PDO::PARAM_INT);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function detalle_Usuario_xID($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM usuario WHERE id_us = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$id, PDO::PARAM_INT);
            $stm->execute();
            $detalle = $stm->fetch(PDO::FETCH_ASSOC);
            if(!$detalle){$detalle = null;}
            $stm = null;
            return $detalle;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function actualizar_Usuario($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
            $pdo->beginTransaction();
            $query = "UPDATE usuario SET id_perfil = :perfil, condicion_us = :estd WHERE id_us = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":perfil",$datos[1], PDO::PARAM_INT);
            $stm->bindParam(":estd",$datos[2], PDO::PARAM_INT);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function actualizar_Credenciales($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
            $pdo->beginTransaction();
            $query = "UPDATE usuario SET pass_us = :passw, chpass_us = :chpass, tipo_us = :tipo, fechareg_us = :fecha WHERE id_us = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":passw",$datos[1], PDO::PARAM_STR);
            $stm->bindParam(":chpass",$datos[2], PDO::PARAM_INT);
            $stm->bindParam(":tipo",$datos[3], PDO::PARAM_INT);
            $stm->bindParam(":fecha",$datos[4], PDO::PARAM_STR);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function Change_Password_Usuario_Default($datos){
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
            $pdo->beginTransaction();
            $query = "UPDATE usuario SET pass_us = :passw, chpass_us = :chpass, fechareg_us = :fecha WHERE id_us = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":passw",$datos[1], PDO::PARAM_STR);
            $stm->bindParam(":chpass",$datos[2], PDO::PARAM_STR);
            $stm->bindParam(":fecha",$datos[3], PDO::PARAM_STR);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function renovar_password_xUsuario($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
            $pdo->beginTransaction();
            $query = "UPDATE usuario SET chpass_us = :chpass, fechareg_us = :fecha WHERE id_us = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":chpass",$datos[1], PDO::PARAM_INT);
            $stm->bindParam(":fecha",$datos[2], PDO::PARAM_STR);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_Usuario_Almacen($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
            $pdo->beginTransaction();
            $query = "INSERT INTO usuario_almacen (id_su,id_alm,fechareg_ual) VALUES (:idsu,:idalm,:fecha)";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idsu",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":idalm",$datos[1], PDO::PARAM_INT);
            $stm->bindParam(":fecha",$datos[2], PDO::PARAM_STR);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function detalle_UsuarioServicio_xID($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM servicio_usuario WHERE id_su = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$id, PDO::PARAM_INT);
            $stm->execute();
            $detalle = $stm->fetch(PDO::FETCH_ASSOC);
            if(!$detalle){$detalle = null;}
            $stm = null;
            return $detalle;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function update_Estado_UsuarioServicio($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "UPDATE servicio_usuario SET condicion_su = :estd WHERE id_su = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":estd",$datos[1], PDO::PARAM_STR);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function delete_UsuarioServicio_xID($id) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "DELETE FROM servicio_usuario WHERE id_su = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$id, PDO::PARAM_INT);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function add_UsuarioServicio($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "INSERT INTO servicio_usuario (id_serv,id_us,fechareg_su) VALUES (:idserv,:idus,:fecha)";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idserv",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":idus",$datos[1], PDO::PARAM_INT);
            $stm->bindParam(":fecha",$datos[2], PDO::PARAM_STR);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }
}
