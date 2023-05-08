<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);
require_once '../model/IdentificadorModel.php';
require_once '../model/ColaboradorModel.php';
require_once '../model/PersonaModel.php';
require_once '../model/FuncionesModel.php';

require '../assets/plugins/PHPMailer-5.2.25/src/Exception.php';
require '../assets/plugins/PHPMailer-5.2.25/src/PHPMailer.php';
require '../assets/plugins/PHPMailer-5.2.25/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;

$action = $_REQUEST["action"];
$controller = new IdentificadorController();
call_user_func(array($controller,$action));

class IdentificadorController {

    public function lista_Identificador_All(){
        try {
            $obj_id = new IdentificadorModel();
            $listCodes = $obj_id->lista_Identificador_All();
            $obj_fn = new FuncionesModel();

            $datos = array();
            if (is_array($listCodes)) {
                foreach ($listCodes as $codes) {

                    $optAnular = "";
                    $optDel = "";
                    $estado = '<span class="label label-block text-danger">ANULADO</span>';
                    $asignadoEl = "";
                    if((int)$codes['condicion_ic'] == 1) {
                        $estado = '<span class="label label-block text-success-600">ASIGNADO</span>';
                        if (empty($codes['asignadoa_ic'])) {
                            $estado = '<span class="label label-block text-teal-600">GENERADO</span>';
                            $optAnular = '<a class="cursor-pointer text-hover-waning" id="anularIdentify" data-id="' . $codes['id_ic'] . '" title="Anular"><i class="f30 opacity-7 ti-na"></i></a>';
                            $optDel = '<a class="cursor-pointer text-hover-danger ml-10" id="deleteIdentify" data-id="' . $codes['id_ic'] . '" title="Eliminar"><i class="f30 opacity-7 ti-trash"></i></a>';
                        }
                        else{
                            $date    = new DateTime($codes['asignadoel_ic']);
                            $asignadoEl = $date->format('d.m.y H:i');
                        }
                    }

                    $row = array(
                        0 => $codes['ndoc_ic'],
                        1 => $codes['name_ic'],
                        2 => $codes['code_ic'],
                        3 => $codes['creadoel'],
                        4 => $codes['asignadoa_ic'],
                        5 => $asignadoEl,
                        6 => $codes['servicio_ic'],
                        7 => $codes['codetransac_ic'],
                        8 => $estado,
                        9 => $optAnular.$optDel
                    );

                    array_push($datos, $row);
                }
            }

            $tabla = array('data' => $datos);
            echo json_encode($tabla);
            unset($datos);

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function generar_IdentifyCode_JSON(){
        try {
            date_default_timezone_set("America/Lima");
            setlocale(LC_TIME, 'es_PE.UTF-8');
            $obj_fn = new FuncionesModel();
            $numberDoc = trim($_POST['numberdoc']);
            $idUsuario = $obj_fn->encrypt_decrypt('decrypt',$_POST['idustk']);
            $obj_per = new PersonaModel();
            $dtllePersona = $obj_per->detalle_Persona_xIDUsuario($idUsuario);
            $namePersona = "";
            if(!is_null($dtllePersona)){
                $namePersona = $dtllePersona['ape_pa_per']." ".$dtllePersona['ape_ma_per'].", ".$dtllePersona['nombres_per']." ";
            }

            $obj_col = new ColaboradorModel();
            $dtlleCol = $obj_col->buscar_colaborador_xnDoc($numberDoc);

            $val = 0;
            $message = "Error al generar el código de Identificación.";
            if(!is_null($dtlleCol)){
                $nameCollaborator = $dtlleCol['apa_col']." ".$dtlleCol['ama_col'].", ".$dtlleCol['nombres_col'];


                $datesRegister[0] = (int)$idUsuario;
                $datesRegister[1] = $namePersona;
                $datesRegister[2] = (int)$dtlleCol['id_col'];
                $datesRegister[3] = trim($_POST['numberdoc']);
                $datesRegister[4] = $nameCollaborator;
                $datesRegister[5] = $obj_fn->getRandomCode();
                $datesRegister[6] = date("Y-m-d H:i:s");

                $obj_ic = new IdentificadorModel();
                $insertIdentify = $obj_ic->insert_Identificador($datesRegister);

                if($insertIdentify>0){
                    $val = 1;
                    $message = "Código de Identificación generado satisfactoriamente.";
                    $this->sendMail_createIdentificador($datesRegister);
                }
            }

            echo json_encode(array('status'=>$val,'message'=>$message,'identify'=>$insertIdentify));

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function sendMail_createIdentificador($datos){
        try {
            $today = getdate();
            $hora = $today["hours"];
            if ($hora < 12) {
                $saludo = " Buenos días ";
            } else if ($hora <= 18) {
                $saludo = "Buenas tardes ";
            } else {
                $saludo = "Buenas noches ";
            }

            $obj_fn = new FuncionesModel();
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->CharSet = "UTF-8";
            $mail->SMTPDebug = 2;
            // Configuración del servidor en modo seguro
            $mail->SMTPAuth = 'true';
            $mail->SMTPSecure = "STARTTLS";
            $mail->Host = "SMTP.Office365.com";
            $mail->Port = 587;
            $mail->Debugoutput = 'error_log';
            // Datos de autenticación
            $mail->Username = "soporte-imc@confipetrol.pe";
            $mail->Password = "Si22052018*";
            $mail->SetFrom ("soporte-imc@confipetrol.pe", "Soporte IMC");
            $mail->Subject = "ALERTA IDENTIFICADOR POR CÓDIGO GENERADO";
            $mail->ContentType = "text/plain";
            //contenido de Mensaje
            $mail->IsHTML(true);

            $fechaHora = explode(" ",$datos[6]);
            $fechaH =  $obj_fn->fecha_ENG_ESP($fechaHora[0]);
            $dateTime = $fechaH." ".$fechaHora[1];

            $courp = '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="m_-59907513642489685mott_formulario">';
            $courp .='<tbody>';
            $courp .='<tr>';
            $courp .='<td align="center" valign="top" style="padding:0;margin:0;background:#efeeed">';
            $courp .=' <table width="100%" border="0" cellpadding="0" cellspacing="0" style="width:100%;max-width:600px">';
            $courp .='      <tbody>';
            $courp .='         <tr>';
            $courp .='            <td align="center" valign="top" style="padding:0;margin:0;background:#002b4d;color:#fff">';
            $courp .='               <table border="0" align="center" cellpadding="0" cellspacing="0" style="width:90%;max-width:600px">';
            $courp .='                 <tbody>';
            $courp .='                     <tr>';
            $courp .='                      <td width="471" align="left" style="padding:7px;padding-left:10px;font-family:Helvetica,Arial,sans-serif;font-size:24px;font-weight:100;color:#FFFFFF;text-align:left; vertical-align: middle">';
            $courp .='							<span>Asistencia Técnica</span>';
            $courp .='						 </td>';
            $courp .='						 <td width="118" align="right" style="padding:7px;padding-right:10px;font-family:Helvetica,Arial,sans-serif;font-size:24px;font-weight:100;color:#FFFFFF;text-align:right; vertical-align: middle">';
            $courp .='							<span>IMC</span>';
            $courp .='						 </td>';
            $courp .='                     </tr>';
            $courp .='                  </tbody>';
            $courp .='               </table>';
            $courp .='            </td>';
            $courp .='         </tr>';
            $courp .='         <tr>';
            $courp .='            <td>';
            $courp .='               <table border="0" align="center" cellpadding="0" cellspacing="0" style="width:100%;max-width:600px">';
            $courp .='                  <tbody>';
            $courp .='                     <tr>';
            $courp .='                        <td align="center" style="background:#fff;padding-top:30px;padding-bottom:10px;line-height:30px;font-family:Helvetica,Arial,sans-serif;text-align:center;font-size:30px;font-family:Helvetica,Arial,sans-serif;font-weight:100;color:#515151">';
            $courp .='                           <span style="font-size:30px;font-family:Helvetica,Arial,sans-serif;font-weight:100;color:#515151"><b>CONTROL DE EPPS Y MATERIALES</b></span>';
            $courp .='                        </td>';
            $courp .='                     </tr>';
            $courp .='                   </tbody>';
            $courp .='                </table>';
            $courp .='             </td>';
            $courp .='          </tr>';
            $courp .='          <tr>';
            $courp .='            <td>';
            $courp .='               <table border="0" align="center" cellpadding="0" cellspacing="0" style="background:#fff;width:100%;max-width:600px">';
            $courp .='                  <tbody>';
            $courp .='                     <tr>';
            $courp .='                        <td>';
            $courp .='                           <table width="100%" border="0">';
            $courp .='                              <tbody><tr>';
            $courp .='                                <td align="center" style="padding-top:20px;padding-bottom:25px;line-height:24px;font-family:Helvetica,Arial,sans-serif;text-align:center;font-size:24px;font-family:Helvetica,Arial,sans-serif;font-weight:100;color:#515151">';
            $courp .='                                    <span style="font-size:24px;font-family:Helvetica,Arial,sans-serif;font-weight:100;color:#797979">';
            $courp .=                                        $saludo;
            $courp .='                                    </span><br>';
            $courp .='                                 </td>';
            $courp .='                              </tr>';
            $courp .='                           </tbody></table>';
            $courp .='                        </td>';
            $courp .='                    </tr>';
            $courp .='                     <tr>';
            $courp .='                        <td>';
            $courp .='                           <table width="100%" border="0">';
            $courp .='                             <tbody>';
            $courp .='                                 <tr>';
            $courp .='                                    <td align="center" style="padding-bottom:10px;line-height:21px;font-family:Helvetica,Arial,sans-serif;font-size:14px;text-align:center;color:#797979">';
            $courp .='                                       <span style="font-size:14px;font-family:Helvetica,Arial,sans-serif;color:#797979">';
            $courp .='                                        Se emite la siguiente alerta para indicar que se a generado un identificador por código para el siguiente personal:</span></td>';
            $courp .='                                 </tr>';
            $courp .='                                  <tr>';
            $courp .='                                   <td style="background:#f7f7f7;padding-top:10px;padding-bottom:10px ">';
            $courp .='									   <table border="0" align="center" style="background:#f7f7f7;width:100%;max-width:600px;font-size:14px;font-family:Helvetica,Arial,sans-serif;color:#797979">';
            $courp .='                                     <tbody>';
            $courp .='                                       <tr>';
            $courp .='                                         <td height="36" align="right" style="width:40%">Número documento : </td>';
            $courp .='                                         <td style="width:60%" align="left">';
            $courp .='											 <b style="font-size:20px;font-family:Helvetica,Arial,sans-serif;font-weight:100;color:#002b4d">'. $datos[3] .'</b>';
            $courp .='										   </td>';
            $courp .='                                       </tr>';
            $courp .='                                       <tr>';
            $courp .='                                         <td height="36" align="right" style="width:40%">Personal : </td>';
            $courp .='                                         <td style="width:60%" align="left">';
            $courp .='											 <b style="font-size:20px;font-family:Helvetica,Arial,sans-serif;font-weight:100;color:#002b4d">'. $datos[4] .'</b>';
            $courp .='										   </td>';
            $courp .='                                       </tr>';
            $courp .='                                       <tr>';
            $courp .='                                         <td height="36" align="right" style="width:40%">Identificador Generado : </td>';
            $courp .='                                         <td style="width:60%" align="left">';
            $courp .='											 <b style="font-size:20px;font-family:Helvetica,Arial,sans-serif;font-weight:100;color:#002b4d">'. $datos[5] .'</b>';
            $courp .='										   </td>';
            $courp .='                                       </tr>';
            $courp .='                                       <tr>';
            $courp .='                                          <td height="36" align="right" style="width:40%">Fecha/Hora : </td>';
            $courp .='                                          <td style="width:60%" align="left">';
            $courp .='											  <b style="font-size:20px;font-family:Helvetica,Arial,sans-serif;font-weight:100;color:#002b4d">'. $dateTime .'</b>';
            $courp .='										    </td>';
            $courp .='                                       </tr>';
            $courp .='                                     </tbody>';
            $courp .='                                   </table></td>';
            $courp .='                                 </tr>';
            $courp .='                                <tr>';
            $courp .='                                    <td align="center" style="padding:0 0 20px 0;line-height:12px;font-family:Helvetica,Arial,sans-serif;font-size:14px;text-align:center;color:#797979">';
            $courp .='                                       <span style="font-size:12px;font-family:Helvetica,Arial,sans-serif;color:#797979">';
            $courp .='                                            Por tanto, se pone en conocimiento para los fines que se estime conveniente.';
            $courp .='                                       </span>';
            $courp .='                                   </td>';
            $courp .='                                 </tr>';
            $courp .='                              </tbody>';
            $courp .='                           </table>';
            $courp .='                        </td>';
            $courp .='                     </tr>';
            $courp .='                  </tbody>';
            $courp .='               </table>';
            $courp .='			  </td>';
            $courp .='        </tr>';

            $courp  .='         <tr>';
            $courp  .='            <td>';
            $courp  .='               <table border="0" align="center" cellpadding="0" cellspacing="0" style="background:#fff;width:100%;max-width:600px">';
            $courp  .='                  <tbody>';
            $courp  .='                     <tr>';
            $courp  .='                        <td style="padding:10px">&nbsp;</td>';
            $courp  .='                     </tr>';
            $courp  .='                  </tbody>';
            $courp  .='               </table>';
            $courp  .='            </td>';
            $courp  .='         </tr>';

            $courp .='        <tr>';
            $courp .='            <td>';
            $courp .='               <table border="0" align="center" cellpadding="0" cellspacing="0" style="background:#002b4d;width:100%;max-width:600px">';
            $courp .='                  <tbody>';
            $courp .='                     <tr>';
            $courp .='                        <td style="padding-top:25px;padding-bottom:25px">';
            $courp .='                           <table width="100%" border="0">';
            $courp .='                              <tbody>';
            $courp .='                                 <tr>';
            $courp .='                                    <td align="center" valign="middle">';
            $courp .='                                      <span style="line-height:25px;font-family:Helvetica,Arial,sans-serif;font-size:14px;color:#fff">';
            $courp .='                                         Ingenieria de Mantenimiento y Confiabilidad - IMC<br>';
            $courp .='                                          Asistencia Técnica: soporte-imc@confipetrol.pe<br>';
            $courp .='                                          Lima - Perú';
            $courp .='                                          </span>';
            $courp .='                                    </td>';
            $courp .='                                 </tr>';
            $courp .='                              </tbody>';
            $courp .='                           </table>';
            $courp .='                       </td>';
            $courp .='                     </tr>';
            $courp .='                  </tbody>';
            $courp .='               </table>';
            $courp .='            </td>';
            $courp .='         </tr>';
            $courp .='      </tbody>';
            $courp .='   </table>';
            $courp .=' </td>';
            $courp .=' </tr>';
            $courp .=' </tbody>';
            $courp .='</table>';
            /*----------------------------------------------------------------------------------------------------*/
            $mail->msgHTML($courp);// Mensaje a enviar
            $mail->AltBody = "Usted esta viendo este mensaje simple debido a que su servidor de correo no admite formato HTML."; // Texto sin html

            // Destinatario del mensaje
            $mail->AddAddress ("fernando.macedo@confipetrol.pe","Macedo, Fernando Aquiles");
            $mail->AddCC("dante.castaneda@confipetrol.pe", "Castañeda, Dante Elmer");
            $exito = $mail->Send();

            $val = 0;
            if($exito) {  $val = 1; }

            return $val;

        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function delete_IdentifyCode_JSON(){
        try {
            $Identify = (int)$_POST['id'];
            $val = 0;
            $message = "Error al eliminar el código de Identificación.";
            $obj_ic = new IdentificadorModel();
            $deleteIdentify = $obj_ic->delete_Identificador_xID($Identify);
            if($deleteIdentify){
                $val = 1;
                $message = "Código de Identificación eliminado satisfactoriamente.";
            }

            echo json_encode(array('status'=>$val,'message'=>$message));

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function anular_IdentifyCode_JSON(){
        try {
            $Identify = (int)$_POST['id'];
            $val = 0;
            $message = "Error al anular el código de Identificación.";
            $obj_ic = new IdentificadorModel();
            $anulaIdentify = $obj_ic->anular_Identificador_xID($Identify);
            if($anulaIdentify){
                $val = 1;
                $message = "Código de Identificación anulado satisfactoriamente.";
            }

            echo json_encode(array('status'=>$val,'message'=>$message));

        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function load_view_securevalidation(){
        try {
            $idColaborador = $_POST['idcol'];
            $numberDoc = $_POST['ndoc'];
            $idServicio = $_POST['idserv'];
            $codTransac = $_POST['codtransac'];
            $obj_fn = new FuncionesModel();

            ?>
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header" style="display: block">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h5 class="modal-title text-center">Identificación Personal</h5>
                    </div>
                    <div class="modal-body no-padding">
                        <ul class="nav nav-pills nav-fill nav-pills-inverse mb-4 navTabValidation" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active show border-radius-0" data-toggle="tab" href="#tab-val1" data-option="fingerprint">
                                    <i class="fa fa-light fa-fingerprint fz-30" style="display: block"></i>
                                    Biometria
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link border-radius-0" data-toggle="tab" href="#tab-val2" data-option="identificador">
                                    <i class="fa fa-solid fa-hashtag fz-30" style="display: block"></i>
                                    Identificador
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active show container-fluid" id="tab-val1" role="tabpanel">
                                <div class="row">
                                    <div class="col-12 text-center mt-10">
                                        <button type="button" class="btn btn-square btn-outline-info">
                                            <i class="fa fa-thin fa-fingerprint position-left fz-18"></i>
                                            Activar Lector
                                        </button>
                                        <button type="button" class="btn btn-square btn-outline-danger ml-20">
                                            <i class="ti-close position-left fz-18"></i>
                                            Apagar Lector
                                        </button>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-group row">
                                    <div class="col-12 text-center">
                                        <div class="img">
                                            <img class="imgFinger" src="../assets/img/despacho/finger.png" />
                                        </div>
                                        <div class="txtFinger ct2 text-center" >***************</div>
                                        <div class="dataUser" >
                                            Identificacion: **********
                                            <br>
                                            Nombre: **********
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane container-fluid" id="tab-val2" role="tabpanel">
                                <form id="formSaveDespachoIdentify">
                                    <input type="hidden" name="idcol_idt" value="<?=$idColaborador?>"/>
                                    <input type="hidden" name="ndoc_idt" value="<?=$numberDoc?>"/>
                                    <input type="hidden" name="idserv_idt" value="<?=$idServicio?>"/>
                                    <input type="hidden" name="codtransac_idt" value="<?=$codTransac?>"/>
                                    <div class="flex flex-col mt-4 text-center">
                                        <span>Solicte el Código Identificador de Autorización al número:</span>
                                        <span class="font-bold">+51 915 138 476</span>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 text-center">
                                            <a class="btn btn-primary mb-10 btn-sm text-white btn-hover-transform " id="sendCodeIdentificator"
                                                data-ndoc="<?=$obj_fn->encrypt_decrypt('decrypt',$numberDoc)?>">
                                                <i class="fa fa-envelope-o position-left"></i>
                                                Solictar código identificador
                                            </a>
                                        </div>
                                    </div>

                                    <div id="containerTimer"></div>

                                    <div id="divError_i"></div>
                                    <div id="otp" class="flex flex-row justify-center text-center px-2">
                                        <input class="m-2 border h-10 w-10 text-center form-control rounded fz-17 inputOtp" type="text" id="first" name="otp[]" maxlength="1" autocomplete="off" oninput='digitValidate(this)' onkeyup='tabChange(1);sga.funcion.mayus(this)' required/>
                                        <input class="m-2 border h-10 w-10 text-center form-control rounded fz-17 inputOtp" type="text" id="second" name="otp[]" maxlength="1" autocomplete="off" oninput='digitValidate(this)' onkeyup='tabChange(2);sga.funcion.mayus(this)' required/>
                                        <input class="m-2 border h-10 w-10 text-center form-control rounded fz-17 inputOtp" type="text" id="third" name="otp[]" maxlength="1" autocomplete="off" oninput='digitValidate(this)' onkeyup='tabChange(3);sga.funcion.mayus(this)' required/>
                                        <input class="m-2 border h-10 w-10 text-center form-control rounded fz-17 inputOtp" type="text" id="fourth" name="otp[]" maxlength="1" autocomplete="off" oninput='digitValidate(this)' onkeyup='tabChange(4);sga.funcion.mayus(this)' required/>
                                        <input class="m-2 border h-10 w-10 text-center form-control rounded fz-17 inputOtp" type="text" id="fifth" name="otp[]" maxlength="1" autocomplete="off" oninput='digitValidate(this)' onkeyup='tabChange(5);sga.funcion.mayus(this)' required/>
                                        <input class="m-2 border h-10 w-10 text-center form-control rounded fz-17 inputOtp" type="text" id="sixth" name="otp[]" maxlength="1" autocomplete="off" oninput='digitValidate(this)' onkeyup='tabChange(6);sga.funcion.mayus(this)' required/>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 text-center">
                                            <span class="text-muted text-center fz-12">Código compuesto por 6 dígitos alfanuméricos</span>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-group row">
                                        <div class="col-12 text-center">
                                            <button type="submit" class="btn bg-success-600 btn-hover-transform" id="btnValidate" disabled>
                                                <i class="ti-check position-left"></i>
                                                Validar
                                            </button>
                                            <button type="button" class="btn btn-default ml-15" id="btnCancel_Modal">
                                                <i class="ti-close position-left"></i>
                                                Cancelar
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        } catch (PDOException $e) {
            throw  $e;
        }
    }

    public function saving_IdentifyPersonal_JSON(){
        try {
            date_default_timezone_set("America/Lima");
            setlocale(LC_TIME, 'es_PE.UTF-8');
            $obj_fn = new FuncionesModel();
            $idServicio = (int)$_POST['idserv_idt'];
            $codTransac = trim($_POST['codtransac_idt']);
            $numberDoc = trim($obj_fn->encrypt_decrypt('decrypt',$_POST['ndoc_idt']));
            $codeIdentify = implode("",$_POST['otp']);

            $datosSearch[0] = (int)$obj_fn->encrypt_decrypt('decrypt',$_POST['idcol_idt']);
            $datosSearch[1] = $numberDoc;
            $datosSearch[2] = $codeIdentify;
            $val = 0;
            $message = "Error al validar el identificador, verifique el código ingresado.";
            $obj_ic = new IdentificadorModel();
            $searchIdentify = $obj_ic->searching_Identificador_xDocument($datosSearch);
            if(!is_null($searchIdentify)){
                $datesUpdate[0] = $searchIdentify['id_ic'];
                $datesUpdate[1] = $numberDoc;
                $datesUpdate[2] = date("Y-m-d H:i:s");
                $datesUpdate[3] = $idServicio;
                $datesUpdate[4] = $codTransac;
                $updateIdentify = $obj_ic->update_Identificador_validate($datesUpdate);
                if($updateIdentify){
                    $val = 1;
                    $message = "Código de Identificación Validado satisfactoriamente, se procederá a registrar el despacho.";
                }
            }

            echo json_encode(array('status'=>$val,'message'=>$message,'code'=>$codeIdentify,'tipovalida'=>"CODEIDENTIFY",'fechavalida'=>date("Y-m-d H:i:s")));

        } catch (PDOException $e) {
            throw $e;
        }
    }


}

