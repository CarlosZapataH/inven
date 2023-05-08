<?php
error_reporting(E_ALL & ~E_NOTICE);
error_reporting(E_ALL);
require_once '../model/FuncionesModel.php';
require_once '../model/MovimientoModel.php';
require_once '../model/AlmacenModel.php';
require_once '../model/InventarioModel.php';
$idMovimiento = $_REQUEST['idMovimiento'];

$obj_mov = new MovimientoModel();
$movimiento = $obj_mov->detalle_Movimiento_xID($idMovimiento);
$obj_alm = new AlmacenModel();
$dtlleAlmacen = $obj_alm->detalle_Almacen_xID($movimiento['id_alm_ini']);


if((int)$dtlleAlmacen['id_vale'] == 1){
    //Horizontal - tamaño fijo
    $width = 210;
    $height = 148.5;
    $lstItemMov = $obj_mov->lista_MovimientoDetalle_xIdMovimiento($idMovimiento);
    if(is_array($lstItemMov)){
        if(sizeof($lstItemMov) > 12){
            $width = 210;
            $height = 279;
        }
    }
}
else if((int)$dtlleAlmacen['id_vale'] == 2 || (int)$dtlleAlmacen['id_vale'] == 3){
    //Vertical A4
    $width = 210;
    $height = 279;
}


require_once('../assets/plugins/tcpdf/tcpdf.php');

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

    function setDatos_Movimiento($movimiento){
        $this->SetPrintHeader(false);
        $this->SetPrintFooter(false);
        $obj_alm = new AlmacenModel();
        $dtlleAlmacen = $obj_alm->detalle_Almacen_xID($movimiento['id_alm_ini']);
        if((int)$dtlleAlmacen['id_vale'] == 1){
            $this->AddPage('L', ''); //(  210 x 279  ) mm
        }
        else if((int)$dtlleAlmacen['id_vale'] == 2 || (int)$dtlleAlmacen['id_vale'] == 3){
            $this->AddPage('P', 'A4'); //(  210 x 279  ) mm
        }

        $this->SetAutoPageBreak(false, 0);
        $this->SetAutoPageBreak(true, 0);

        $obj_mov = new MovimientoModel();
        $lstItemMov = $obj_mov->lista_MovimientoDetalle_xIdMovimiento($movimiento['id_mov']);

        $obj_fn = new FuncionesModel();
        $obj_inv = new InventarioModel();

        $nroValePDF = 0;
        if($movimiento['nrovale_mov'] != "" && $movimiento['nrovale_mov'] != null){
            $nroValePDF = $movimiento['nrovale_mov'];
        }

        if((int)$dtlleAlmacen['id_vale'] == 1) {
            /****************** HEADER ********************/
            $this->SetXY(5, 5);
            $this->Cell(54, 12, "", 1, 1, 'C', 0, '', 0);

            $this->Image('../assets/img/reporte/confi.png', 18, 6, 28, 10, '', '', '', true, 300, '', false, false, 0);

            $this->SetFont('times', 'B', 10);
            $this->setCellPaddings(0, 0, 0, 0);
            $this->SetXY(59, 5);
            $this->Cell(92, 6, "CONFIPETROL", 1, 1, 'C', 0, '', 0);
            $this->SetXY(59, 11);
            $this->Cell(92, 6, "Formato Vale Salida Almacén", 1, 1, 'C', 0, '', 0);

            $this->SetFont('times', 'B', 10);
            $this->setCellPaddings(1, 0.2, 0, 0.2);
            $this->MultiCell(54, 12, "Código:\nVersión:\nFecha:\nPág.:1 de 1", 1, 'L', 0, 1, 151, 5, true, 0, false, true, 12, 'M', true);

            /**************** COLUMNA LEFT*****************/
            $this->SetFont('times', '', 9);
            $this->setCellPaddings(0, 0, 0, 0);
            $this->SetXY(5, 24);
            $this->Cell(54, 4, "ALMACÉN", 0, 1, 'C', 0, '', 0);
            $this->SetFont('times', '', 8);
            $this->MultiCell(54, 10, $dtlleAlmacen['titulo_alm'], 1, 'C', 0, 1, 5, 28, true, 0, false, true, 10, 'M', true);

            /**************** COLUMNA CENTER *****************/
            $this->SetFont('times', '', 9);
            $this->SetXY(60, 24);
            $this->setCellPaddings(0, 0, 2, 0);
            $this->Cell(40, 4, "N° EQUIPO", 0, 1, 'R', 0, '', 0);
            $this->SetXY(100, 24);
            $this->setCellPaddings(0.1, 0.1, 0.1, 0.1);
            $this->Cell(35, 4, $movimiento['und_mov'], 1, 1, 'C', 0, '', 0);

            $this->SetXY(60, 29);
            $this->setCellPaddings(0, 0, 2, 0);
            $this->Cell(40, 4, "Hr/KM", 0, 1, 'R', 0, '', 0);
            $this->SetXY(100, 29);
            $this->setCellPaddings(0.1, 0.1, 0.1, 0.1);
            $this->Cell(35, 4, "", 1, 1, 'C', 0, '', 0);

            $this->SetXY(60, 34);
            $this->setCellPaddings(0, 0, 2, 0);
            $this->Cell(40, 4, "SEMANA", 0, 1, 'R', 0, '', 0);
            $this->SetXY(100, 34);
            $this->setCellPaddings(0.1, 0.1, 0.1, 0.1);
            $this->Cell(35, 4, "", 1, 1, 'C', 0, '', 0);

            /**************** COLUMNA RIGHT *****************/
            $this->SetXY(150, 22);
            $this->setCellPaddings(0, 0, 2, 0);
            $this->Cell(25, 4, "N° VALE", 0, 1, 'R', 0, '', 0);
            $this->SetXY(175, 22);
            $this->SetFont('times', 'B', 12);
            $this->setCellPaddings(0.1, 0.1, 0.1, 0.1);
            $this->Cell(30, 4, $nroValePDF, 1, 1, 'C', 0, '', 0);

            $this->SetXY(150, 29);
            $this->SetFont('times', '', 9);
            $this->setCellPaddings(0, 0, 2, 0);
            $this->Cell(25, 4, "N° OT", 0, 1, 'R', 0, '', 0);
            $this->SetXY(175, 29);
            $this->setCellPaddings(0.1, 0.1, 0.1, 0.1);
            $this->Cell(30, 4,  $movimiento['om_mov'], 1, 1, 'C', 0, '', 0);

            $this->SetXY(150, 34);
            $this->setCellPaddings(0, 0, 2, 0);
            $this->Cell(25, 4, "FECHA/HORA", 0, 1, 'R', 0, '', 0);
            $this->SetXY(175, 34);
            $this->setCellPaddings(0.1, 0.1, 0.1, 0.1);
            $this->Cell(30, 4, $obj_fn->fechaHora_ENG_ESP($movimiento['fechareg_mov']), 1, 1, 'C', 0, '', 0);

            /*************************** Cabecera REPUESTO ***************************/

            $this->SetXY(168, 46);
            $this->SetFont('times', '', 9);
            $this->setCellPaddings(1, 0.1, 1, 0.1);
            $this->Cell(37, 4, "PRÉSTAMO REPUESTO", 1, 1, 'C', 0, '', 1);

            $line = 50;
            /*************************** Cabecera Tabla ***************************/
            $this->SetFont('times', '', 9);
            $this->setCellPaddings(1, 1, 1, 1);
            $this->MultiCell(8, 6, "N°", 1, 'C', 0, 1, 5, $line, true, 0, false, true, 6, 'M', true);
            $this->MultiCell(20, 6, "COD.SAP", 1, 'C', 0, 1, 13, $line, true, 0, false, true, 6, 'M', true);
            $this->MultiCell(20, 6, "N° PARTE", 1, 'C', 0, 1, 33, $line, true, 0, false, true, 6, 'M', true);
            $this->MultiCell(70, 6, "DESCRIPCIÓN", 1, 'C', 0, 1, 53, $line, true, 0, false, true, 6, 'M', true);
            $this->setCellPaddings(0.2, 0.2, 0.2, 0.2);
            $this->MultiCell(18, 6, "O. COMP./\nRESERVA", 1, 'C', 0, 1, 123, $line, true, 0, false, true, 6, 'M', true);
            $this->MultiCell(15, 6, "UND.\nMED.", 1, 'C', 0, 1, 141, $line, true, 0, false, true, 6, 'M', true);
            $this->setCellPaddings(1, 1, 1, 1);
            $this->MultiCell(12, 6, "C/U", 1, 'C', 0, 1, 156, $line, true, 0, false, true, 6, 'M', true);
            $this->MultiCell(18, 6, "DE EQUIPO", 1, 'C', 0, 1, 168, $line, true, 0, false, true, 6, 'M', true);
            $this->MultiCell(19, 6, "REPONER", 1, 'C', 0, 1, 186, $line, true, 0, false, true, 6, 'M', true);

            /*************************** Rows ***************************/
            $line_sig = $line + 6;
            $linefor = $line + 6;

            if(sizeof($lstItemMov) <= 12){
                for ($i = 1; $i <= 12; $i++) {
                    $this->SetFont('times', '', 8);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->SetXY(5, $line_sig);
                    $this->Cell(8, 4, $i, 1, 1, 'C', 0, '', 0);
                    $this->SetXY(13, $line_sig);
                    $this->Cell(20, 4, "", 1, 1, 'C', 0, '', 0);
                    $this->SetXY(33, $line_sig);
                    $this->Cell(20, 4, "", 1, 1, 'C', 0, '', 0);
                    $this->SetXY(53, $line_sig);
                    $this->Cell(70, 4, "", 1, 1, 'L', 0, '', 1);
                    $this->SetXY(123, $line_sig);
                    $this->Cell(18, 4, "", 1, 1, 'C', 0, '', 0);
                    $this->SetXY(141, $line_sig);
                    $this->Cell(15, 4, "", 1, 1, 'C', 0, '', 0);
                    $this->SetXY(156, $line_sig);
                    $this->Cell(12, 4, "", 1, 1, 'C', 0, '', 0);
                    $this->SetXY(168, $line_sig);
                    $this->Cell(18, 4, "", 1, 1, 'C', 0, '', 0);
                    $this->SetXY(186, $line_sig);
                    $this->Cell(9.5, 4, "SI", 1, 1, 'C', 0, '', 0);
                    $this->SetXY(195.5, $line_sig);
                    $this->Cell(9.5, 4, "NO", 1, 1, 'C', 0, '', 0);
                    $line_sig = $line_sig + 4;
                }

                foreach ($lstItemMov as $itemMov){
                    $dtlleInventario = $obj_inv->detalle_Item_xID($itemMov['id_inv']);

                    $campoOCR = $dtlleInventario['ordencompra_inv']."/".$dtlleInventario['reserva_inv'];
                    if(!empty($dtlleInventario['ordencompra_inv']) && empty($dtlleInventario['reserva_inv'])){
                        $campoOCR = $dtlleInventario['ordencompra_inv'];
                    }
                    else if(empty($dtlleInventario['ordencompra_inv']) && !empty($dtlleInventario['reserva_inv'])){
                        $campoOCR = $dtlleInventario['reserva_inv'];
                    }

                    $this->SetFont('times', '', 8);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->SetXY(13, $linefor);
                    $this->Cell(20, 4, $dtlleInventario['cod_inv'], 1, 1, 'C', 0, '', 0);
                    $this->SetXY(33, $linefor);
                    $this->Cell(20, 4, $dtlleInventario['nroparfte_inv'], 1, 1, 'C', 0, '', 0);
                    $this->SetXY(53, $linefor);
                    $this->Cell(70, 4, $dtlleInventario['des_inv'], 1, 1, 'L', 0, '', 1);
                    $this->SetXY(123, $linefor);
                    $this->Cell(18, 4, $campoOCR, 1, 1, 'C', 0, '', 0);
                    $this->SetXY(141, $linefor);
                    $this->Cell(15, 4, $dtlleInventario['um_inv'], 1, 1, 'C', 0, '', 0);
                    $this->SetXY(156, $linefor);
                    $this->Cell(12, 4, $itemMov['cant_mde'], 1, 1, 'C', 0, '', 0);
                    $this->SetXY(168, $linefor);
                    $this->Cell(18, 4,  $dtlleInventario['und_inv'], 1, 1, 'C', 0, '', 0);
                    $linefor = $linefor + 4;
                }
            }
            else{
                $inc = 1;
                foreach ($lstItemMov as $itemMov){
                    $dtlleInventario = $obj_inv->detalle_Item_xID($itemMov['id_inv']);

                    $campoOCR = $dtlleInventario['ordencompra_inv']."/".$dtlleInventario['reserva_inv'];
                    if(!empty($dtlleInventario['ordencompra_inv']) && empty($dtlleInventario['reserva_inv'])){
                        $campoOCR = $dtlleInventario['ordencompra_inv'];
                    }
                    else if(empty($dtlleInventario['ordencompra_inv']) && !empty($dtlleInventario['reserva_inv'])){
                        $campoOCR = $dtlleInventario['reserva_inv'];
                    }

                    $this->SetFont('times', '', 8);
                    $this->setCellPaddings(0, 0, 0, 0);
                    $this->SetXY(5, $line_sig);
                    $this->Cell(8, 4, $inc, 1, 1, 'C', 0, '', 0);
                    $this->SetXY(13, $line_sig);
                    $this->Cell(20, 4, $dtlleInventario['cod_inv'], 1, 1, 'C', 0, '', 0);
                    $this->SetXY(33, $line_sig);
                    $this->Cell(20, 4, $dtlleInventario['nroparfte_inv'], 1, 1, 'C', 0, '', 0);
                    $this->SetXY(53, $line_sig);
                    $this->Cell(70, 4, $dtlleInventario['des_inv'], 1, 1, 'L', 0, '', 1);
                    $this->SetXY(123, $line_sig);
                    $this->Cell(18, 4, $campoOCR, 1, 1, 'C', 0, '', 0);
                    $this->SetXY(141, $line_sig);
                    $this->Cell(15, 4, $dtlleInventario['um_inv'], 1, 1, 'C', 0, '', 0);
                    $this->SetXY(156, $line_sig);
                    $this->Cell(12, 4, $itemMov['cant_mde'], 1, 1, 'C', 0, '', 0);
                    $this->SetXY(168, $line_sig);
                    $this->Cell(18, 4,  $dtlleInventario['und_inv'], 1, 1, 'C', 0, '', 0);
                    $this->SetXY(186, $line_sig);
                    $this->Cell(9.5, 4, "SI", 1, 1, 'C', 0, '', 0);
                    $this->SetXY(195.5, $line_sig);
                    $this->Cell(9.5, 4, "NO", 1, 1, 'C', 0, '', 0);
                    $line_sig = $line_sig + 4;
                    $inc++;
                }
            }

            $obs_line1 = "";
            $obs_line2 = "";
            if(!empty($movimiento['observ_mov'])){
                if(strlen($movimiento['observ_mov']) > 87){
                    $obs_line1 = substr($movimiento['observ_mov'],0,84)."-";
                    $obs_line2 = substr($movimiento['observ_mov'],85,-1)."-";;
                }
                else{
                    $obs_line1 = $movimiento['observ_mov'];
                    $obs_line2 = "";
                }
            }

            $this->SetFont('times', '', 9);
            $this->SetXY(5, $line_sig);
            $this->Cell(200, 5, "OBSERVACIONES:", 1, 1, 'C', 0, '', 0);
            $this->SetXY(5, $line_sig + 5);
            $this->Cell(200, 5, $obs_line1, 1, 1, 'C', 0, '', 0);
            $this->SetXY(5, $line_sig + 10);
            $this->Cell(200, 5, $obs_line2, 1, 1, 'C', 0, '', 0);

            $this->SetFont('times', '', 8);
            $this->setCellPaddings(0, 0, 0, 0);

            if(sizeof($lstItemMov) <= 12){
                $line_sig = 104;
            }

            /**************** COLUMNA LEFT*****************/
            $this->SetXY(13, $line_sig + 25);
            $this->Cell(40, 4, "", "B", 1, 'C', 0, '', 0);
            $this->SetXY(13, $line_sig + 29);
            $this->Cell(40, 4, "Solicitador por", 0, 1, 'C', 0, '', 0);
            $this->SetXY(13, $line_sig + 33);
            $this->Cell(40, 4, "Nombre:", 0, 1, 'L', 0, '', 0);
            $this->SetXY(13, $line_sig + 37);
            $this->Cell(40, 4, "Cargo:", 0, 1, 'L', 0, '', 0);

            /**************** COLUMNA CENTER*****************/
            $this->SetXY(73, $line_sig + 25);
            $this->Cell(67, 4, "", "B", 1, 'C', 0, '', 0);
            $this->SetXY(73, $line_sig + 29);
            $this->Cell(67, 4, "Autorizado por", 0, 1, 'C', 0, '', 0);
            $this->SetXY(73, $line_sig + 33);
            $this->Cell(67, 4, "Nombre:", 0, 1, 'L', 0, '', 0);
            $this->SetXY(73, $line_sig + 37);
            $this->Cell(67, 4, "Cargo:", 0, 1, 'L', 0, '', 0);

            /**************** COLUMNA RIGHT*****************/
            $this->SetXY(160, $line_sig + 25);
            $this->Cell(40, 4, "", "B", 1, 'C', 0, '', 0);
            $this->SetXY(160, $line_sig + 29);
            $this->Cell(40, 4, "Entregado por", 0, 1, 'C', 0, '', 0);
            $this->SetXY(160, $line_sig + 33);
            $this->Cell(40, 4, "Nombre:", 0, 1, 'L', 0, '', 0);
            $this->SetXY(160, $line_sig + 37);
            $this->Cell(40, 4, "Cargo:", 0, 1, 'L', 0, '', 0);
        }
        else if((int)$dtlleAlmacen['id_vale'] == 2){
            /****************** HEADER ********************/
            $this->SetXY(5, 9);
            $this->Cell(28, 16, "", 1, 1, 'C', 0, '', 0);
            $this->Image('../assets/img/reporte/confi.png', 8, 11, 22, 12, '', '', '', true, 300, '', false, false, 0);

            /*************************  BORDER PAGINA  ****************************/
            $this->SetXY(5, 9);
            $this->Cell(200, 281, '', 1, 0, 'L', 0, '', 0,false,'','');

            $this->SetFont('times', 'B', 8);
            $this->setCellPaddings(0, 0, 0, 0);
            $this->SetXY(33, 9);
            $this->Cell(140, 8, "CONFIPETROL", 1, 1, 'C', 0, '', 0);
            $this->SetXY(33, 17);
            $this->Cell(140, 8, "FORMATO DE RETIRO DE REPUESTOS", 1, 1, 'C', 0, '', 0);

            $this->SetFont('times', 'B', 8);
            $this->setCellPaddings(1, 2.4, 1, 2.4);
            $this->MultiCell(32, 16, "Código:\nVersión:\nFecha:\nPág.:1 de 1", 1, 'L', 0, 1, 173, 9, true, 0, false, true, 16, 'M', true);

            /**************** COLUMNA LEFT *****************/
            $this->SetFont('times', '', 6);
            $this->SetXY(5, 28);
            $this->setCellPaddings(0, 0, 2, 0);
            $this->Cell(38, 8, "ORDEN DE TRABAJO:", 0, 0, 'R', 0, '', 0,false,'','B');
            $this->SetXY(43, 28);
            $this->SetFont('times', '', 8);
            $this->setCellPaddings(0.1, 0.1, 0.1, 0.5);
            $this->Cell(44, 8, $movimiento['om_mov'], 'B', 1, 'L', 0, '', 0,false,'','B');

            $this->SetXY(5, 36);
            $this->SetFont('times', '', 6);
            $this->setCellPaddings(0, 0, 2, 0);
            $this->Cell(38, 6, "UBICACIÓN REPUESTO:", 0, 0, 'R', 0, '', 0,false,'','B');
            $this->SetXY(43, 36);
            $this->SetFont('times', '', 8);
            $this->setCellPaddings(0.1, 0.1, 0.1, 0.1);
            $this->Cell(44, 6, "", "B", 1, 'L', 0, '', 0,false,'','B');

            $this->SetXY(5, 41);
            $this->SetFont('times', '', 6);
            $this->setCellPaddings(0, 0, 2, 0);
            $this->Cell(38, 7, "EQUIPO:", 0, 0, 'R', 0, '', 0,false,'','B');
            $this->SetXY(43, 41);
            $this->SetFont('times', '', 8);
            $this->setCellPaddings(0.1, 0.1, 0.1, 0.1);
            $this->Cell(44, 7, $movimiento['und_mov'], 'B', 1, 'L', 0, '', 0,false,'','B');

            /**************** COLUMNA RIGHT *****************/
            $this->SetXY(140, 28);
            $this->SetFont('times', '', 6);
            $this->setCellPaddings(0, 0, 2, 0);
            $this->Cell(33, 8, "NÚMERO DE FORMATO:", 0, 1, 'R', 0, '', 0,false,'','B');
            $this->SetXY(173, 28);
            $this->SetFont('times', 'B', 12);
            $this->setCellPaddings(0.1, 0.1, 0.1, 0.1);
            $this->Cell(32, 8, str_pad($nroValePDF,7,'0',STR_PAD_LEFT), 0, 1, 'L', 0, '', 0,false,'','B');

            $line = 53;
            /*************************** Cabecera Tabla ***************************/
            $this->SetFont('times', '', 6);
            $this->setCellPaddings(0, 0, 0, 0);
            $this->MultiCell(8, 10, "ITEM", 1, 'C', 0, 1, 5, $line, true, 1, false, true, 10, 'M', true);
            $this->setCellPaddings(0.3, 0.3, 0.3, 0.3);
            $this->MultiCell(20, 10, "CODIGO SAP", 1, 'C', 0, 1, 13, $line, true, 0, false, true, 10, 'M', true);
            $this->setCellPaddings(0.3, 0.3, 0.3, 0.3);
            $this->MultiCell(10, 10, "CANT.", 1, 'C', 0, 1, 33, $line, true, 0, false, true, 10, 'M', true);
            $this->MultiCell(70, 10, "DESCRIPCIÓN DEL COMPONENTE/REPUESTO", 1, 'C', 0, 1, 43, $line, true, 0, false, true, 10, 'M', true);
            $this->MultiCell(15, 10, "UND.\nMEDIDA", 1, 'C', 0, 1, 113, $line, true, 0, false, true, 10, 'M', true);
            $this->MultiCell(15, 10, "NÚMERO\nRESERVA", 1, 'C', 0, 1, 128, $line, true, 0, false, true, 10, 'M', true);
            $this->MultiCell(15, 10, "FECHA RETIRO\nALMACÉN", 1, 'C', 0, 1, 143, $line, true, 0, false, true, 10, 'M', true);
            $this->MultiCell(15, 10, "FECHA\nINSTALAC.", 1, 'C', 0, 1, 158, $line, true, 0, false, true, 10, 'M', true);
            $this->MultiCell(32, 10, "OBSERVACIONES", 1, 'C', 0, 1, 173, $line, true, 0, false, true, 10, 'M', true);

            $line_sig = $line + 10;
            for ($i = 1; $i <= 10; $i++) {
                $this->SetFont('times', '', 8);
                $this->setCellPaddings(0, 0, 0, 0);
                $this->SetXY(5, $line_sig);
                $this->Cell(8, 6, $i, 1, 1, 'C', 0, '', 0);
                $this->SetXY(13, $line_sig);
                $this->Cell(20, 6, "", 1, 1, 'C', 0, '', 0);
                $this->SetXY(33, $line_sig);
                $this->Cell(10, 6, "", 1, 1, 'C', 0, '', 0);
                $this->SetXY(43, $line_sig);
                $this->Cell(70, 6, "", 1, 1, 'L', 0, '', 1);
                $this->SetXY(113, $line_sig);
                $this->Cell(15, 6, "", 1, 1, 'C', 0, '', 0);
                $this->SetXY(128, $line_sig);
                $this->Cell(15, 6, "", 1, 1, 'C', 0, '', 0);
                $this->SetXY(143, $line_sig);
                $this->Cell(15, 6, "", 1, 1, 'C', 0, '', 0);
                $this->SetXY(158, $line_sig);
                $this->Cell(15, 6, "", 1, 1, 'C', 0, '', 0);
                $this->SetXY(173, $line_sig);
                $this->Cell(32, 6, "", 1, 1, 'C', 0, '', 1);
                $line_sig = $line_sig + 6;
            }

            $line_row = $line + 10;
            foreach ($lstItemMov as $itemMov){
                $dtlleInventario = $obj_inv->detalle_Item_xID($itemMov['id_inv']);

                $campoOCR = $dtlleInventario['ordencompra_inv']."/".$dtlleInventario['reserva_inv'];
                if(!empty($dtlleInventario['ordencompra_inv']) && empty($dtlleInventario['reserva_inv'])){
                    $campoOCR = $dtlleInventario['ordencompra_inv'];
                }
                else if(empty($dtlleInventario['ordencompra_inv']) && !empty($dtlleInventario['reserva_inv'])){
                    $campoOCR = $dtlleInventario['reserva_inv'];
                }

                $this->SetFont('times', '', 8);
                $this->setCellPaddings(0, 0, 0, 0);
                $this->SetXY(13, $line_row);
                $this->Cell(20, 6, $dtlleInventario['cod_inv'], 0, 1, 'C', 0, '', 0);
                $this->SetXY(33, $line_row);
                $this->Cell(10, 6, $itemMov['cant_mde'], 0, 1, 'C', 0, '', 0);
                $this->SetXY(43, $line_row);
                $this->Cell(70, 6, $dtlleInventario['des_inv'], 0, 1, 'L', 0, '', 1);
                $this->SetXY(113, $line_row);
                $this->Cell(15, 6, $dtlleInventario['um_inv'], 0, 1, 'C', 0, '', 0);
                $this->SetXY(128, $line_row);
                $this->Cell(15, 6, $dtlleInventario['reserva_inv'], 0, 1, 'C', 0, '', 0);
                $this->SetXY(143, $line_row);
                $this->Cell(15, 6, '', 0, 1, 'C', 0, '', 0);
                if($dtlleInventario['fechains_inv'] != "0000-00-00") {
                    $this->SetXY(158, $line_row);
                    $this->Cell(15, 6, $obj_fn->fecha_ENG_ESP($dtlleInventario['fechains_inv']), 0, 1, 'C', 0, '', 0);
                }

                $this->MultiCell(32, 6, '', 0, 'C', 0, 1, 173, $line_row, true, 1, false, true, 6, 'M', true);
                $line_row = $line_row + 6;
            }


            $line_sig = 118;
            /**************** COLUMNA LEFT *****************/
            $this->SetXY(13, $line_sig + 15);
            $this->setCellPaddings(0, 0, 0, 1);
            $this->Cell(85, 5, "OTROS COMENTARIOS", 0, 1, 'L', 0, '', 0,false,'','B');
            $this->SetXY(13, $line_sig + 20);
            $this->setCellPaddings(2, 2, 2, 2);
            $this->Cell(85, 60, $movimiento['observ_mov'], 1, 1, 'C', 0, '', 0,false,'','T');

            /**************** COLUMNA RIGHT *****************/
            /***** FIRMA 1 *****************/
            $this->SetXY(112, $line_sig + 25);
            $this->SetFont('times', '', 7);
            $this->setCellPaddings(0, 0, 0, 0);
            $this->Cell(70, 5, "Firma Responsable SPCC", 'T', 1, 'C', 0, '', 0,false,'','B');

            $this->SetXY(112, $line_sig + 30);
            $this->SetFont('times', '', 7);
            $this->setCellPaddings(0, 0, 1, 0);
            $this->Cell(25, 10, "Apellidos y Nombre:", 0, 1, 'R', 0, '', 0,false,'','B');

            $this->SetXY(137, $line_sig + 30);
            $this->SetFont('times', '', 8);
            $this->setCellPaddings(0, 0, 0, 0);
            $this->Cell(45, 10, '', 'B', 1, 'L', 0, '', 0,false,'','B');

            $this->SetXY(112, $line_sig + 40);
            $this->SetFont('times', '', 7);
            $this->setCellPaddings(0, 0, 1, 0);
            $this->Cell(25, 7, "DNI:", 0, 1, 'R', 0, '', 0,false,'','B');

            $this->SetXY(137, $line_sig + 40);
            $this->SetFont('times', '', 8);
            $this->setCellPaddings(0, 0, 0, 0);
            $this->Cell(45, 7, '', 'B', 1, 'L', 0, '', 0,false,'','B');

            /***** FIRMA 2 *****************/
            $line_sig = $line_sig + 40;
            $this->SetXY(112, $line_sig + 25);
            $this->SetFont('times', '', 7);
            $this->setCellPaddings(0, 0, 0, 0);
            $this->Cell(70, 5, "Firma Supervisor Confipetrol *", 'T', 1, 'C', 0, '', 0,false,'','B');

            $this->SetXY(112, $line_sig + 30);
            $this->SetFont('times', '', 7);
            $this->setCellPaddings(0, 0, 1, 0);
            $this->Cell(25, 10, "Apellidos y Nombre:", 0, 1, 'R', 0, '', 0,false,'','B');

            $this->SetXY(137, $line_sig + 30);
            $this->SetFont('times', '', 8);
            $this->setCellPaddings(0, 0, 0, 0);
            $this->Cell(45, 10, '', 'B', 1, 'L', 0, '', 0,false,'','B');

            $this->SetXY(112, $line_sig + 40);
            $this->SetFont('times', '', 7);
            $this->setCellPaddings(0, 0, 1, 0);
            $this->Cell(25, 7, "DNI:", 0, 1, 'R', 0, '', 0,false,'','B');

            $this->SetXY(137, $line_sig + 40);
            $this->SetFont('times', '', 8);
            $this->setCellPaddings(0, 0, 0, 0);
            $this->Cell(45, 7, '', 'B', 1, 'L', 0, '', 0,false,'','B');

            /***** FIRMA 3 *****************/
            $line_sig = $line_sig + 40;
            $this->SetXY(112, $line_sig + 25);
            $this->SetFont('times', '', 7);
            $this->setCellPaddings(0, 0, 0, 0);
            $this->Cell(70, 5, "Firma Almacenero Confipetrol", 'T', 1, 'C', 0, '', 0,false,'','B');

            $this->SetXY(112, $line_sig + 30);
            $this->SetFont('times', '', 7);
            $this->setCellPaddings(0, 0, 1, 0);
            $this->Cell(25, 10, "Apellidos y Nombre:", 0, 1, 'R', 0, '', 0,false,'','B');

            $this->SetXY(137, $line_sig + 30);
            $this->SetFont('times', '', 8);
            $this->setCellPaddings(0, 0, 0, 0);
            $this->Cell(45, 10, '', 'B', 1, 'L', 0, '', 0,false,'','B');

            $this->SetXY(112, $line_sig + 40);
            $this->SetFont('times', '', 7);
            $this->setCellPaddings(0, 0, 1, 0);
            $this->Cell(25, 7, "DNI:", 0, 1, 'R', 0, '', 0,false,'','B');

            $this->SetXY(137, $line_sig + 40);
            $this->SetFont('times', '', 8);
            $this->setCellPaddings(0, 0, 0, 0);
            $this->Cell(45, 7, '', 'B', 1, 'L', 0, '', 0,false,'','B');

            /***** FIRMA 4 *****************/
            $line_sig = $line_sig + 40;
            $this->SetXY(112, $line_sig + 25);
            $this->SetFont('times', '', 7);
            $this->setCellPaddings(0, 0, 0, 0);
            $this->Cell(70, 5, "Firma Técnico Instalador del Componente/Repuesto", 'T', 1, 'C', 0, '', 0,false,'','B');

            $this->SetXY(112, $line_sig + 30);
            $this->SetFont('times', '', 7);
            $this->setCellPaddings(0, 0, 1, 0);
            $this->Cell(25, 10, "Apellidos y Nombre:", 0, 1, 'R', 0, '', 0,false,'','B');

            $this->SetXY(137, $line_sig + 30);
            $this->SetFont('times', '', 8);
            $this->setCellPaddings(0, 0, 0, 0);
            $this->Cell(45, 10, '', 'B', 1, 'L', 0, '', 0,false,'','B');

            $this->SetXY(112, $line_sig + 40);
            $this->SetFont('times', '', 7);
            $this->setCellPaddings(0, 0, 1, 0);
            $this->Cell(25, 7, "DNI:", 0, 1, 'R', 0, '', 0,false,'','B');

            $this->SetXY(137, $line_sig + 40);
            $this->SetFont('times', '', 8);
            $this->setCellPaddings(0, 0, 0, 0);
            $this->Cell(45, 7, '', 'B', 1, 'L', 0, '', 0,false,'','B');

        }
        //VALE DE RETIRO
        else if((int)$dtlleAlmacen['id_vale'] == 3){
            /****************** HEADER ********************/
            $this->SetXY(5, 9);
            $this->Cell(28, 16, "", 1, 1, 'C', 0, '', 0);
            $this->Image('../assets/img/reporte/confi.png', 8, 11, 22, 12, '', '', '', true, 300, '', false, false, 0);

            /*************************  BORDER PAGINA  ****************************/
            $this->SetXY(5, 9);
            $this->Cell(200, 281, '', 1, 0, 'L', 0, '', 0,false,'','');

            $this->SetFont('times', 'B', 8);
            $this->setCellPaddings(0, 0, 0, 0);
            $this->SetXY(33, 9);
            $this->Cell(140, 8, "CONFIPETROL", 1, 1, 'C', 0, '', 0);
            $this->SetXY(33, 17);
            $this->Cell(140, 8, "CARGO DE ENTREGA DE HERRAMIENTAS, EQUIPOS E INSTRUMENTOS DE MEDICIÓN Y ENSAYO", 1, 1, 'C', 0, '', 0);

            $this->SetFont('times', 'B', 8);
            $this->setCellPaddings(1, 2.4, 1, 2.4);
            $this->MultiCell(32, 16, "Código: LOG-GEN1-F-43\nVersión: 2\nFecha: 25-01-2017\nPág.: 1 de 1", 1, 'L', 0, 1, 173, 9, true, 0, false, true, 16, 'M', true);

            /**************** COLUMNA LEFT *****************/

            $nombreApellido = "";
            if(!empty($movimiento['solicitado_mov'])){
                $nombreApellido = mb_strtoupper($movimiento['solicitado_mov'],"UTF-8");
            }
            $numDocumento = "";
            if(!empty($movimiento['recibido_mov'])){
                $numDocumento = $movimiento['recibido_mov'];
            }

            $this->SetXY(10, 25);
            $this->SetFont('times', '', 8);
            $this->setCellPaddings(0, 0, 2, 0);
            $this->Cell(30, 6, "ALMACÉN:", 0, 0, 'L', 0, '', 0,false,'','B');
            $this->SetXY(47, 25);
            $this->SetFont('times', '', 8);
            $this->setCellPaddings(0.1, 0.1, 0.1, 0.1);
            $this->Cell(80, 6, mb_strtoupper($obj_fn->reemplazar_string($dtlleAlmacen['titulo_alm']),"UTF-8"), "B", 1, 'L', 0, '', 0,false,'','B');


            $this->SetXY(10, 31);
            $this->SetFont('times', '', 8);
            $this->setCellPaddings(0, 0, 2, 0);
            $this->Cell(30, 6, "APELLIDOS Y NOMBRES:", 0, 0, 'L', 0, '', 0,false,'','B');
            $this->SetXY(47, 31);
            $this->SetFont('times', '', 8);
            $this->setCellPaddings(0.1, 0.1, 0.1, 0.1);
            $this->Cell(80, 6, $obj_fn->reemplazar_string($nombreApellido), "B", 1, 'L', 0, '', 0,false,'','B');

            $this->SetXY(10, 37);
            $this->SetFont('times', '', 8);
            $this->setCellPaddings(0, 0, 2, 0);
            $this->Cell(30, 6, "NÚMERO DE DOCUMENTO:", 0, 0, 'L', 0, '', 0,false,'','B');
            $this->SetXY(47, 37);
            $this->SetFont('times', '', 8);
            $this->setCellPaddings(0.1, 0.1, 0.1, 0.1);
            $this->Cell(80, 6, $numDocumento, 'B', 1, 'L', 0, '', 0,false,'','B');

            $campoArea = explode("/",$movimiento['areaoperativa_mov']);
            $textoAOperativa = $movimiento['areaoperativa_mov'];
            $textoTipoCargo = "";
            if(!empty($campoArea[0]) && !empty($campoArea[1])){
                $textoAOperativa = trim($campoArea[0]);
                $textoTipoCargo = trim($campoArea[1]);
            }
            $this->SetXY(10, 43);
            $this->SetFont('times', '', 8);
            $this->setCellPaddings(0, 0, 2, 0);
            $this->Cell(30, 6, "AREA OPERATIVA:", 0, 0, 'L', 0, '', 0,false,'','B');
            $this->SetXY(47, 43);
            $this->SetFont('times', '', 8);
            $this->setCellPaddings(0.1, 0.1, 0.1, 0.1);
            $this->Cell(80, 6, $obj_fn->reemplazar_string($textoAOperativa), "B", 1, 'L', 0, '', 0,false,'','B');

            $this->SetXY(10, 48);
            $this->SetFont('times', '', 8);
            $this->setCellPaddings(0, 0, 2, 0);
            $this->Cell(30, 6, "TIPO CARGO:", 0, 0, 'L', 0, '', 0,false,'','B');
            $this->SetXY(47, 48);
            $this->SetFont('times', '', 8);
            $this->setCellPaddings(0.1, 0.1, 0.1, 0.1);
            $this->Cell(80, 6, mb_strtoupper($obj_fn->reemplazar_string($textoTipoCargo),"UTF-8"), 'B', 1, 'L', 0, '', 0,false,'','B');

            /**************** COLUMNA RIGHT *****************/
            $this->SetXY(143, 28);
            $this->SetFont('times', 'B', 12);
            $this->setCellPaddings(0.1, 0.1, 0.1, 0.1);
            $this->Cell(32, 8, "Nro. ".str_pad($nroValePDF,7,'0',STR_PAD_LEFT), 0, 1, 'L', 0, '', 0,false,'','B');

            $this->SetXY(143, 36);
            $this->SetFont('times', '', 8);
            $this->setCellPaddings(0, 0, 2, 0);
            $this->Cell(12, 6, "FECHA:", 0, 0, 'L', 0, '', 0,false,'','B');
            $this->SetXY(155, 36);
            $this->SetFont('times', '', 9);
            $this->setCellPaddings(0.1, 0.1, 0.1, 0.1);
            $this->Cell(20, 6, $obj_fn->fecha_ENG_ESP($movimiento['fecha_mov']), "B", 1, 'L', 0, '', 0,false,'','B');

            $line = 58;
            /*************************** Cabecera Tabla ***************************/
            $this->SetFont('times', 'B', 5);
            $this->setCellPaddings(0, 0, 0, 0);
            $this->MultiCell(6, 10, "ITEM", 1, 'C', 0, 1, 5, $line, true, 1, false, true, 10, 'M', true);
            $this->setCellPaddings(0.3, 0.3, 0.3, 0.3);
            $this->MultiCell(140, 3, "ARTÍCULO", 1, 'C', 0, 1, 11, $line, true, 1, false, true, 3,  'M',true);

            $this->MultiCell(25, 7, "CODIGO", 1, 'C', 0, 1, 11, $line+3, true, 0, false, true, 7, 'M', true);
            $this->MultiCell(87, 7, "DESCRIPCIÓN", 1, 'C', 0, 1, 36, $line+3, true, 0, false, true, 7, 'M', true);
            $this->MultiCell(28, 7, "NRO. SERIE", 1, 'C', 0, 1, 123, $line+3, true, 0, false, true, 7, 'M', true);
            $this->MultiCell(8, 10, "U.M.", 1, 'C', 0, 1, 151, $line, true, 0, false, true, 10, 'M', true);
            $this->MultiCell(10, 10, "CANT.", 1, 'C', 0, 1, 159, $line, true, 0, false, true, 10, 'M', true);
            $this->MultiCell(20, 10, "FIRMA\nDEVOLUCIÓN\nPOR ITEM", 1, 'C', 0, 1, 169, $line, true, 0, false, true, 10, 'M', true);
            $this->MultiCell(16, 10, "FECHA\nDEVOLUCIÓN", 1, 'C', 0, 1, 189, $line, true, 0, false, true, 10, 'M', true);

            $line_sig = $line + 10;
            for ($i = 1; $i <= 22; $i++) {
                $this->SetFont('times', 'B', 6);
                $this->setCellPaddings(0, 0, 0, 0);
                $this->SetXY(5, $line_sig);
                $this->Cell(6, 6, $i, 1, 1, 'C', 0, '', 0);
                $this->SetXY(11, $line_sig);
                $this->Cell(25, 6, "", 1, 1, 'C', 0, '', 0);
                $this->SetXY(36, $line_sig);
                $this->Cell(87, 6, "", 1, 1, 'L', 0, '', 0);
                $this->SetXY(123, $line_sig);
                $this->Cell(28, 6, "", 1, 1, 'C', 0, '', 0);
                $this->SetXY(151, $line_sig);
                $this->Cell(8, 6, "", 1, 1, 'C', 0, '', 0);
                $this->SetXY(159, $line_sig);
                $this->Cell(10, 6, "", 1, 1, 'C', 0, '', 0);
                $this->SetXY(169, $line_sig);
                $this->Cell(20, 6, "", 1, 1, 'C', 0, '', 0);
                $this->SetXY(189, $line_sig);
                $this->Cell(16, 6, "", 1, 1, 'C', 0, '', 0);
                $line_sig = $line_sig + 6;
            }

            $line_row = $line + 10;
            foreach ($lstItemMov as $itemMov){
                $this->SetFont('times', '', 6);
                $this->setCellPaddings(0, 0, 0, 0);
                $this->SetXY(11, $line_row);
                $this->Cell(25, 6, $itemMov['cod_mde'], 0, 1, 'C', 0, '', 0);
                $this->SetXY(36.5, $line_row);
                $this->Cell(86.5, 6, $itemMov['des_mde'], 0, 1, 'L', 0, '', 1);
                $this->SetXY(123, $line_row);
                $this->Cell(28, 6, $itemMov['nparte_mde'], 0, 1, 'C', 0, '', 1);
                $this->SetXY(151, $line_row);
                $this->Cell(8, 6, $itemMov['um_mde'], 0, 1, 'C', 0, '', 0);
                $this->SetXY(159, $line_row);
                $this->Cell(10, 6, $itemMov['cant_mde'], 0, 1, 'C', 0, '', 0);
                $line_row = $line_row + 6;
            }

            $lineObserv = 205;
            /**************** COLUMNA LEFT *****************/
            $this->SetXY(8, $lineObserv);
            $this->setCellPaddings(0, 0, 0, 1);
            $this->Cell(85, 5, "OBSERVACIONES", 0, 1, 'L', 0, '', 0,false,'','B');

            $this->SetXY(8, $lineObserv+5);
            $this->setCellPaddings(0, 0, 0, 0);
            $this->Cell(194, 32, "", 1, 1, 'L', 0, '', 0,false,'','T');
            $this->SetFont('times', '', 7);
            //$movimiento['observ_mov']
            $this->MultiCell(192, 30, $movimiento['observ_mov'], 0, 'C', 0, 1, 9, $lineObserv+6, true, 0, false, true, 29, 'M', true);


            $lineFirmas = 262;
            /***** FIRMAS *****************/
            $this->SetXY(37.1, $lineFirmas);
            $this->SetFont('times', '', 7);
            $this->setCellPaddings(0, 0, 0, 0);
            $this->Cell(27.1, 5, "FIRMA ALMACEN", 'T', 1, 'C', 0, '', 0,false,'','B');

            $this->SetXY(91.3, $lineFirmas);
            $this->SetFont('times', '', 7);
            $this->setCellPaddings(0, 0, 0, 0);
            $this->Cell(27.1, 5, "RECIBIDO", 'T', 1, 'C', 0, '', 0,false,'','B');

            $this->SetXY(120, 244);
            $this->SetFont('times', '', 5);
            $this->Cell(15, 18, $numDocumento, 1, 1, 'C', 0, '', 0,false,'','T');

            $this->SetXY(120, 259);
            $this->SetFont('times', '', 4);
            $this->Cell(15, 3, "HUELLA DIGITAL", 0, 1, 'C', 0, '', 1,false,'','B');

            $this->SetXY(145.5, $lineFirmas);
            $this->SetFont('times', '', 7);
            $this->setCellPaddings(0, 0, 0, 0);
            $this->Cell(27.1, 5, "ENTREGADO", 'T', 1, 'C', 0, '', 0,false,'','B');

            $this->SetXY(174, 244);
            $this->SetFont('times', '', 5);
            $this->Cell(15, 18, $numDocumento, 1, 1, 'C', 0, '', 0,false,'','T');

            $this->SetXY(174, 259);
            $this->SetFont('times', '', 4);
            $this->Cell(15, 3, "HUELLA DIGITAL", 0, 1, 'C', 0, '', 1,false,'','B');

            $textFooter = "En cumplimiento de mis funciones y para facilitar el desarrollo de mis labores, la Empresa me ha asignado a partir de la fecha los elementos descritos según relación anexa, en la cual también se describe el estado en el que estoy recibiendo todos y cada uno de los elementos allí relacionados. Manifiesto expresamente que me comprometo a devolver los anteriores elementos en el mismo estado en el que los estoy recibiendo, salvo el deterioro natural de estos. En caso de no ser así y/o en caso de perdida autorizo al Empleador para descontar de mi salario y/o liquidación el valor de las pérdidas y/o daños que ocasione a dichos elementos y que sean atribuidos al suscrito, de conformidad con lo señalado en el Reglamento Interno de Trabajo y/o en los Contratos de Trabajo respectivos.Así mismo es responsabilidad del usuario devolver los instrumentos de medición 20 días antes del vencimiento de la fecha de calibración.\n";
            $this->SetFont('times', '', 10);
            $this->setCellPaddings(0.3, 0.3, 0.3, 0.3);
            $this->MultiCell(190, 15, $textFooter, 0, 'J', 0, 1, 10, 273, true, 1, false, true, 15,  'M',true);


            /*
                        $this->SetXY(160, $line_sig + 33);
                        $this->Cell(40, 4, "Nombre:", 0, 1, 'L', 0, '', 0);
                        $this->SetXY(160, $line_sig + 37);
                        $this->Cell(40, 4, "Cargo:", 0, 1, 'L', 0, '', 0);*/

        }
    }
}


$pageLayout = array($width, $height);
// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, $pageLayout, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Dante Castañeda Medina');
$pdf->SetTitle('VALE DE RETIRO');
$pdf->SetSubject('CONTROL DE ALMACEN');

$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

/**************************************************************/
$pdf->setDatos_Movimiento($movimiento);

//Close and output PDF document
$filename = "VALE-".$movimiento['id_alm_ini']."-".$movimiento['id_mov'].".pdf";
header('Content-type: application/pdf');
$pdf->Output($filename, 'D'); // D  I

//============================================================+
// END OF FILE
//============================================================+