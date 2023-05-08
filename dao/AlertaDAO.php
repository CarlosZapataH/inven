<?php
require_once '../ds/AccesoDB.php';

class AlertaDAO{

    public function registrar_Alerta_lastID($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
            $query = "INSERT INTO alerta(id_alm,email_alt,fecha_alt) VALUES (:idalm,:email,:fecha)";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idalm",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":email",$datos[1]);
            $stm->bindParam(":fecha",$datos[2]);
            $stm->execute();
            $id = $pdo->lastInsertId();
            return $id;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_Alerta_Detalle($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "INSERT INTO alerta_detalle_inventario(id_alt,id_inv,unidad_ali,cod_ali,cantidad_ali,des_ali,nroparte_ali,fecharec_ali,diastrans_ali)
                      VALUES (:idalt,:idinv,:unidad,:cod,:cantidad,:des,:nroparte,:fecharec,:diastrans)";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idalt",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":idinv",$datos[1], PDO::PARAM_INT);
            $stm->bindParam(":unidad",$datos[2]);
            $stm->bindParam(":cod",$datos[3]);
            $stm->bindParam(":cantidad",$datos[4]);
            $stm->bindParam(":des",$datos[5]);
            $stm->bindParam(":nroparte",$datos[6]);
            $stm->bindParam(":fecharec",$datos[7]);
            $stm->bindParam(":diastrans",$datos[8], PDO::PARAM_INT);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function maximo_ID_Alerta_Almacen($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT max(id_alt) as id FROM alerta WHERE id_alm = :id";
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

    public function busca_inventario_AlertaDetalle($idinv,$idalt){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM alerta_detalle_inventario WHERE id_alt = :idalt and id_inv = :idinv";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idinv",$idinv, PDO::PARAM_INT);
            $stm->bindParam(":idalt",$idalt, PDO::PARAM_INT);
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
