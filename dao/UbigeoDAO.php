<?php
require_once '../ds/AccesoDB.php';

class UbigeoDAO{

    public function detalle_ubigeo_xId($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT nombre_ubigeo FROM  ubigeo WHERE id_ubigeo = :id LIMIT 1";
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

    public function listar_departamentos_all(){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT id_ubigeo, nombre_ubigeo, codigo_ubigeo, etiqueta_ubigeo, buscador_ubigeo, numero_hijos_ubigeo, nivel_ubigeo, id_padre_ubigeo
                      FROM  ubigeo WHERE  id_padre_ubigeo = 2533 ORDER BY  nombre_ubigeo ASC";
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

    public function listar_hijos_IdPadre($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT id_ubigeo, nombre_ubigeo, codigo_ubigeo, etiqueta_ubigeo, buscador_ubigeo, numero_hijos_ubigeo, nivel_ubigeo, id_padre_ubigeo
                      FROM  ubigeo WHERE  id_padre_ubigeo = :id ORDER BY  nombre_ubigeo ASC ";
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


}
