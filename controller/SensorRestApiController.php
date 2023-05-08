<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);
require_once '../model/FuncionesModel.php';


$action = $_REQUEST["action"];
$controller = new SensorRestApiController();
call_user_func(array($controller,$action));

set_time_limit(0);
date_default_timezone_set("America/Lima");

class SensorRestApiController {

    public function index(){
        try {
            $fechaTimestamp = $_POST['timestamp'];
            $fechaTimestamp = $_POST['autoization'];

            $header = str_replace("Basic","",$fechaTimestamp);
            $api = Config::get("constans.API_KEY");
            if($api == $header){
                $time = 600 * 5;
                $elapseTome = 0;
                $fecha_bd = 0;
                $fech_actual = $fechaTimestamp;
                while ($fecha_bd <= $fech_actual){
                    $objHuella = "";
                    clearstatcache();

                }
            }
            else{

            }


            $ndoc = trim($_POST['ndoc_col']);
            $val = 0;
            $mesage = 'El número ingresado no pertenece a ningún colaborador registrado, verifique nuevamente el número de documento <code>(DNI/CEX)</code> y vuelva a intentarlo';
            $idntify = null;
            $obj_col = new ColaboradorModel();
            $obj_fn = new FuncionesModel();
            $dtlleCol = $obj_col->buscar_colaborador_xnDoc($ndoc);
            if(!is_null($dtlleCol)){
                $val = 1;
                $mesage = 'Colaborador validado';
                $idntify = $obj_fn->encrypt_decrypt("encrypt",$dtlleCol['id_col']);
            }

            echo json_encode(array('status'=>$val,'idntify'=>$idntify,'message'=>$mesage));

        } catch (PDOException $e) {
            throw $e;
        }
    }

}

