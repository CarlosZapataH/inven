<?php
require_once '../dao/ValeDAO.php';

class ValeModel{

    public function lst_Vale_All_Activas(){
        try{
            $dao = new ValeDAO();
            $listar = $dao->lst_Vale_All_Activas();
            return $listar;
        }catch(PDOException $e){
            throw $e;
        }
    }

    public function lst_Vale_All_Activas_exceptionActual($id){
        try{
            $dao = new ValeDAO();
            $listar = $dao->lst_Vale_All_Activas_exceptionActual($id);
            return $listar;
        }catch(PDOException $e){
            throw $e;
        }
    }

    public function detalle_Vale_xID($id){
        try{
            $dao = new ValeDAO();
            $detalle = $dao->detalle_Vale_xID($id);
            return $detalle;
        }catch(PDOException $e){
            throw $e;
        }
    }
}
