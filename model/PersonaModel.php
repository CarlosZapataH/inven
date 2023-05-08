<?php
require_once '../dao/PersonaDAO.php';

class PersonaModel{

    public function detalle_Persona_xID($id){
        try {
            $obj_dao = new PersonaDAO();
            $detalle = $obj_dao->detalle_Persona_xID($id);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function detalle_Persona_xIDUsuario($id){
        try {
            $obj_dao = new PersonaDAO();
            $detalle = $obj_dao->detalle_Persona_xIDUsuario($id);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function buscar_Persona_xnDoc($ndoc){
        try {
            $obj_dao = new PersonaDAO();
            $detalle = $obj_dao->buscar_Persona_xnDoc($ndoc);
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_Persona($datos){
        try { $obj_model = new PersonaDAO();
            $register = $obj_model->registrar_Persona($datos);
            return $register;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function actualizar_Persona($datos){
        try { $obj_model = new PersonaDAO();
            $update = $obj_model->actualizar_Persona($datos);
            return $update;
        } catch (PDOException $e) {
            throw $e;
        }
    }
}
