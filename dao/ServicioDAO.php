<?php
require_once '../ds/AccesoDB.php';

class ServicioDAO{

    public function lst_servicios_Asignados_Activos_xIDUS($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM servicio_usuario WHERE id_us = :id AND condicion_su = 1";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$id, PDO::PARAM_INT);
            $stm->execute();
            $lista = $stm->fetchAll(PDO::FETCH_ASSOC);
            if(!$lista){$lista = null;}
            $stm = null;
            return $lista;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function lst_Servicio_xGerencia_Usuario($idge,$idus){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT se.*, su.id_su FROM servicio se 
                        INNER JOIN servicio_usuario su ON se.id_serv = su.id_serv 
                      WHERE su.id_us = :idus AND se.id_ge = :idge AND se.condicion_serv = 1";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idge",$idge, PDO::PARAM_INT);
            $stm->bindParam(":idus",$idus, PDO::PARAM_INT);
            $stm->execute();
            $lista = $stm->fetchAll(PDO::FETCH_ASSOC);
            if(!$lista){$lista = null;}
            $stm = null;
            return $lista;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function lst_Servicio_Activos_xGerencia_All($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM servicio WHERE id_ge = :id AND condicion_serv = 1";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$id, PDO::PARAM_INT);
            $stm->execute();
            $lista = $stm->fetchAll(PDO::FETCH_ASSOC);
            if(!$lista){$lista = null;}
            $stm = null;
            return $lista;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function detalle_ServicioUsuario_xIDSU($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM servicio_usuario WHERE id_su = :id LIMIT 1";
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

    public function registrar_Servicio_usuario_lastID($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
            //$pdo->beginTransaction();
            $query = "INSERT INTO servicio_usuario (id_serv,id_us,fechareg_su) VALUES (:idserv,:idus,:fecha)";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idserv",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":idus",$datos[1], PDO::PARAM_INT);
            $stm->bindParam(":fecha",$datos[2], PDO::PARAM_STR);
            $stm->execute();
            $id = $pdo->lastInsertId();
            return $id;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lst_servicios_noAsignados_xUsuario($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT serv.* FROM servicio serv WHERE 
            NOT EXISTS (SELECT NULL FROM servicio_usuario userv WHERE userv.id_serv = serv.id_serv AND userv.id_us = :id) 
            AND serv.condicion_serv = 1 ORDER BY serv.des_serv ASC";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$id, PDO::PARAM_INT);
            $stm->execute();
            $lista = $stm->fetchAll(PDO::FETCH_ASSOC);
            if(!$lista){$lista = null;}
            $stm = null;
            return $lista;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function lista_Servicios_Asignados_All_xIdUsuario($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM servicio_usuario WHERE id_us = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$id,PDO::PARAM_INT);
            $stm->execute();
            $lista = $stm->fetchAll(PDO::FETCH_ASSOC);
            if(!$lista){$lista = null;}
            $stm = null;
            return $lista;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function detalle_Servicio_xID($id){
        try{  $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM servicio WHERE id_serv = :id AND condicion_serv = 1";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$id,PDO::PARAM_INT);
            $stm->execute();
            $detalle = $stm->fetch(PDO::FETCH_ASSOC);
            if(!$detalle){$detalle = null;}
            $stm = null;
            return $detalle;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function detalle_Servicio_xIDUS($id){
        try{  $pdo = AccesoDB::getPDO();
            $query = "SELECT serv.* FROM servicio_usuario su INNER JOIN servicio serv ON su.id_serv = serv.id_serv WHERE su.id_su = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$id,PDO::PARAM_INT);
            $stm->execute();
            $detalle = $stm->fetch(PDO::FETCH_ASSOC);
            if(!$detalle){$detalle = null;}
            $stm = null;
            return $detalle;
        } catch(PDOException $e){
            throw $e;
        }
    }

}
