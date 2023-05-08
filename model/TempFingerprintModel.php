<?php
require_once '../dao/TempFingerprintDAO.php';

class TempFingerprintModel{

    public function delete_TempFingerPrint_xTokenPC($token){
        try { $obj_model = new TempFingerprintDAO();
            $delete = $obj_model->delete_TempFingerPrint_xTokenPC($token);
            return $delete;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function save_TempFingerPrint_Enroll($datos){
        try { $obj_model = new TempFingerprintDAO();
            $register = $obj_model->save_TempFingerPrint_Enroll($datos);
            return $register;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function save_TempFingerPrint_Read($datos){
        try { $obj_model = new TempFingerprintDAO();
            $register = $obj_model->save_TempFingerPrint_Read($datos);
            return $register;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function update_TempFingerPrint($datos){
        try { $obj_model = new TempFingerprintDAO();
            $register = $obj_model->update_TempFingerPrint($datos);
            return $register;
        } catch (PDOException $e) {
            throw $e;
        }
    }







    public function lista_Identificador_All(){
        try {
            $obj_dao = new TempFingerprintDAO();
            $lista = $obj_dao->lista_Identificador_All();
            return $lista;
        } catch (PDOException $e) {
            throw $e;
        }
    }


    public function anular_Identificador_xID($id){
        try { $obj_model = new TempFingerprintDAO();
            $anular = $obj_model->anular_Identificador_xID($id);
            return $anular;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function searching_Identificador_xDocument($datos){
        try {
            $obj_dao = new TempFingerprintDAO();
            $detalle = $obj_dao->searching_Identificador_xDocument($datos);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function update_Identificador_validate($datos){
        try { $obj_model = new TempFingerprintDAO();
            $update = $obj_model->update_Identificador_validate($datos);
            return $update;
        } catch (PDOException $e) {
            throw $e;
        }
    }
}
