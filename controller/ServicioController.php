<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);
require_once '../model/GerenciaModel.php';
require_once '../model/ServicioModel.php';

$action = $_REQUEST["action"];
$controller = new ServicioController();
call_user_func(array($controller,$action));

class ServicioController {

    public function loadSelect_Servicio_JSON(){
        try {
            $obj_ge = new GerenciaModel();
            $lstGerencias = $obj_ge->lst_Gerencia_Activas();

            $datos = array();
            if(!is_null($lstGerencias)){
                $obj_serv = new ServicioModel();
                foreach ($lstGerencias as $gerencia) {
                    $lstServicios = $obj_serv->lst_Servicio_Activos_xGerencia_All($gerencia['id_ge']);
                    $values_group = array();
                    if (is_array($lstServicios)) {
                        foreach ($lstServicios as $servicio) {
                            $row_opt = array(
                                'id' => $servicio['id_serv'],
                                'texto' => $servicio['des_serv']
                            );
                            array_push($values_group, $row_opt);
                        }
                    }

                    $row = array(
                        'label' => strtoupper($gerencia['des_ge']),
                        'datos' => $values_group
                    );
                    array_push($datos, $row);
                }
            }
            echo json_encode($datos);
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

}


