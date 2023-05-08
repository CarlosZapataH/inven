<?php
error_reporting(E_ALL & ~E_NOTICE);
error_reporting(E_ALL);
extract($_REQUEST);

require '../assets/plugins/barcode/autoload.php';
require_once('../assets/plugins/tcpdf/tcpdf.php');
require_once('../model/MaterialModel.php');

try {

    $option = (int)$_REQUEST['option'];
    $datos = "";
    $cbCodigo = "";
    $cbDes = "";
    $cbUm = "";
    //Vertical A4
    $width = 210;
    $height = 279;
    $orientation = "P";
    if($option == 1) {
        $cbCodigo = trim($_REQUEST['cod']);
        $cbDes = trim($_REQUEST['des']);
        $cbUm = trim($_REQUEST['um']);
        //Personal horizontal
        $width = 200;
        $height = 90;
        $orientation = "L";
    }
    else if($option == 2) {
        $datos = explode(",",$_REQUEST['datos']);
    }

    class MYPDF extends TCPDF{

        function setDatos_CodeBar_Unit($code, $des, $um){
            $this->SetPrintHeader(false);
            $this->SetPrintFooter(false);
            $this->AddPage('L', '');
            $this->getBreakMargin();
            $this->SetAutoPageBreak(false, 0);

            $style = array(
                'position' => 'C',
                'align' => 'C',
                'stretch' => true,
                'text' => true,
                'font' => 'helvetica',
                'fontsize' => 40,
                'hpadding' => 'auto',
                'vpadding' => 'auto',
                'fitwidth' => false,
                'border' => false,
                'fgcolor' => array(0, 0, 0),
                'bgcolor' => false, //array(255,255,255),
                'cellfitalign' => 'C',
                'stretchtext' => 50
            );

            $this->SetFont('times', 'B', 30);
            $this->setCellPaddings(0, 0, 0, 0);
            $this->MultiCell(188, 20, $um . " - " . $des, 0, 'C', 0, 1, 6, 0, true, 0, false, true, 30, 'M', true);
            $this->write1DBarcode($code, 'C128A', 0, 23, 188, 77, 0.1, $style, 'C');
            $this->Ln();
        }

        function setDatos_CodeBar_A4($datos){
            $this->SetPrintHeader(false);
            $this->SetPrintFooter(false);
            $this->AddPage('P', 'A4');
            $this->getBreakMargin();

            $obj_mat = new MaterialModel();

            $txtAction = "";
            for($i=0; $i<sizeof($datos); $i++){
                if($i == sizeof($datos) - 1){
                    $txtAction .=  " id_mat = " . (int)$datos[$i];
                }
                else{
                    $txtAction .=  " id_mat = " . (int)$datos[$i]."  OR ";
                }
            }
            $where = "(".$txtAction.") ";

            $lmaterial = array();
            if(!empty($txtAction)){
                $lmaterial = $obj_mat->lista_Material_xID_All($where);
            }

            if(is_array($lmaterial)){
                $cantItem = sizeof($lmaterial);
                $filas = ceil($cantItem/2);

                $style = array(
                    'position' => '',
                    'align' => 'C',
                    'stretch' => true,
                    'text' => true,
                    'font' => 'helvetica',
                    'fontsize' => 25,
                    'hpadding' => 2,
                    'vpadding' => 3,
                    'fitwidth' => false,
                    'border' => false,
                    'fgcolor' => array(0, 0, 0),
                    'bgcolor' => false, //array(255,255,255),
                    'cellfitalign' => 'C',
                    'stretchtext' => 50
                );

                $lineY = 9;
                $key = 0;
                $this->setCellPaddings(0, 0, 0, 0);
                for($t = 0; $t < $filas; $t++){
                    if(!empty($lmaterial[$key]['codigo'])) {
                        $this->SetXY(10, $lineY);
                        $this->Cell(90, 45, "", 1, 1, 'C', 0, '', 0);
                        $this->SetFont('helvetica', 'B', 16);
                        $this->MultiCell(90, 18, $lmaterial[$key]['um'] . " - " . $lmaterial[$key]['descrip'], 0, 'C', 0, 1, 10, $lineY+1, true, 0, false, true, 18, 'M', true);
                        $style['position'] = 'L';
                        $this->write1DBarcode($lmaterial[$key]['codigo'], 'C128A', 10, $lineY+15, 90, 33, 0.4, $style, 'N');
                    }

                    if(!empty($lmaterial[$key+1]['codigo'])) {
                        $this->SetXY(110, $lineY);
                        $this->Cell(90, 45, "", 1, 1, 'C', 0, '', 0);
                        $this->SetFont('helvetica', 'B', 16);
                        $this->MultiCell(90, 18, $lmaterial[$key+1]['um'] . " - " . $lmaterial[$key+1]['descrip'], 0, 'C', 0, 1, 110, $lineY+1, true, 0, false, true, 18, 'M', true);
                        $style['position'] = 'R';
                        $this->write1DBarcode($lmaterial[$key + 1]['codigo'], 'C128A', 110, $lineY+15, 90, 33, 0.4, $style, 'N');
                    }

                    $lineY = $lineY + 54;
                    if($lineY > 260 && $t < $filas-1){
                        $this->AddPage('P', 'A4');
                        $this->getBreakMargin();
                        $this->SetAutoPageBreak(false, 0);
                        $lineY = 9;
                    }

                    $key= $key+2;
                }
            }
        }
    }

    $pageLayout = array($width, $height);
    // create new PDF document
    $pdf = new MYPDF($orientation, 'mm', $pageLayout, true, 'UTF-8', false);

    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Dante Castañeda Medina');
    $pdf->SetTitle('CODE BARRA MATERIAL');
    $pdf->SetSubject('CODE BARRA MATERIAL');

    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    $filename = "barcodePDF-All-" . date("dmY") . ".pdf";
    if($option == 1) {
        $pdf->setDatos_CodeBar_Unit($cbCodigo, $cbDes, $cbUm);
        $filename = "barcodePDF-" . $cbCodigo . ".pdf";
    }
    else if($option == 2) {
        $pdf->setDatos_CodeBar_A4($datos);
    }

   header('Content-type: application/pdf');
   $pdf->Output($filename, 'D'); // D  I
}
catch (PDOException $e) {
    throw $e;
}
