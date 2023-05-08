<?php
require_once '../ds/AccesoDB.php';

class ValeDAO{

    public function lst_Vale_All_Activas(){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM vale WHERE condicion_vale = 1 ORDER BY titulo_vale ASC";
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

    public function lst_Vale_All_Activas_exceptionActual($idActual){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM vale WHERE condicion_vale = 1 AND id_vale <> :valeActual ORDER BY titulo_vale ASC";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":valeActual",$idActual,PDO::PARAM_INT);
            $stm->execute();
            $lista = $stm->fetchAll(PDO::FETCH_ASSOC);
            if(!$lista){$lista = null;}
            $stm = null;
            return $lista;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function detalle_Vale_xID($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM vale WHERE id_vale = :id";
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
