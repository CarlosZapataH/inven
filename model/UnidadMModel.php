<?php
require_once '../dao/UnidadMDAO.php';

class UnidadMModel{

    public function listar_unidadM_All(){
        try {
            $obj_dao = new UnidadMDAO();
            $listar = $obj_dao->listar_unidadM_All();
            return $listar;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function busca_existenciaUM_xCodigo($cod){
        try {
            $obj_dao = new UnidadMDAO();
            $listar = $obj_dao->busca_existenciaUM_xCodigo($cod);
            return $listar;
        } catch (PDOException $e) {
            throw $e;
        }
    }
}
