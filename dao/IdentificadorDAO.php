<?php
require_once '../ds/AccesoDB.php';

class IdentificadorDAO{

    public function lista_Identificador_All(){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT *, date_format(creadoel_ic,'%d.%m.%y %H:%i') as creadoel FROM identificador_code ORDER BY id_ic DESC";
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

    public function delete_Identificador_xID($id) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "DELETE FROM identificador_code WHERE id_ic = :id";
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

    public function anular_Identificador_xID($id) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "UPDATE identificador_code SET condicion_ic = 0 WHERE id_ic = :id";
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

    public function insert_Identificador($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
            $query = "INSERT INTO identificador_code (id_us,name_us,id_col,ndoc_ic,name_ic,code_ic,creadoel_ic) VALUES (:idus,:nameus,:idcol,:numberdoc,:namecol,:codeidenty,:creadoel)";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idus",$datos[0]);
            $stm->bindParam(":nameus",$datos[1]);
            $stm->bindParam(":idcol",$datos[2]);
            $stm->bindParam(":numberdoc",$datos[3]);
            $stm->bindParam(":namecol",$datos[4]);
            $stm->bindParam(":codeidenty",$datos[5]);
            $stm->bindParam(":creadoel",$datos[6]);
            $stm->execute();
            return $pdo->lastInsertId();
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function searching_Identificador_xDocument($datos){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM identificador_code WHERE id_col = :idcol AND ndoc_ic = :ndoc AND code_ic = :codidenty AND asignadoa_ic IS NULL LIMIT 1";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idcol",$datos[0]);
            $stm->bindParam(":ndoc",$datos[1]);
            $stm->bindParam(":codidenty",$datos[2]);
            $stm->execute();
            $detalle = $stm->fetch(PDO::FETCH_ASSOC);
            if(!$detalle){$detalle = null;}
            $stm = null;
            return $detalle;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function update_Identificador_validate($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "UPDATE identificador_code SET asignadoa_ic = :numberdoc,asignadoel_ic = :fechasign,servicio_ic = :idservicio,codetransac_ic = :codetransac WHERE id_ic = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":numberdoc",$datos[1]);
            $stm->bindParam(":fechasign",$datos[2]);
            $stm->bindParam(":idservicio",$datos[3], PDO::PARAM_INT);
            $stm->bindParam(":codetransac",$datos[4]);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }
}
