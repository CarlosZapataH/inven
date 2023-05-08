<?php
date_default_timezone_set("America/Lima");
setlocale(LC_TIME, 'es_PE.UTF-8');
require_once '../model/FuncionesModel.php';
require_once '../model/MaterialModel.php';
require_once '../model/ColaboradorModel.php';
require_once '../model/PersonaModel.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../assets/plugins/PHPMailer-5.2.25/src/Exception.php';
require '../assets/plugins/PHPMailer-5.2.25/src/PHPMailer.php';
require '../assets/plugins/PHPMailer-5.2.25/src/SMTP.php';

require_once('../assets/plugins/tcpdf/tcpdf.php');

try {
    //Vertical A4
    $width = 210;
    $height = 279;

    class MYPDF extends TCPDF {

        public function Header() {
            // Obtener el margen de salto de página actual
            $bMargin = $this->getBreakMargin();

            // Obtener el modo actual de salto de página automático
            $auto_page_break = $this->AutoPageBreak;

            // Deshabilitar el salto de página automático
            $this->SetAutoPageBreak(false, 0);

            // Defina la ruta a la imagen que desea usar como marca de agua.
            $img_file = '../assets/img/despacho/sello-anulado.jpg';

            // Renderizar la imagen
            $this->Image($img_file, -5, 90, 223, 140, '', '', 'M', false, 300, '', false, false, 0);

            // Restaurar el estado de salto de página automático
            $this->SetAutoPageBreak($auto_page_break, $bMargin);

            // Establecer el punto de partida para el contenido de la página
            $this->setPageMark();
        }

        function setDatos_Despacho($idDespacho){
            $obj_mat = new MaterialModel();
            $detalleDespacho = $obj_mat->detalle_Despacho_xID($idDespacho);

            $annulledEstate = false;
            if((int)$detalleDespacho['condicion_des'] == 0){ $annulledEstate = true; }
            $this->SetPrintHeader($annulledEstate);
            $this->SetPrintFooter(false);

            $obj_f = new FuncionesModel();

            if(!is_null($detalleDespacho)) {
                if (trim($detalleDespacho['tipodespacho_des']) == "EPPS") {
                    $this->AddPage('P', 'A4'); //(  210 x 279  ) mm
                    $this->SetAutoPageBreak(false, 0);
                    $this->setFontSubsetting(false);

                    $totalColab = 0;
                    $obj_col = new ColaboradorModel();
                    $nregistrosCol = $obj_col->numero_Colaborador_xServicio($detalleDespacho['id_serv']);
                    if (!is_null($nregistrosCol)) {
                        $totalColab = (int)$nregistrosCol['registros']; // esto esta mal
                    }

                    $detalleRegistrador = $obj_col->buscar_colaborador_xnDoc(trim($detalleDespacho['ndoccreadopor_des']));
                    $regisCargo = "";
                    $regisFirma = "";
                    if(!is_null($detalleRegistrador)){
                        $regisCargo = trim($detalleRegistrador['cargo_col']);
                        $regisFirma = trim($detalleRegistrador['imgsign_col']);;
                    }

                    $detalleTrabajador = $obj_col->buscar_colaborador_xnDoc(trim($detalleDespacho['ndoc_des']));
                    $img1 = "";
                    if(!is_null($detalleTrabajador)){
                        if(!is_null($detalleTrabajador['imgsign_col'])){
                            $imageContent1 = file_get_contents(trim($detalleTrabajador['imgsign_col']));
                            $path1 = tempnam(sys_get_temp_dir(), 'prefix');
                            file_put_contents($path1, $imageContent1);
                            $img1 = '<img src="' . $path1 . '">';

                        }
                    }

                    /****************** HEADER ********************/
                    $this->SetXY(5, 5);
                    $this->SetFont('times', 'B', 7);
                    $this->setCellPaddings(0, 0, 0, 0.2);
                    $this->Cell(40, 16, "", 1, 1, 'C', 0, '', 0,false,'','B');
                    $this->Image('../assets/img/reporte/confi.png', 12.2, 7, 25, 12, '', '', '', true, 300, '', false, false, 0);

                    $this->SetFont('times', 'B', 9);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->SetXY(45, 5);
                    $this->Cell(123, 8, "CONFIPETROL", 'TRB', 1, 'C', 0, '', 0,false,'','M');
                    $this->SetXY(45, 13);
                    $this->Cell(123, 8, "REGISTRO DE EQUIPOS DE SEGURIDAD", 'RB', 1, 'C', 0, '', 0,false,'','M');

                    $this->SetFont('times', 'B', 9);
                    $this->setCellPaddings(1, 0.5, 2, 0.5);
                    $this->MultiCell(37, 16, "Código: HSEQ-S&SO1-F-79\nVersión: 1\nFecha: 03-10-2022\nPág.: 1 de 1", 1, 'L', 0, 1, 168, 5, true, 0, false, true, 16, 'M', true);

                    /**************** Código Registro *********************/
                    $this->SetXY(5, 23);
                    $this->SetFont('times', 'B', 7);
                    $this->SetFillColor(189,215,238);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(35, 7, "N° DE REGISTRO:", 1, 0, 'C', 1, '', 0,false,'','M');

                    $this->SetXY(40, 23);
                    $this->SetFont('times', '', 8);
                    $this->SetFillColor(255,255,255);
                    $this->setCellPaddings(1, 0, 0, 0);
                    $this->Cell(30, 7, str_pad($detalleDespacho['codigo_des'],6,"0",STR_PAD_LEFT), 1, 1, 'C', 0, '', 0,false,'','M');


                    /**************** Datos del Empleador *********************/
                    $this->SetXY(5, 32);
                    $this->SetFont('times', 'B', 7);
                    $this->SetFillColor(189,215,238);
                    $this->setCellPaddings(1, 0, 0, 0);
                    $this->Cell(200, 7, "DATOS DEL EMPLEADOR", 1, 0, 'L', 1, '', 0,false,'','M');

                    $this->SetXY(5, 39);
                    $this->SetFont('times', 'B', 6);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(50, 7, "RAZÓN SOCIAL", 'LBR', 0, 'C', 0, '', 0,false,'','M');

                    $this->SetXY(5, 46);
                    $this->SetFont('times', '', 7);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(50, 7, "CONFIPETROL ANDINA SA", 'LR', 0, 'C', 0, '', 0,false,'','M');

                    $this->SetXY(55, 39);
                    $this->SetFont('times', 'B', 6);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(25, 7, "RUC", 'BR', 0, 'C', 0, '', 0,false,'','M');

                    $this->SetXY(55, 46);
                    $this->SetFont('times', '', 7);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(25, 7, "20357259976", "R", 0, 'C', 0, '', 0,false,'','M');

                    $this->SetXY(80, 39);
                    $this->SetFont('times', 'B', 6);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(70, 7, "DOMICILIO (Dirección, distrito, provincia, departamento)", 'BR', 0, 'C', 0, '', 0,false,'','M');

                    $this->SetXY(80, 46);
                    $this->SetFont('times', '', 7);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(70, 7, "AV. SANTO TORIBIO 173 TORRE REAL 102 SAN ISIDRO", 'R', 0, 'C', 0, '', 0,false,'','M');

                    $this->SetXY(150, 39);
                    $this->SetFont('times', 'B', 6);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(35, 7, "ACTIVIDAD ECONÓMICA", 'BR', 0, 'C', 0, '', 0,false,'','M');

                    $this->SetXY(150, 46);
                    $this->SetFont('times', '', 7);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(35, 7, "SERVICIO", 'R', 0, 'C', 0, '', 0,false,'','M');

                    $this->SetFont('times', 'B', 4);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->MultiCell(20, 7, "N° TRABAJADORES EN EL\nCENTRO LABORAL", 'BR', 'C', 0, 1, 185, 39, true, 1, false, true, 4, 'M', true);

                    $this->SetXY(185, 46);
                    $this->SetFont('times', '', 7);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(20, 7, $totalColab, 'R', 0, 'C', 0, '', 0,false,'','M');

                    /**************** Datos del Trabajador ********************/
                    $this->SetXY(5, 53);
                    $this->SetFont('times', 'B', 7);
                    $this->SetFillColor(189,215,238);
                    $this->setCellPaddings(1, 0, 0, 0);
                    $this->Cell(200, 7, "DATOS DEL TRABAJADOR", 1, 0, 'L', 1, '', 0,false,'','M');

                    $this->SetXY(5, 60);
                    $this->SetFont('times', 'B', 6);
                    $this->setCellPaddings(1, 0, 0, 0);
                    $this->Cell(75, 7, "NOMBRES Y APELLIDOS DEL TRABAJADOR:", "LR", 0, 'L', 0, '', 0,false,'','M');

                    $this->SetXY(5, 67);
                    $this->SetFont('times', '', 7);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(75, 7, mb_strtoupper($detalleDespacho['solicitadopor_des'], 'UTF-8'), "LR", 0, 'C', 0, '', 0,false,'','M');

                    $this->SetXY(80, 60);
                    $this->SetFont('times', 'B', 6);
                    $this->setCellPaddings(1, 0, 0, 0);
                    $this->Cell(23, 7, "DNI/CE:", "R", 0, 'L', 0, '', 0,false,'','M');

                    $this->SetXY(80, 67);
                    $this->SetFont('times', '', 7);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(23, 7, $detalleDespacho['ndoc_des'], "R", 0, 'C', 0, '', 0,false,'','M');

                    $this->SetXY(103, 60);
                    $this->SetFont('times', 'B', 6);
                    $this->setCellPaddings(1, 0, 0, 0);
                    $this->Cell(57, 7, "SERVICIO:", "R", 0, 'L', 0, '', 0,false,'','M');

                    $this->SetXY(103, 67);
                    $this->SetFont('times', '', 7);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(57, 7, mb_strtoupper($detalleDespacho['desserv_des'], 'UTF-8'), "R", 0, 'C', 0, '', 0,false,'','M');

                    $this->SetXY(160, 60);
                    $this->SetFont('times', 'B', 6);
                    $this->setCellPaddings(1, 0, 0, 0);
                    $this->Cell(45, 7, "ALMACÉN:", "R", 0, 'L', 0, '', 0,false,'','M');

                    $this->SetXY(160, 67);
                    $this->SetFont('times', '', 7);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(45, 7, mb_strtoupper($detalleDespacho['desalm_des'], 'UTF-8'), "R", 0, 'C', 0, '', 0,false,'','M');

                    /**************** Datos Tipo de Equipo ********************/
                    $this->SetXY(5, 74);
                    $this->SetFont('times', 'B', 7);
                    $this->SetFillColor(189,215,238);
                    $this->setCellPaddings(1, 0, 0, 0);
                    $this->Cell(200, 7, "TIPO DE EQUIPO DE SEGURIDAD O EMERGENCIA ENTREGADO", 1, 0, 'L', 1, '', 0,false,'','M');

                    $this->SetXY(5, 81);
                    $this->SetFont('times', 'B', 6);
                    $this->setCellPaddings(1, 0, 0, 0);
                    $this->Cell(140, 7, "EQUIPO DE PROTECCIÓN PERSONAL", "LBR", 0, 'C', 0, '', 0,false,'','M');

                    $this->SetXY(5, 88);
                    $this->SetFont('times', '', 7);
                    $this->setCellPaddings(1, 0, 0, 0);
                    $this->Cell(140, 7, "Casco, Lentes de Seguridad, protector auditivo, respirador y filtros, ropa de trabajo, guantes, zapatos de seguridad y otros", "LR", 0, 'C', 0, '', 0,false,'','M');

                    $this->SetXY(145, 81);
                    $this->SetFont('times', 'B', 6);
                    $this->setCellPaddings(1, 0, 0, 0);
                    $this->Cell(60, 7, "EQUIPO DE EMERGENCIA", "BR", 0, 'C', 0, '', 0,false,'','M');

                    $this->SetXY(145, 88);
                    $this->SetFont('times', '', 7);
                    $this->setCellPaddings(1, 0, 0, 0);
                    $this->Cell(60, 7, "*", "R", 0, 'C', 0, '', 0,false,'','M');

                    /**************** Títulos Campos *********************/
                    $this->SetXY(5, 95);
                    $this->SetFont('times', 'B', 6);
                    $this->SetFillColor(191,191,191);
                    $this->setCellPaddings(1, 0, 0, 0);
                    $this->Cell(200, 7,"NOMBRE DEL EQUIPO DE SEGURIDAD O EMERGENCIA ENTREGADOS:", 1, 0, 'C', 1, '', 0,false,'','M');

                    $this->SetXY(5, 102);
                    $this->SetFont('times', 'B', 6);
                    $this->setCellPaddings(1, 0, 0, 0);
                    $this->Cell(5, 7, "N°", "LBR", 0, 'C', 0, '', 0,false,'','M');

                    $this->SetXY(10, 102);
                    $this->SetFont('times', 'B', 6);
                    $this->setCellPaddings(1, 0, 0, 0);
                    $this->Cell(15, 7, "CODIGO", "BR", 0, 'C', 0, '', 0,false,'','M');

                    $this->SetXY(25, 102);
                    $this->SetFont('times', 'B', 6);
                    $this->setCellPaddings(1, 0, 0, 0);
                    $this->Cell(58, 7, "DESCRIPCION DEL EPP", "BR", 0, 'C', 0, '', 0,false,'','M');

                    $this->SetXY(83, 102);
                    $this->SetFont('times', 'B', 6);
                    $this->setCellPaddings(1, 0, 0, 0);
                    $this->Cell(8, 7, "U.M.", "BR", 0, 'C', 0, '', 0,false,'','M');

                    $this->SetXY(91, 102);
                    $this->SetFont('times', 'B', 6);
                    $this->setCellPaddings(1, 0, 0, 0);
                    $this->Cell(12, 7, "CANT.", "BR", 0, 'C', 0, '', 0,false,'','M');

                    $this->SetXY(103, 102);
                    $this->SetFont('times', 'B', 6);
                    $this->setCellPaddings(1, 0, 0, 0);
                    $this->Cell(42, 7, "AREA", "BR", 0, 'C', 0, '', 0,false,'','M');

                    $this->SetFont('times', 'B', 6);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->MultiCell(15, 7, "FECHA DE\nENTREGA", 'BR', 'C', 0, 1, 145, 102, true, 1, false, true, 7, 'M', true);

                    $this->SetFont('times', 'B', 5);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->MultiCell(23, 7, "FECHA DE RENOVACION\nESTIMADA", 'BR', 'C', 0, 1, 160, 102, true, 1, false, true, 7, 'M', true);

                    $this->SetXY(183, 102);
                    $this->SetFont('times', 'B', 6);
                    $this->setCellPaddings(1, 0, 0, 0);
                    $this->Cell(22, 7, "FIRMA", "BR", 0, 'C', 0, '', 0,false,'','M');

                    $liney = 109;
                    for($i=1;$i<=16;$i++){
                        $this->SetXY(5, $liney);
                        $this->setCellPaddings(0, 0, 0, 0);
                        $this->Cell(5, 9, $i, "LBR", 0, 'C', 0, '', 0,false,'','M');

                        $this->SetXY(10, $liney);
                        $this->Cell(15, 9, "", "BR", 0, 'C', 0, '', 0,false,'','M');

                        $this->SetXY(25, $liney);
                        $this->Cell(58, 9, "", "BR", 0, 'C', 0, '', 0,false,'','M');

                        $this->SetXY(83, $liney);
                        $this->Cell(8, 9, "", "BR", 0, 'C', 0, '', 0,false,'','M');

                        $this->SetXY(91, $liney);
                        $this->Cell(12, 9, "", "BR", 0, 'C', 0, '', 0,false,'','M');

                        $this->SetXY(103, $liney);
                        $this->Cell(42, 9, "", "BR", 0, 'C', 0, '', 0,false,'','M');

                        $this->SetXY(145, $liney);
                        $this->Cell(15, 9, "", "BR", 0, 'C', 0, '', 0,false,'','M');

                        $this->SetXY(160, $liney);
                        $this->Cell(23, 9, "", "BR", 0, 'C', 0, '', 0,false,'','M');

                        $this->SetXY(183, $liney);
                        $this->Cell(22, 9, "", "BR", 0, 'C', 0, '', 0,false,'','M');
                        $liney = $liney + 9;
                    }

                    $lstMateriales = $obj_mat->lista_Materiales_xIdDespacho($detalleDespacho['id_des']);
                    if(!is_null($lstMateriales)){
                        $line = 109;
                        foreach ($lstMateriales as $material){
                            $this->SetXY(10, $line);
                            $this->SetFont('times', '', 8);
                            $this->setCellPaddings(0, 0, 0, 0);
                            $this->Cell(15, 9, $material['codigo'], 0, 0, 'C', 0, '', 0,false,'','M');

                            $this->setCellPaddings(1, 0, 1,0 );
                            $this->MultiCell(58, 9, $material['descripcion'], 0, 'L', 0, 1, 25, $line, true, 1, false, true, 9, 'M', true);

                            $this->SetXY(83, $line);
                            $this->setCellPaddings(0, 0, 0, 0);
                            $this->Cell(8, 9, $material['unidadm'], 0, 0, 'C', 0, '', 0,false,'','M');

                            $this->SetXY(91, $line);
                            $this->setCellPaddings(0, 0, 0, 0);
                            $this->Cell(12, 9, $material['cantidad'], 0, 0, 'C', 0, '', 0,false,'','M');

                            $this->SetXY(103, $line);
                            $this->setCellPaddings(0, 0, 0, 0);
                            $this->Cell(42, 9, $material['area'], 0, 0, 'C', 0, '', 0,false,'','M');

                            $this->SetXY(145, $line);
                            $this->setCellPaddings(0, 0, 0, 0);
                            $this->Cell(15, 9, $material['fechaentrega'], 0, 0, 'C', 0, '', 0,false,'','M');

                            if((int)$material['periodo']> 0){
                                $this->SetXY(160, $line);
                                $this->Cell(23, 9, $material['periodo']." MESES", 0, 0, 'C', 0, '', 0,false,'','M');
                            }

                            /*
                            if(!empty($img1)) {
                                $this->writeHTMLCell(12, 9, 184, $line+0.3, $img1, 0, 0, 0, true, 'C', true);
                            }*/


                            $line = $line + 9;
                        }
                    }

                    /**************** Datos del Responsable Registro*********************/
                    $this->SetXY(5, 257);
                    $this->SetFont('times', 'B', 7);
                    $this->SetFillColor(189,215,238);
                    $this->setCellPaddings(1, 0, 0, 0);
                    $this->Cell(200, 7, "DATOS DEL RESPONSABLE DEL REGISTRO:", 1, 0, 'C', 1, '', 0,false,'','M');


                    $this->SetXY(5, 264);
                    $this->setCellPaddings(1, 0, 0, 0);
                    $this->Cell(35, 7, "NOMBRE", 1, 0, 'LBR', 0, '', 0,false,'','M');

                    $this->SetXY(40, 264);
                    $this->setCellPaddings(1, 0, 0, 0);
                    $this->Cell(143, 7, mb_strtoupper($detalleDespacho['creadopor_des'], 'UTF-8'), 'BR', 0, 'L', 0, '', 0,false,'','M');

                    $this->SetXY(183, 264);
                    $this->SetFont('times', '', 6);
                    $this->setCellPaddings(0, 0.5, 0, 0);
                    $this->Cell(22, 7, $detalleDespacho['ndoc_des'], 'R', 0, 'C', 0, '', 0,false,'','T');

                    $this->SetXY(5, 271);
                    $this->SetFont('times', 'B', 7);
                    $this->setCellPaddings(1, 0, 0, 0);
                    $this->Cell(35, 7, "CARGO", 'LBR', 0, 'L', 0, '', 0,false,'','M');

                    $this->SetXY(40, 271);
                    $this->setCellPaddings(1, 0, 0, 0);
                    $this->Cell(143, 7, mb_strtoupper($regisCargo, 'UTF-8'), 'BR', 0, 'L', 0, '', 0,false,'','M');

                    $this->SetXY(183, 271);
                    $this->setCellPaddings(1, 0, 0, 0);
                    $this->Cell(22, 7, "", 'R', 0, 'L', 0, '', 0,false,'','M');

                    $this->SetXY(5, 278);
                    $this->setCellPaddings(1, 0, 0, 0);
                    $this->Cell(35, 7, "FECHA", 'LBR', 0, 'L', 0, '', 0,false,'','M');

                    $this->SetXY(40, 278);
                    $this->setCellPaddings(1, 0, 0, 0);
                    $this->Cell(143, 7, $obj_f->fecha_ENG_ESP($detalleDespacho['fecha_des']), 'BR', 0, 'L', 0, '', 0,false,'','M');

                    $this->SetXY(183, 278);
                    $this->setCellPaddings(1, 0, 0, 0);
                    $this->Cell(22, 7, "", 'R', 0, 'L', 0, '', 0,false,'','M');

                    $this->SetXY(5, 285);
                    $this->setCellPaddings(1, 0, 0, 0);
                    $this->Cell(35, 7, "FIRMA", 'LBR', 0, 'L', 0, '', 0,false,'','M');

                    $this->SetXY(40, 285);
                    $this->setCellPaddings(1, 0, 0, 0);
                    $this->Cell(143, 7, "", 'BR', 0, 'L', 0, '', 0,false,'','M');

                    $this->SetXY(183, 285);
                    $this->SetFont('times', '', 5);
                    $this->setCellPaddings(0, 0, 0, 0.5);
                    $this->Cell(22, 7, "HUELLA DIGITAL", 'BR', 0, 'C', 0, '', 0,false,'','B');

                    /*
                    $imageContent = file_get_contents($regisFirma);
                    $path = tempnam(sys_get_temp_dir(), 'prefix');
                    file_put_contents ($path, $imageContent);
                    $img = '<img src="' . $path . '">';
                    $this->SetFillColor(255,255,255);
                    $this->writeHTMLCell(15, 5, 40, 286.2, $img, 0,0,0,true,'C',true);
*/

                }
                else {
                    $this->AddPage('P', 'A5'); //(   139.5 x 210 ) mm
                    $this->SetAutoPageBreak(false, 0);
                    $this->setFontSubsetting(false);

                    /****************** HEADER ********************/
                    $this->SetXY(5, 5);
                    $this->SetFont('times', 'B', 5);
                    $this->setCellPaddings(0, 0, 0, 0.2);
                    $this->Cell(30, 12, "RUC 20357259976", 1, 1, 'C', 0, '', 0,false,'','B');
                    $this->Image('../assets/img/reporte/confi.png', 8.5, 6, 22, 9, '', '', '', true, 300, '', false, false, 0);

                    $this->SetFont('times', 'B', 8);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->SetXY(35, 5);
                    $this->Cell(78.5, 6, "CONFIPETROL", 'TB', 0, 'C', 0, '', 0,false,'','M');
                    $this->SetXY(35, 11);
                    $this->Cell(78.5, 6, "VALE DE CAMPO", 'B', 0, 'C', 0, '', 0,false,'','M');
                    //138.5
                    $this->SetFont('times', 'B', 10);
                    $this->setCellPaddings(1, 0.5, 0.5, 0);
                    $this->MultiCell(30, 12, "Código: HSEQ-S&SO1-F-79\nVersión: 1\nFecha: 03-10-2022\nPág.: 1 de 1", 1, 'L', 0, 1, 113.5, 5, true, 0, false, true, 12, 'M', true);

                    /**************** *********************/
                    $this->SetXY(5, 22);
                    $this->SetFont('times', 'B', 10);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(138.5, 5, "N° REGISTRO ".str_pad($detalleDespacho['codigo_des'],3,"0",STR_PAD_LEFT), 0, 1, 'C', 0, '', 0,false,'','M');

                    /**************** *********************/
                    $this->SetXY(5, 29);
                    $this->SetFont('times', 'B', 6);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(15, 5, "ALMACEN:", 0, 1, 'L', 0, '', 0,false,'','M');

                    $this->SetXY(20, 29);
                    $this->SetFont('times', '', 7);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(31, 5, mb_strtoupper($detalleDespacho['desalm_des'], 'UTF-8'), "B", 1, 'L', 0, '', 1,false,'','M');

                    $this->SetXY(52, 29);
                    $this->SetFont('times', 'B', 6);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(19, 5, "OBRA/SERVICIO:", 0, 1, 'L', 0, '', 0,false,'','M');

                    $this->SetXY(71, 29);
                    $this->SetFont('times', '', 7);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(41, 5,  mb_strtoupper($detalleDespacho['desserv_des'], 'UTF-8'), "B", 1, 'L', 0, '', 1,false,'','M');

                    $this->SetXY(113, 29);
                    $this->SetFont('times', 'B', 6);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(10, 5, "FECHA:", 0, 1, 'L', 0, '', 0,false,'','M');

                    $this->SetXY(123, 29);
                    $this->SetFont('times', '', 7);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(20.5, 5, $obj_f->fecha_ENG_ESP($detalleDespacho['fecha_des']), 0, 1, 'L', 0, '', 0,false,'','M');

                    $this->SetXY(5, 34);
                    $this->SetFont('times', 'B', 6);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(12, 5, "DESTINO:", 0, 1, 'L', 0, '', 0,false,'','M');

                    $this->SetXY(17, 34);
                    $this->SetFont('times', '', 7);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(55, 5, "OPERACIONES", "B", 1, 'L', 0, '', 0,false,'','M');

                    $this->SetXY(5, 39);
                    $this->SetFont('times', 'B', 6);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(14, 5, "ENTREGAR:", 0, 1, 'L', 0, '', 0,false,'','M');

                    $this->SetXY(19, 39);
                    $this->SetFont('times', '', 7);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(60, 5, mb_strtoupper($detalleDespacho['solicitadopor_des'], 'UTF-8'), "B", 1, 'L', 0, '', 0,false,'','M');

                    $this->SetXY(80, 39);
                    $this->SetFont('times', 'B', 6);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(9, 5, "O.T. Nº:", 0, 1, 'L', 0, '', 0,false,'','M');

                    $this->SetXY(89, 39);
                    $this->SetFont('times', 'B', 6);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(30, 5, "", "B", 1, 'L', 0, '', 0,false,'','M');

                    $this->SetXY(5, 44);
                    $this->SetFont('times', 'B', 6);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(11, 5, "MOTIVO:", 0, 1, 'L', 0, '', 0,false,'','M');

                    $this->SetXY(16, 44);
                    $this->SetFont('times', '', 6);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(80, 5, "DESPACHO", "B", 1, 'L', 0, '', 0,false,'','M');

                    $this->SetXY(109, 45);
                    $this->SetFont('times', 'B', 7);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(10, 4, "X", 1, 1, 'C', 0, '', 0,false,'','M');

                    $this->SetXY(120, 45);
                    $this->SetFont('times', 'B', 6);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(23.5, 5, "VALE DE SALIDA", 0, 1, 'L', 0, '', 0,false,'','M');

                    $this->SetXY(5, 49);
                    $this->SetFont('times', 'B', 6);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(28, 5, "IMPUTACIÓN CONTABLE:", 0, 1, 'L', 0, '', 0,false,'','M');

                    $this->SetXY(33, 49);
                    $this->SetFont('times', '', 7);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(63, 5, "", "B", 1, 'L', 0, '', 0,false,'','M');

                    $this->SetXY(109, 49);
                    $this->SetFont('times', 'B', 9);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(10, 4, "", "RBL", 1, 'C', 0, '', 0,false,'','M');

                    $this->SetXY(120, 49);
                    $this->SetFont('times', 'B', 6);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(23.5, 5, "VALE DE INGRESO", 0, 1, 'L', 0, '', 0,false,'','M');

                    $this->SetXY(5, 54);
                    $this->SetFont('times', 'B', 6);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(15, 5, "N° RESERVA:", 0, 1, 'L', 0, '', 0,false,'','M');

                    $this->SetXY(20, 54);
                    $this->SetFont('times', '', 7);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(76, 5, "", "B", 1, 'L', 0, '', 0,false,'','M');

                    /**************** *********************/
                    $this->SetXY(5, 64);
                    $this->SetFont('times', 'B', 6);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(98.5, 4, "ARTÍCULO", 1, 1, 'C', 0, '', 0,false,'','M');

                    $this->SetXY(103.5, 64);
                    $this->SetFont('times', 'B', 6);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(10, 4, "UNIDAD", "TRB", 1, 'C', 0, '', 0,false,'','M');

                    $this->SetXY(113.5, 64);
                    $this->SetFont('times', 'B', 6);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(30, 4, "CANTIDAD", "TRB", 1, 'C', 0, '', 0,false,'','M');

                    $this->SetFont('times', 'B', 6);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->MultiCell(18, 6, "CODIGO", "LBR", 'C', 0, 1, 5, 68, true, 1, false, true, 6, 'M', true);
                    $this->MultiCell(7, 6, "ITEM", "BR", 'C', 0, 1, 23, 68, true, 1, false, true, 6, 'M', true);
                    $this->MultiCell(73.5, 6, "DESCRIPCION", "BR", 'C', 0, 1, 30, 68, true, 1, false, true, 6, 'M', true);
                    $this->MultiCell(10, 6, "MAT.", "BR", 'C', 0, 1, 103.5, 68, true, 1, false, true, 6, 'M', true);
                    $this->MultiCell(15, 6, "SOLICITADO\nA", "BR", 'C', 0, 1, 113.5, 68, true, 1, false, true, 6, 'M', true);
                    $this->MultiCell(15, 6, "ENTREGADO\nA", "BR", 'C', 0, 1, 128.5, 68, true, 1, false, true, 6, 'M', true);

                    $linea = 74;
                    $hight = 8;
                    $this->SetFont('times', '', 7);
                    $this->setCellPaddings(0, 0, 0, 0);
                    for($i=0; $i<=13; $i++){
                        $this->MultiCell(18, $hight, "", "LBR", 'C', 0, 1, 5, $linea, true, 1, false, true, $hight, 'M', true);
                        $this->MultiCell(7, $hight, ($i+1), "BR", 'C', 0, 1, 23, $linea, true, 1, false, true, $hight, 'M', true);
                        $this->MultiCell(73.5, $hight, "", "BR", 'C', 0, 1, 30, $linea, true, 1, false, true, $hight, 'M', true);
                        $this->MultiCell(10, $hight, "", "BR", 'C', 0, 1, 103.5, $linea, true, 1, false, true, $hight, 'M', true);
                        $this->MultiCell(15, $hight, "", "BR", 'C', 0, 1, 113.5, $linea, true, 1, false, true, $hight, 'M', true);
                        $this->MultiCell(15, $hight, "", "BR", 'C', 0, 1, 128.5, $linea, true, 1, false, true, $hight, 'M', true);
                        $linea = $linea + $hight;
                    }

                    $lstMateriales = $obj_mat->lista_Materiales_xIdDespacho($detalleDespacho['id_des']);
                    if(!is_null($lstMateriales)){
                        $line = 74;
                        $h = 8;
                        $this->SetFont('times', '', 7);

                        foreach ($lstMateriales as $material){
                            $this->setCellPaddings(0, 0, 0, 0);
                            $this->MultiCell(18, $h, $material['codigo'], 0, 'C', 0, 1, 5, $line, true, 1, false, true, $h, 'M', true);
                            $this->setCellPaddings(1, 0, 0, 0);
                            $this->MultiCell(73.5, $h, $material['descripcion'], 0, 'L', 0, 1, 30, $line, true, 1, false, true, $h, 'M', true);
                            $this->setCellPaddings(0, 0, 0, 0);
                            $this->MultiCell(10, $h,  $material['unidadm'], 0, 'C', 0, 1, 103.5, $line, true, 1, false, true, $h, 'M', true);
                            $this->MultiCell(15, $h, $material['cantidad'], 0, 'C', 0, 1, 128.5, $line, true, 1, false, true, $h, 'M', true);
                            $line = $line + $h;
                        }
                    }


                    $linea = $linea + 15;
                    $this->SetXY(10, $linea);
                    $this->SetFont('times', 'B', 7);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(38, 5, "FIRMA AUTORIZANTE", "T", 1, 'C', 0, '', 0,false,'','M');

                    $this->SetXY(57.5, $linea);
                    $this->SetFont('times', 'B', 7);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(38, 5, "RECIBIDO ", "T", 1, 'C', 0, '', 0,false,'','M');

                    $this->SetXY(105.5, $linea);
                    $this->SetFont('times', 'B', 7);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->Cell(38, 5, "ENTREGO", "T", 1, 'C', 0, '', 0,false,'','M');

                }
            }
        }
    }

    $obj_fn = new FuncionesModel();
    $idDespacho = 0;
    if (!empty($_REQUEST['idDespacho'])) { $idDespacho = $obj_fn->encrypt_decrypt("decrypt",$_REQUEST['idDespacho']);}
    $optionMail = 0;
    if (!empty($_REQUEST['optionMail'])) { $optionMail = (int)$_REQUEST['optionMail'];}

    $obj_mat = new MaterialModel();
    $detalleDespacho = $obj_mat->detalle_Despacho_xID($idDespacho);

    $tipoDespacho = null;
    if(!is_null($detalleDespacho)){
        $fechaDespacho = date("dmY");

        $numberDespacho = str_pad($detalleDespacho['codigo_des'],4,"0",STR_PAD_LEFT);

        if(!is_null($detalleDespacho['fecha_des'])) {
            $fechaDes = explode("-", $detalleDespacho['fecha_des']);
            $fechaDespacho = $fechaDes[2] . $fechaDes[1] . $fechaDes[0];
        }

        //Se genera el archivo PDF
        // Creación del objeto de la clase heredada
        $pageLayout = array($width, $height);
        // create new PDF document
        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, $pageLayout, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Dante Castañeda Medina');
        $pdf->SetTitle('REPORTE DESPACHO');
        $pdf->SetSubject('CONTROL DE EPPS');
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        /**************************************************************/
        $pdf->setDatos_Despacho($idDespacho);

        //Close and output PDF document
        $filename = "FRM-".$idDespacho."-".$fechaDespacho.".pdf";
        header('Content-type: application/pdf');
        $pdf->Output($filename, 'D'); // D  I

        //Se prodece a enviar el PDF por Email
        //Debio ser 1 - se puso 5 para que no ejecute
        if($optionMail == 5){
            //creamos el archivo
            $attachFile = $pdf->Output($filename,'S'); // save to a local server file with the name given by name.
            $nameAttachFile = "Despacho-".$numberDespacho.".pdf";
            $dateNow = date("Y-m-d");
            //Enviamos el email
            try {
                //obtenemos datos del Colaborador
                $obj_col = new ColaboradorModel();
                $dtlleColaborador = $obj_col->detalle_Colaborador_xId($detalleDespacho['id_col']);
                $nameColaborador = "";
                $emailColaborador = "";
                if(!is_null($dtlleColaborador)){
                    $nameColaborador = trim($dtlleColaborador['apa_col'].", ".$dtlleColaborador['nombres_col']);
                    $emailColaborador = trim($dtlleColaborador['email_col']);
                }

                //obtenemos datos del Usuario
                $obj_per = new PersonaModel();
                $dtlleUsuario = $obj_per->detalle_Persona_xIDUsuario($detalleDespacho['id_us']);
                $nameUsuario = "";
                $emailUsuario = "";
                if(!is_null($dtlleUsuario)){
                    $nameUsuario = trim($dtlleUsuario['ape_pa_per'].", ".$dtlleUsuario['nombres_per']);
                    $emailUsuario = trim($dtlleUsuario['email_per']);
                }

                $nameDay = $obj_fn->saber_dia($dateNow);
                $today = getdate();
                $hora = $today["hours"];
                if ($hora < 12) {
                    $saludo = " Buenos días ";
                } else if ($hora <= 18) {
                    $saludo = "Buenas tardes ";
                } else {
                    $saludo = "Buenas noches ";
                }

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
                $mail->Subject = "Alerta Despacho - Control de EPPS";
                $mail->ContentType = "text/plain";
                //contenido de Mensaje
                $mail->IsHTML(true);
                $cuerpo = "Estimados<br>".$saludo.
                    "<br><br>Se remite el siguiente correo como parte de la alerta activada, en base a la generación de un nuevo despacho según detalle:<br><br>".
                    "Trabajador: ".$nameColaborador."<br>".
                    "Responsable registro: ".$nameUsuario."<br>".
                    "Fecha despacho: ".$obj_fn->fecha_ENG_ESP($detalleDespacho['fecha_des'])."<br>".
                    "<br>Saludos<br><br>Soporte IMC";
                $mail->msgHTML($cuerpo);
                $mail->AddStringAttachment($attachFile, $nameAttachFile, 'base64', 'application/pdf');
                $mail->AddAddress (strtolower($emailColaborador), $nameColaborador);
                $mail->addCC (strtolower($emailUsuario), $nameUsuario);

                $val = 1;
                $mensaje = "Correo enviado satisfactoriamente";
                if(!$mail->Send()) {
                    $val = 0;
                    $mensaje = "Error al enviar el correo: " . $mail->ErrorInfo;
                }
                //unlink($archivo);

                echo json_encode(array('status'=>$val,'mensaje'=>$mensaje));
            }
            catch (Exception $e) {
                echo $e->getMessage(); //Boring error messages from anything else!
            }

        }
    }
}
catch (PDOException $e) {
    throw $e;
}


