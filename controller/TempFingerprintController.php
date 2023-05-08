<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);
require_once '../model/TempFingerprintModel.php';
require_once '../model/FuncionesModel.php';

date_default_timezone_set("America/Lima");

$action = $_REQUEST["action"];
$controller = new TempFingerprintController();
call_user_func(array($controller,$action));

class TempFingerprintController {

    public function store_enroll() {
        $tokenPC = trim($_REQUEST['token_pc']);
        $obj_tfp = new TempFingerprintModel();
        $obj_tfp->delete_TempFingerPrint_xTokenPC($tokenPC);

        $id = strtotime("now");
        $TempFingerprint[0] = $id;
        $TempFingerprint[1] = $_REQUEST['user_id']; //user_id
        $TempFingerprint[2] = $_REQUEST['finger_name']; //finger_name
        $TempFingerprint[3] = $_REQUEST['token_pc']; //serial_number_pc
        $TempFingerprint[4] = "enroll"; //option
        $TempFingerprint[5] = date("Y-m-d H:i:s"); //created_at
        $result = $obj_tfp->save_TempFingerPrint_Enroll($TempFingerprint);

        return array("code" => $result, "message" => "Ok");
    }

    public function store_read() {
        $tokenPC = trim($_REQUEST['token_pc']);
        $obj_tfp = new TempFingerprintModel();
        $obj_tfp->delete_TempFingerPrint_xTokenPC($tokenPC);

        $id = strtotime("now");
        $TempFingerprint[0] = $id;
        $TempFingerprint[1] = $_REQUEST['token_pc']; //serial_number_pc
        $TempFingerprint[2] = "read"; //option
        $TempFingerprint[3] = date("Y-m-d H:i:s"); //created_at
        $result = $obj_tfp->save_TempFingerPrint_Read($TempFingerprint);

        return array("code" => $result, "message" => "Ok");
    }

    public function update() {
        $tokenPC = trim($_REQUEST['token_pc']);
        $obj_tfp = new TempFingerprintModel();
        $obj_tfp->delete_TempFingerPrint_xTokenPC($tokenPC);

        $TempFingerprint[0] = $tokenPC; //serial_number_pc
        $TempFingerprint[1] = null; //image
        $TempFingerprint[2] = "close"; //option
        $result = $obj_tfp->update_TempFingerPrint($TempFingerprint);
        return array("code" => $result, "message" => "Ok");
    }


}

