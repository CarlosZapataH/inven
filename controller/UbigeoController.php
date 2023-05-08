<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);
require_once '../model/UbigeoModel.php';


$action = $_REQUEST["action"];
$controller = new UbigeoController();
call_user_func(array($controller,$action));

class UbigeoController {

    public function lista_Hijos_Ubigeo_xIdPadre_JSON(){
        try {
            $IdPadre = $_GET['id'];
            $obj_ubi = new UbigeoModel();
            $lstHijos = $obj_ubi->listar_hijos_IdPadre($IdPadre);

            $datos = array();
            if(!is_null($lstHijos)){
                foreach ($lstHijos as $hijo){
                    $row = array(
                        'id'=>$hijo['id_ubigeo'],
                        'texto'=>$hijo['nombre_ubigeo']

                    );
                    array_push($datos, $row);
                }
            }
            echo json_encode($datos);
        } catch (PDOException $e) {
            throw $e;
        }
    }

}

