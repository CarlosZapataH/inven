<?php
require_once '../dao/GerenciaDAO.php';

class GerenciaModel{

    public function lst_Gerencia_Activas(){
        try{  $dao = new GerenciaDAO();
            $listar = $dao->lst_Gerencia_Activas();
            return $listar;
        }catch(PDOException $e){
            throw $e;
        }
    }

}
