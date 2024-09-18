<?php
require_once '../ds/AccesoDB.php';

class ColaboradorDAO{

    public function lst_Colaborador_xServicio($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM  colaborador WHERE id_serv= :id ORDER BY apa_col, ama_col, nombres_col ASC ";
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

    public function buscar_colaborador_xnDoc($ndoc){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM colaborador WHERE ndoc_col = :ndoc LIMIT 1";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":ndoc",$ndoc);
            $stm->execute();
            $detalle = $stm->fetch(PDO::FETCH_ASSOC);
            if(!$detalle){$detalle = null;}
            $stm = null;
            return $detalle;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function buscar_colaborador_xServicio($datos){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM colaborador WHERE id_serv = :id AND ndoc_col = :ndoc LIMIT 1";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$datos[0],PDO::PARAM_INT);
            $stm->bindParam(":ndoc",$datos[1]);
            $stm->execute();
            $detalle = $stm->fetch(PDO::FETCH_ASSOC);
            if(!$detalle){$detalle = null;}
            $stm = null;
            return $detalle;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function detalle_Colaborador_xId($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM  colaborador WHERE id_col = :id LIMIT 1";
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

    public function numero_Colaborador_xServicio($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT count(*) as registros FROM colaborador WHERE id_serv= :id LIMIT 1 ";
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

    public function update_Colaborador_Estado_xID($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "UPDATE colaborador SET  condicion_col = :condicion WHERE id_col = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":condicion",$datos[1], PDO::PARAM_INT);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function eliminar_Colaborador_xID($id) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "DELETE FROM colaborador WHERE id_col = :id";
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

    public function insert_Colaborador_StepOne($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
            $query = "INSERT INTO colaborador (id_serv,ndoc_col,apa_col,ama_col,nombres_col,cargo_col,servicio_col,areaop_col,fcontrato_col,legajo_col,email_col)
                      VALUES (:idServicio,:numberDoc,:apePaterno,:apeMaterno,:nombres,:cargo,:nameServicio,:areaOP,:fContrato,:nLegajo,:correo)";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idServicio",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":numberDoc",$datos[1]);
            $stm->bindParam(":apePaterno",$datos[2]);
            $stm->bindParam(":apeMaterno",$datos[3]);
            $stm->bindParam(":nombres",$datos[4]);
            $stm->bindParam(":cargo",$datos[5]);
            $stm->bindParam(":nameServicio",$datos[6]);
            $stm->bindParam(":areaOP",$datos[7]);
            $stm->bindParam(":fContrato",$datos[8]);
            $stm->bindParam(":nLegajo",$datos[9]);
            $stm->bindParam(":correo",$datos[10]);
            $stm->execute();
            return $pdo->lastInsertId();
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function insert_Colaborador($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "INSERT INTO colaborador (id_serv,ndoc_col,apa_col,ama_col,nombres_col,cargo_col,servicio_col,areaop_col,fcontrato_col,legajo_col,email_col)
                      VALUES (:idServicio,:numberDoc,:apePaterno,:apeMaterno,:nombres,:cargo,:nameServicio,:areaOP,:fContrato,:nLegajo,:correo)";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idServicio",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":numberDoc",$datos[1]);
            $stm->bindParam(":apePaterno",$datos[2]);
            $stm->bindParam(":apeMaterno",$datos[3]);
            $stm->bindParam(":nombres",$datos[4]);
            $stm->bindParam(":cargo",$datos[5]);
            $stm->bindParam(":nameServicio",$datos[6]);
            $stm->bindParam(":areaOP",$datos[7]);
            $stm->bindParam(":fContrato",$datos[8]);
            $stm->bindParam(":nLegajo",$datos[9]);
            $stm->bindParam(":correo",$datos[10]);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function update_Colaborador($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "UPDATE colaborador SET id_serv = :idServicio,ndoc_col = :numberDoc,apa_col = :apePaterno,ama_col = :apeMaterno,nombres_col = :nombres,
                       cargo_col = :cargo,servicio_col = :nameServicio,areaop_col = :areaOP,fcontrato_col = :fContrato,legajo_col = :nLegajo,email_col = :correo
                       WHERE id_col = :idColaborador";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idServicio",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":numberDoc",$datos[1]);
            $stm->bindParam(":apePaterno",$datos[2]);
            $stm->bindParam(":apeMaterno",$datos[3]);
            $stm->bindParam(":nombres",$datos[4]);
            $stm->bindParam(":cargo",$datos[5]);
            $stm->bindParam(":nameServicio",$datos[6]);
            $stm->bindParam(":areaOP",$datos[7]);
            $stm->bindParam(":fContrato",$datos[8]);
            $stm->bindParam(":nLegajo",$datos[9]);
            $stm->bindParam(":correo",$datos[10]);
            $stm->bindParam(":idColaborador",$datos[11], PDO::PARAM_INT);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function insert_Colaborador_StepTwo($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "UPDATE colaborador SET firma_col = :firmaAction, ipsign_col = :ipremote, imgsign_col = :dataImg, fechasign_col = :fechaSign WHERE id_col = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":firmaAction",$datos[1], PDO::PARAM_INT);
            $stm->bindParam(":ipremote",$datos[2]);
            $stm->bindParam(":dataImg",$datos[3]);
            $stm->bindParam(":fechaSign",$datos[4]);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_Colaborador($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "INSERT INTO colaborador (id_serv,ndoc_col,apa_col,ama_col,nombres_col,cargo_col,servicio_col,areaop_col,email_col)
                      VALUES (:idServicio,:numberDoc,:apePaterno,:apeMaterno,:nombres,:cargo,:nameServicio,:areaOP,:correo)";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idServicio",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":numberDoc",$datos[1]);
            $stm->bindParam(":apePaterno",$datos[2]);
            $stm->bindParam(":apeMaterno",$datos[3]);
            $stm->bindParam(":nombres",$datos[4]);
            $stm->bindParam(":cargo",$datos[5]);
            $stm->bindParam(":nameServicio",$datos[6]);
            $stm->bindParam(":areaOP",$datos[7]);
            $stm->bindParam(":correo",$datos[8]);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function actualizar_Colaborador($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "UPDATE colaborador SET ndoc_col = :numberDoc,apa_col = :apePaterno,ama_col = :apeMaterno,nombres_col = :nombres,
                      cargo_col = :cargo,areaop_col = :areaOP,email_col = :correo WHERE id_col = :idColaborador";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idColaborador",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":numberDoc",$datos[1]);
            $stm->bindParam(":apePaterno",$datos[2]);
            $stm->bindParam(":apeMaterno",$datos[3]);
            $stm->bindParam(":nombres",$datos[4]);
            $stm->bindParam(":cargo",$datos[5]);
            $stm->bindParam(":areaOP",$datos[6]);
            $stm->bindParam(":correo",$datos[7]);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function buscar_colaborador_xId($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM colaborador WHERE id_col = :id LIMIT 1";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$id);
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
