<?php
require_once '../ds/AccesoDB.php';

class PersonaDAO{

    public function detalle_Persona_xID($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM persona WHERE id_per = :id";
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

    public function detalle_Persona_xIDUsuario($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT per.* FROM usuario us INNER JOIN persona per on us.id_per = per.id_per WHERE  us.id_us = :id LIMIT 1";
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

    public function buscar_Persona_xnDoc($ndoc){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM persona WHERE ndoc_per = :ndoc LIMIT 1";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":ndoc",$ndoc, PDO::PARAM_STR);
            $stm->execute();
            $detalle = $stm->fetch(PDO::FETCH_ASSOC);
            if(!$detalle){$detalle = null;}
            $stm = null;
            return $detalle;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function registrar_Persona($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
            //$pdo->beginTransaction();
            $query = "INSERT INTO persona (ape_pa_per,ape_ma_per,nombres_per,ndoc_per,email_per,cargo_per,avatar_per,area_servicio_per)
                      VALUES (:ape_pa, :ape_ma, :nombres, :ndoc, :email, :cargo, :avatar, :area)";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":ape_pa",$datos[0], PDO::PARAM_STR);
            $stm->bindParam(":ape_ma",$datos[1], PDO::PARAM_STR);
            $stm->bindParam(":nombres",$datos[2], PDO::PARAM_STR);
            $stm->bindParam(":ndoc",$datos[3], PDO::PARAM_STR);
            $stm->bindParam(":email",$datos[4], PDO::PARAM_STR);
            $stm->bindParam(":cargo",$datos[5], PDO::PARAM_STR);
            $stm->bindParam(":avatar",$datos[6], PDO::PARAM_INT);
            $stm->bindParam(":area",$datos[7], PDO::PARAM_STR);
            $stm->execute();
            $id = $pdo->lastInsertId();
            return $id;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function actualizar_Persona($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
            $pdo->beginTransaction();
            $query = "UPDATE persona SET ape_pa_per = :ape_pa, ape_ma_per = :ape_ma, nombres_per = :nombres, ndoc_per = :ndoc,
                             email_per = :email, cargo_per = :cargo, avatar_per = :avatar, area_servicio_per = :area
                      WHERE id_per = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":ape_pa",$datos[1], PDO::PARAM_STR);
            $stm->bindParam(":ape_ma",$datos[2], PDO::PARAM_STR);
            $stm->bindParam(":nombres",$datos[3], PDO::PARAM_STR);
            $stm->bindParam(":ndoc",$datos[4], PDO::PARAM_STR);
            $stm->bindParam(":email",$datos[5], PDO::PARAM_STR);
            $stm->bindParam(":cargo",$datos[6], PDO::PARAM_STR);
            $stm->bindParam(":avatar",$datos[7], PDO::PARAM_INT);
            $stm->bindParam(":area",$datos[8], PDO::PARAM_STR);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }
}
