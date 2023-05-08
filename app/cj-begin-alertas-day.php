<?php
date_default_timezone_set("America/Lima");
setlocale(LC_TIME, 'es_PE.UTF-8');
require_once '../model/AlmacenModel.php';
require_once '../model/InventarioModel.php';
require_once '../model/AlertaModel.php';
require_once '../model/FuncionesModel.php';
require_once '../assets/plugins/phpspreadsheet-1.17.1.0/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../assets/plugins/PHPMailer-5.2.25/src/Exception.php';
require '../assets/plugins/PHPMailer-5.2.25/src/PHPMailer.php';
require '../assets/plugins/PHPMailer-5.2.25/src/SMTP.php';

try {
    $obj_fn = new FuncionesModel();
    $obj_alm = new AlmacenModel();
    $lstAlmacren = $obj_alm->lst_Almacenes_All_Activos();
    $dateNow = date("Y-m-d");
    if (is_array($lstAlmacren)) {
        $obj_inv = new InventarioModel();
        $obj_alt = new AlertaModel();
        foreach ($lstAlmacren as $almacen) {
            if((int)$almacen['semaforo_alm'] == 1){
                $maxIDAlerta = $obj_alt->maximo_ID_Alerta_Almacen($almacen['id_alm']);
                $listaInvent = $obj_inv->lista_inventario_xIdAlmacen($almacen['id_alm']);
                $arrayInventario = array();
                if(is_array($listaInvent)){
                    foreach ($listaInvent as $inventario){
                        if(!empty($inventario['fecharec_inv']) && trim($inventario['fecharec_inv']) != "0000-00-00" && !is_null($inventario['fecharec_inv'])) {
                            $semaforoAction = $obj_fn->semaforoInventario_tipo($inventario['fecharec_inv'], $almacen['verde_alm'], $almacen['amarillo_alm'], $almacen['rojo_alm']);
                            if(trim($semaforoAction) == "rojo"){
                                //verificamos si existe el registro como alerta
                                $buscaInventario = $obj_alt->busca_inventario_AlertaDetalle($inventario['id_inv'],$maxIDAlerta['id']);
                                if(is_null($buscaInventario)) {
                                    $diasTranscurridos = $obj_fn->dias_transc_semaforoInventario_tipo($inventario['fecharec_inv'], $almacen['verde_alm'], $almacen['amarillo_alm'], $almacen['rojo_alm']);
                                    $row = array(
                                        0 => $inventario['id_inv'],
                                        1 => $inventario['und_inv'],
                                        2 => $inventario['cod_inv'],
                                        3 => $inventario['cant_inv'],
                                        4 => $inventario['des_inv'],
                                        5 => $inventario['nroparte_inv'],
                                        6 => $inventario['fecharec_inv'],
                                        7 => $diasTranscurridos
                                    );
                                    array_push($arrayInventario, $row);
                                }
                            }
                        }
                    }
                }

                //Si existe registro en alerta roja
                if(is_array($arrayInventario) && sizeof($arrayInventario)>0){
                    $datesAlert[0] = $almacen['id_alm'];
                    $datesAlert[1] = $almacen['alerta_alm'];
                    $datesAlert[2] = date("Y-m-d");
                    $obj_alt = new AlertaModel();
                    $insertID = $obj_alt->registrar_Alerta_lastID($datesAlert);
                    if((int)$insertID > 0){
                        for ($i=0; $i<sizeof($arrayInventario); $i++) {
                            $datesItems[0] = $insertID;
                            $datesItems[1] = $arrayInventario[$i][0];
                            $datesItems[2] = $arrayInventario[$i][1];
                            $datesItems[3] = $arrayInventario[$i][2];
                            $datesItems[4] = $arrayInventario[$i][3];
                            $datesItems[5] = $arrayInventario[$i][4];
                            $datesItems[6] = $arrayInventario[$i][5];
                            $datesItems[7] = $arrayInventario[$i][6];
                            $datesItems[8] = $arrayInventario[$i][7];
                            $obj_alt->registrar_Alerta_Detalle($datesItems);
                        }
                    }

                    //Creamos el Excel
                    $spreadsheet = new Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();
                    //----- BEGIN ESTILOS ---------------
                    $st_Encabezado = [
                        'font' => [
                            'bold' => false,
                            'italic' => false,
                            'underline' => false,
                            'strikethrough' => false,
                            'color' => ['argb' => 'FFFFFF'],
                            'name' => "calibri",
                            'size' => 11
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                        ],
                        'borders' => [
                            'top' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => '000000']
                            ],
                            'bottom' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => '000000']
                            ],
                            'left' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => '000000']
                            ],
                            'right' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => '000000']
                            ]
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['argb' => '808080']
                        ]
                    ];

                    $st_Titulo = [
                        'font' => [
                            'bold' => true,
                            'italic' => false,
                            'underline' => false,
                            'strikethrough' => false,
                            'color' => ['argb' => '000000'],
                            'name' => "calibri",
                            'size' => 15
                        ],
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                        ]
                    ];

                    $st_borders = [
                        'borders' => [
                            'top' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => '000000']
                            ],
                            'bottom' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => '000000']
                            ],
                            'left' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => '000000']
                            ],
                            'right' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => '000000']
                            ]
                        ]
                    ];

                    //INSERTAMOS
                    $sheet->mergeCells('A1:G1');
                    $sheet->setCellValue('A1', "LISTA ALERTA INVENTARIO AL ".date("d/m/Y"));
                    $sheet->getStyle('A1:G1')->applyFromArray($st_Titulo);

                    $sheet->setCellValue('A3', "ALMACEN");
                    $sheet->mergeCells('B3:D3');
                    $sheet->setCellValue('B3', $almacen['titulo_alm']);
                    $sheet->getStyle('B3:D3')->applyFromArray($st_borders);

                    //creamos titulos para el reporte
                    $titulos = array(
                        0 => "UNIDAD",
                        1 => "CÓDIGO",
                        2 => "STOCK",
                        3 => "DESCRIPCIÓN",
                        4 => "NRO. PARTE",
                        5 => "FECHA RECEPCIÓN",
                        6 => "DÍAS TRANSC."
                    );

                    //INSERTAMOS INFORMACION
                    $sheet->setCellValue('A5', $titulos[0]);
                    $sheet->setCellValue('B5', $titulos[1]);
                    $sheet->setCellValue('C5', $titulos[2]);
                    $sheet->setCellValue('D5', $titulos[3]);
                    $sheet->setCellValue('E5', $titulos[4]);
                    $sheet->setCellValue('F5', $titulos[5]);
                    $sheet->setCellValue('G5', $titulos[6]);
                    $sheet->getStyle('A5:G5')->applyFromArray($st_Encabezado);

                    $sheet->getRowDimension(1)->setRowHeight(17);
                    $sheet->getRowDimension(2)->setRowHeight(15);
                    $sheet->getRowDimension(3)->setRowHeight(18);
                    $sheet->getRowDimension(4)->setRowHeight(10);
                    $sheet->getRowDimension(5)->setRowHeight(14);


                    //INSERTAMOS VALORES
                    $line = 6;
                    for ($i=0; $i<sizeof($arrayInventario); $i++){
                        $sheet->setCellValue('A' . $line, $arrayInventario[$i][1]);
                        $sheet->setCellValue('B' . $line, $arrayInventario[$i][2]);
                        $sheet->setCellValue('C' . $line, $arrayInventario[$i][3]);
                        $sheet->setCellValue('D' . $line, $arrayInventario[$i][4]);
                        $sheet->setCellValue('E' . $line, $arrayInventario[$i][5]);
                        $sheet->setCellValue('F' . $line, $obj_fn->fecha_ENG_ESP($arrayInventario[$i][6]));
                        $sheet->setCellValue('G' . $line, $arrayInventario[$i][7]);
                        $sheet->getStyle('A'.$line.':G'.$line)->applyFromArray($st_borders);
                        $sheet->getRowDimension($line)->setRowHeight(15);
                        $line++;
                    }

                    $sheet->getColumnDimension('A')->setWidth(13);
                    $sheet->getColumnDimension('B')->setWidth(26);
                    $sheet->getColumnDimension('C')->setWidth(20);
                    $sheet->getColumnDimension('D')->setWidth(60);
                    $sheet->getColumnDimension('E')->setWidth(17);
                    $sheet->getColumnDimension('F')->setWidth(17);
                    $sheet->getColumnDimension('G')->setWidth(17);

                    //Definimos el Zoom 92%
                    $sheet->setTitle("INVENTARIO");

                    //inmovilizamos la culumna
                    $sheet->freezePane('B8');

                    $ruta = "../assets/rpte_email/";
                    $archivo = $ruta."INVENTARIO-".date("Y-m-d").".xls";

                    $objWriter = IOFactory::createWriter($spreadsheet, "Xls");

                    if (file_exists($archivo)) {
                        unlink($archivo);
                    }
                    $objWriter->save($archivo);
                    chmod ($archivo, 0755);

                    //enviamos el email
                    if (file_exists($archivo)) {
                        try {
                            $emails = explode(";",trim($almacen['alerta_alm']));
                            $today = getdate();
                            $hora = $today["hours"];
                            if ($hora < 12) {
                                $saludo = " Buenos días ";
                            } else if ($hora <= 18) {
                                $saludo = "Buenas tardes ";
                            } else {
                                $saludo = "Buenas noches ";
                            }

                            $nameDay = $obj_fn->saber_dia($dateNow);


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
                            $mail->Subject = "Alerta Inventario Almacén - ".$nameDay;
                            $mail->ContentType = "text/plain";
                            //contenido de Mensaje
                            $mail->IsHTML(true);
                            $cuerpo = "Estimados<br>".$saludo."<br><br>Se emite la presente alerta para indicar aquellos items que se encuentran en proceso de alarma del Almacén:<b>".$almacen['titulo_alm']."</b> <br><br>Saludos<br><br>Soporte IMC";
                            $mail->msgHTML($cuerpo);
                            $mail->AddAttachment($archivo) ;
                            foreach ($emails as $e) {
                                // Destinatario del mensaje
                                $mail->addAddress($e);
                            }
                            $mail->addCC("israel.manrique@confipetrol.pe","Manrique, Israel Abdul");
                            $val = 0;
                            $mensaje = "Mensaje enviado satisfactoriamente";
                            if(!$mail->Send()) {
                                $mensaje = "Error al enviar el mensaje: " . $mail->ErrorInfo;
                            }
                            unlink($archivo);
                            echo $mensaje;


                        } catch (Exception $e) {
                            echo $e->getMessage(); //Boring error messages from anything else!
                        }
                    }
                }
            }
        }
    }
}
catch (PDOException $e) {
    throw $e;
}