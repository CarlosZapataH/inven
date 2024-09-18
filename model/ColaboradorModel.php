<?php
require_once '../dao/ColaboradorDAO.php';

class ColaboradorModel{

    public function lst_Colaborador_xServicio($id){
        try {
            $obj_dao = new ColaboradorDAO();
            $list = $obj_dao->lst_Colaborador_xServicio($id);
            return $list;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function buscar_colaborador_xnDoc($ndoc){
        try {
            $obj_dao = new ColaboradorDAO();
            $detalle = $obj_dao->buscar_colaborador_xnDoc($ndoc);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function buscar_colaborador_xServicio($datos){
        try {
            $obj_dao = new ColaboradorDAO();
            $detalle = $obj_dao->buscar_colaborador_xServicio($datos);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function detalle_Colaborador_xId($id){
        try {
            $obj_dao = new ColaboradorDAO();
            $detalle = $obj_dao->detalle_Colaborador_xId($id);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function numero_Colaborador_xServicio($id){
        try {
            $obj_dao = new ColaboradorDAO();
            $list = $obj_dao->numero_Colaborador_xServicio($id);
            return $list;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function update_Colaborador_Estado_xID($datos){
        try { $obj_model = new ColaboradorDAO();
            $update = $obj_model->update_Colaborador_Estado_xID($datos);
            return $update;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function eliminar_Colaborador_xID($datos){
        try { $obj_model = new ColaboradorDAO();
            $delete = $obj_model->eliminar_Colaborador_xID($datos);
            return $delete;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function insert_Colaborador_StepOne($datos){
        try { $obj_model = new ColaboradorDAO();
            $insert = $obj_model->insert_Colaborador_StepOne($datos);
            return $insert;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function insert_Colaborador($datos){
        try { $obj_model = new ColaboradorDAO();
            $insert = $obj_model->insert_Colaborador($datos);
            return $insert;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function update_Colaborador($datos){
        try { $obj_model = new ColaboradorDAO();
            $update = $obj_model->update_Colaborador($datos);
            return $update;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function insert_Colaborador_StepTwo($datos){
        try { $obj_model = new ColaboradorDAO();
            $update = $obj_model->insert_Colaborador_StepTwo($datos);
            return $update;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_Colaborador($datos){
        try { $obj_model = new ColaboradorDAO();
            $insert = $obj_model->registrar_Colaborador($datos);
            return $insert;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function actualizar_Colaborador($datos){
        try { $obj_model = new ColaboradorDAO();
            $update = $obj_model->actualizar_Colaborador($datos);
            return $update;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function buscar_colaborador_xId($id){
        try {
            $obj_dao = new ColaboradorDAO();
            $detalle = $obj_dao->buscar_colaborador_xId($id);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }
}
