<?php
require_once '../dao/IdentificadorDAO.php';

class IdentificadorModel{

    public function lista_Identificador_All(){
        try {
            $obj_dao = new IdentificadorDAO();
            $lista = $obj_dao->lista_Identificador_All();
            return $lista;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function delete_Identificador_xID($id){
        try { $obj_model = new IdentificadorDAO();
            $delete = $obj_model->delete_Identificador_xID($id);
            return $delete;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function anular_Identificador_xID($id){
        try { $obj_model = new IdentificadorDAO();
            $anular = $obj_model->anular_Identificador_xID($id);
            return $anular;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function insert_Identificador($datos){
        try { $obj_model = new IdentificadorDAO();
            $register = $obj_model->insert_Identificador($datos);
            return $register;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function searching_Identificador_xDocument($datos){
        try {
            $obj_dao = new IdentificadorDAO();
            $detalle = $obj_dao->searching_Identificador_xDocument($datos);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function update_Identificador_validate($datos){
        try { $obj_model = new IdentificadorDAO();
            $update = $obj_model->update_Identificador_validate($datos);
            return $update;
        } catch (PDOException $e) {
            throw $e;
        }
    }
}
