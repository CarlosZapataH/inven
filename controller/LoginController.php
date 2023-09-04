<?php
error_reporting(E_ALL & ~E_NOTICE);
session_start();
require_once '../model/LoginModel.php';
require_once '../model/FuncionesModel.php';
require_once '../assets/util/Session.php';

$action = $_REQUEST["action"];
$controller = new LoginController();
call_user_func(array($controller,$action));

class LoginController {

    public function valida_logueoUsuario(){
        file_put_contents(__DIR__ .'/../logs/log_'.date("j.n.Y").'-'.date("h.i.s").'.log', json_encode("error"), FILE_APPEND);
        try {
            $nroDoc = $_POST['txtNroDoc'];
            $clave = $_POST['txtPassword'];
            $codcaptcha = $_POST['codecaptcha'];
            $captcha = Session::getAttribute("captcha");
            $salt = '$bgr$/@.*¡¿]&';
            $passw = md5($salt.$clave);

            $obj_model = new LoginModel();
            $userReg = $obj_model->consulta_Logueo_xNroDocumento($nroDoc);

            if($userReg == NULL){
                throw new PDOException ('Usuario no Registrado.');}
            else if($passw != $userReg['pass_us']){
                throw new PDOException ("Clave Incorrecta.");}
            else if($userReg["condicion_us"] != 1){
                throw new PDOException ("El usuario no esta activo, consulte al administrador.");}
            else if($captcha['code'] != $codcaptcha){
                throw new PDOException ("Error en el Captcha");}
            else {
                session_start();
                $dates_usurious['id_us'] = $userReg['id_us'];
                $dates_usurious['ape_pa'] = $userReg['ape_pa_per'];
                $dates_usurious['ape_ma'] = $userReg['ape_ma_per'];
                $dates_usurious['nombres'] = $userReg['nombres_per'];
                $dates_usurious['nro_doc'] = $userReg['ndoc_per'];
                $dates_usurious['email'] = $userReg['email_per'];
                $dates_usurious['avatar'] = $userReg['avatar_per'];
                $dates_usurious['perfil'] = $userReg['id_perfil'];

                if((int)$userReg['chpass_us'] == 1) {
                    $dates_usurious['id_us'] = $userReg['id_us'];
                    Session::setAttribute("usuario", $dates_usurious);
                    Session::setAttribute("nombresesion", session_name("dantecas"));
                    Session::setAttribute("autentificado", "SI");
                    Session::setAttribute("ultimoAcceso", date("Y-n-j H:i:s"));
                    $target = "../app/sistema.php";
                }
                else if( (int)$userReg['chpass_us'] == 0 ) {
                    $obj_fn = new FuncionesModel();
                    $tokenID = $obj_fn->encrypt_decrypt('encrypt',$userReg['id_us']);
                    $dates_usurious['id_us'] = $tokenID;
                    Session::setAttribute("usuario_temp", $dates_usurious);
                    Session::setAttribute("nombresesion", session_name("dantemedina"));
                    Session::setAttribute("autentificado", "SI");
                    Session::setAttribute("ultimoAcceso", date("Y-n-j H:i:s"));
                    $target = "../app/password-new.php";
                }
            }

        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
            $target = "../index.php";
        }
        header("location: $target");

    }

}
