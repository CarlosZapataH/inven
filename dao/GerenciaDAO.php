<?php
require_once '../ds/AccesoDB.php';

class GerenciaDAO{

    public function lst_Gerencia_Activas(){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM gerencia WHERE condicion_ge = 1 ORDER BY des_ge ASC";
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

}
