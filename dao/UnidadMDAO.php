<?php
require_once '../ds/AccesoDB.php';

class UnidadMDAO{

    public function listar_unidadM_All(){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT cod_um, des_um FROM unidad_medida WHERE condicion_um = 1 ORDER BY des_um ASC";
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

    public function busca_existenciaUM_xCodigo($cod){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM unidad_medida WHERE condicion_um = 1 AND cod_um = :codMaterial";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":codMaterial",$cod);
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
