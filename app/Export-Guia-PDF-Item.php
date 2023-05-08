<?php
error_reporting(E_ALL & ~E_NOTICE);
error_reporting(E_ALL);
require_once '../model/FuncionesModel.php';
require_once '../model/MovimientoModel.php';
require_once '../model/AlmacenModel.php';
require_once '../model/ServicioModel.php';
require_once '../model/UbigeoModel.php';

$obj_fn = new FuncionesModel();
$idTransfer = $obj_fn->encrypt_decrypt('decrypt',$_REQUEST['idTransfer']);
//$idTransfer = (int)$_REQUEST['idTransfer'];
$option = (int)$_REQUEST['option']; // 1->formato 2->Impresion

$obj_mov = new MovimientoModel();
$movimiento = $obj_mov->detalle_MovimientoTransito_xID($idTransfer);

$width = 240;
$height = 305;

require_once('../assets/plugins/tcpdf/tcpdf.php');

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

    function setDatos_Movimiento($movimiento,$option){
        $this->SetPrintHeader(false);
        $this->SetPrintFooter(false);

        $this->AddPage('P', ''); //(  240 x 305  ) mm
        $this->getBreakMargin();
        $this->SetAutoPageBreak(false, 0);
        if((int)$option == 1) {
            $this->Image('../assets/img/guia/guia_blanco.jpg', 0, 0, 240, 305, '', '', '', true, 300, '', false, false, 0);
            $this->SetAutoPageBreak(true, 0);
        }
        $this->setPageMark();

        $obj_ub = new UbigeoModel();
        $obj_alm = new AlmacenModel();
        /**************** Obtenemos Detalle Almacen Inicio *****************/
        $dtlleAlmacenINI = $obj_alm->detalle_Almacen_xID($movimiento['id_alm_ini']);
        $Idireccion = "";
        $Idireccion2 = "";
        $Idepartamento = "";
        $Iprovincia = "";
        $Idistrito = "";
        $Idiretionlenght = 0;
        if(!is_null($dtlleAlmacenINI)){
            $Idiretion = mb_strtoupper($dtlleAlmacenINI['direccion_alm'],"UTF-8");
            $Idiretionlenght = strlen($Idiretion);
            if($Idiretionlenght> 47){
                $Idireccion  = substr($Idiretion,0,47);
                $Idireccion2 = substr($Idiretion,47,-1);
            }
            else{
                $Idireccion = $Idiretion;
            }

            $Idepartamento_ = $obj_ub->detalle_ubigeo_xId($dtlleAlmacenINI['departamento_alm']);
            $Idepartamento = mb_strtoupper($Idepartamento_['nombre_ubigeo'],"UTF-8");
            $Iprovincia_ = $obj_ub->detalle_ubigeo_xId($dtlleAlmacenINI['provincia_alm']);
            $Iprovincia = mb_strtoupper($Iprovincia_['nombre_ubigeo'],"UTF-8");
            $Idistrito_ = $obj_ub->detalle_ubigeo_xId($dtlleAlmacenINI['distrito_alm']);
            $Idistrito = mb_strtoupper($Idistrito_['nombre_ubigeo'],"UTF-8");
        }

        /**************** Obtenemos Detalle Almacen Destino *****************/
        $dtlleAlmacenDES = $obj_alm->detalle_Almacen_xID($movimiento['id_alm_des']);
        $Ddireccion = "";
        $Ddireccion2 = "";
        $Ddepartamento = "";
        $Dprovincia = "";
        $Ddistrito = "";
        $proyecto = "";
        $Ddiretionlenght = 0;
        if(!is_null($dtlleAlmacenDES)){
            $Ddiretion = $dtlleAlmacenDES['direccion_alm'];
            $Ddiretionlenght = strlen($Ddiretion);
            if(strlen($Ddiretion) > 47){
                $Ddireccion  = substr($Ddiretion,0,47);
                $Ddireccion2 = substr($Ddiretion,47,-1);
            }
            else{
                $Ddireccion = $Ddiretion;
            }

            $tamanio = strlen($dtlleAlmacenDES['direccion_alm']." CALLE LI");


            $Ddepartamento_ = $obj_ub->detalle_ubigeo_xId($dtlleAlmacenDES['departamento_alm']);
            $Ddepartamento = mb_strtoupper($Ddepartamento_['nombre_ubigeo'],"UTF-8");
            $Dprovincia_ = $obj_ub->detalle_ubigeo_xId($dtlleAlmacenDES['provincia_alm']);
            $Dprovincia = mb_strtoupper($Dprovincia_['nombre_ubigeo'],"UTF-8");
            $Ddistrito_ = $obj_ub->detalle_ubigeo_xId($dtlleAlmacenDES['distrito_alm']);
            $Ddistrito = mb_strtoupper($Ddistrito_['nombre_ubigeo'],"UTF-8");

            $obj_serv = new ServicioModel();
            $dtlleServicio = $obj_serv->detalle_Servicio_xID($dtlleAlmacenDES['id_serv']);
            if(!is_null($dtlleServicio)){
                $proyecto = mb_strtoupper($dtlleServicio['des_serv'],"UTF-8")."/".mb_strtoupper($dtlleAlmacenDES['titulo_alm'],"UTF-8")."-".$tamanio;
            }
        }

        /************** OBTENIENDO SERIE Y NUMERO DE GUIA *************************/
        $serieGuia = "000";
        $numberGuia = "0000000";
        if(!is_null($movimiento['nroguia_mov'])){
            $correlativoGuia = explode("-",$movimiento['nroguia_mov']);
            if(sizeof($correlativoGuia)==3){
                $serieGuia  = str_pad((int)$correlativoGuia[1],3,"0",STR_PAD_LEFT);
                $numberGuia = $correlativoGuia[2];
            }
            else{
                $serieGuia  = $correlativoGuia[0];
                $numberGuia = $correlativoGuia[1];
            }
        }

        $obj_fn = new FuncionesModel();
        $this->SetTextColor(0,0,0);
        /**************** SERIE GUIA *****************/
        if((int)$option == 1) {
            $this->SetXY(161, 38);
            $this->SetFont('helvetica', 'B', 18);
            $this->setCellPaddings(0, 0, 0, 0);
            $this->Cell(12, 4, $serieGuia, 0, 1, 'L', 0, '', 0, false, '', 'B');
        }

        /**************** NUMERO DE GUIA *****************/
        $this->SetXY(176, 38);
        $this->SetFont('helvetica', 'B', 18);
        $this->SetTextColor(153,0,0);
        $this->setCellPaddings(0, 0, 0, 0);
        $this->Cell(30, 4, $numberGuia, 0, 1, 'L', 0, '', 0,false,'','B');

        $this->SetTextColor(0,0,0);
        /**************** FECHA DE EMISION *****************/
        $this->SetXY(40, 53.7);
        $this->SetFont('times', '', 9);
        $this->setCellPaddings(0, 0, 0, 0);
        $this->Cell(35, 4, $obj_fn->fecha_ENG_ESP($movimiento['fecha_mov']), 0, 1, 'L', 0, '', 0,false,'','B');

        /**************** FECHA INICIO DEL TRASLADO *****************/
        $this->SetXY(122, 53.7);
        $this->SetFont('times', '', 9);
        $this->setCellPaddings(0, 0, 0, 0);
        $this->Cell(35, 4, $obj_fn->fecha_ENG_ESP($movimiento['fecha_mov']), 0, 1, 'L', 0, '', 0,false,'','B');


        /**************** DOMICILIO PUNTO DE PARTIDA *****************/
        $this->SetXY(31, 65);
        $this->SetFont('times', '', 9);
        $this->setCellPaddings(0, 0, 0, 0);
        $this->Cell(87, 4, $Idireccion, 0, 1, 'L', 0, '', 0,false,'','B');

        if($Idiretionlenght > 47) {
            $this->SetXY(14, 69.7);
            $this->SetFont('times', '', 9);
            $this->setCellPaddings(0, 0, 0, 0);
            $this->Cell(104, 4, $Idireccion2, 0, 1, 'L', 0, '', 0, false, '', 'B');
        }

        $this->SetXY(28, 74.7);
        $this->SetFont('times', '', 7);
        $this->setCellPaddings(0, 0, 0, 0);
        $this->Cell(20, 4, $Idistrito, 0, 1, 'L', 0, '', 0,false,'','B');

        $this->SetXY(59, 74.7);
        $this->SetFont('times', '', 7);
        $this->setCellPaddings(0, 0, 0, 0);
        $this->Cell(26, 4, $Iprovincia, 0, 1, 'L', 0, '', 0,false,'','B');

        $this->SetXY(93, 74.7);
        $this->SetFont('times', '', 7);
        $this->setCellPaddings(0, 0, 0, 0);
        $this->Cell(25, 4, $Idepartamento, 0, 1, 'L', 0, '', 0,false,'','B');


        /**************** DOMICILIO DEL PUNTO DE LLEGADA *****************/
        $this->SetXY(139, 63.7);
        $this->SetFont('times', '', 9);
        $this->setCellPaddings(0, 0, 0, 0);
        $this->Cell(89, 4, $Ddireccion, 0, 1, 'L', 0, '', 0,false,'','B');

        if($Ddiretionlenght > 47) {
            $this->SetXY(122, 67.7);
            $this->SetFont('times', '', 9);
            $this->setCellPaddings(0, 0, 0, 0);
            $this->Cell(106, 4, $Ddireccion2, 0, 1, 'L', 0, '', 0, false, '', 'B');
        }

        $this->SetXY(136, 71.7);//122
        $this->SetFont('times', '', 7);
        $this->setCellPaddings(0, 0, 0, 0);
        $this->Cell(24, 4, $Ddistrito, 0, 1, 'L', 0, '', 0,false,'','B');

        $this->SetXY(171, 71.7);
        $this->SetFont('times', '', 7);
        $this->setCellPaddings(0, 0, 0, 0);
        $this->Cell(22, 4, $Dprovincia, 0, 1, 'L', 0, '', 0,false,'','B');

        $this->SetXY(201, 71.7);
        $this->SetFont('times', '', 7);
        $this->setCellPaddings(0, 0, 0, 0);
        $this->Cell(27, 4, $Ddepartamento, 0, 1, 'L', 0, '', 0,false,'','B');

        $this->SetXY(156, 75.7);
        $this->SetFont('times', '', 7);
        $this->setCellPaddings(0, 0, 0, 0);
        $this->Cell(72, 4, $proyecto, 0, 1, 'L', 0, '', 0,false,'','B');

        /**************** DESTINATARIO *****************/
        $this->SetXY(51, 89);
        $this->SetFont('times', '', 9);
        $this->setCellPaddings(0, 0, 0, 0);
        $this->Cell(68, 4, "CONFIPETROL ANDINA S.A.", 0, 1, 'L', 0, '', 0,false,'','B');

        $this->SetXY(24, 94);
        $this->SetFont('times', '', 9);
        $this->setCellPaddings(0, 0, 0, 0);
        $this->Cell(58, 4, "20357259976", 0, 1, 'L', 0, '', 0,false,'','B');

        $datPer1 = explode("-",$movimiento['persona_mov']);
        $namep1 = mb_strtoupper(trim($datPer1[0]),"UTF-8");
        $docp1  = trim($datPer1[1]);
        $datPer2 = explode("-",$movimiento['persona1_mov']);
        $namep2 = mb_strtoupper(trim($datPer2[0]),"UTF-8");
        $docp2  = trim($datPer2[1]);

        $atencionA = "";
        if(!empty($namep1) && !empty($namep2)){ $atencionA = $namep1." / ". $namep2; }
        else if(!empty($namep1) && empty($namep2)){ $atencionA = $namep1; }
        else if(empty($namep1) && !empty($namep2)){ $atencionA = $namep2; }

        $this->SetXY(31, 99);
        $this->SetFont('times', '', 9);
        $this->setCellPaddings(0, 0, 0, 0);
        $this->Cell(85, 4, $atencionA, 0, 1, 'L', 0, '', 0,false,'','B');

        $dniA = "";
        if(!empty($docp1) && !empty($docp2)){ $dniA = $docp1." / ". $docp2; }
        else if(!empty($docp1) && empty($docp2)){ $dniA = $docp1; }
        else if(empty($docp1) && !empty($docp2)){ $dniA = $docp2; }

        $this->SetXY(91, 94);
        $this->SetFont('times', '', 9);
        $this->setCellPaddings(0, 0, 0, 0);
        $this->Cell(29, 4, $dniA, 0, 1, 'L', 0, '', 0,false,'','B');

            /**************** LISTA DE ITEMS *****************/
        $obj_mv = new MovimientoModel();
        $lstItemMov = $obj_mv->lista_MovimientoTransitoDetalle_xIdMovimiento($movimiento['id_movt']);

        $line = 112.5;
        $increment = 1;
        foreach ($lstItemMov as $itemMov){
            $desItem = $itemMov['des_mde']." *** ".$itemMov['nparte_mde'];
            //verificamos si es reeembolsable
            if((int)$itemMov['id_cla'] == 6){
                $desItem = $itemMov['des_mde'];
                if(!is_null($itemMov['cmapel_mde']) && !is_null($itemMov['conu_mde'])){
                  $desItem = $itemMov['des_mde']." *** ".$itemMov['cmapel_mde']."/".$itemMov['conu_mde'];
                }
                if(!is_null($itemMov['cmapel_mde']) && is_null($itemMov['conu_mde'])){
                    $desItem = $itemMov['des_mde']." *** ".$itemMov['cmapel_mde'];
                }
                if(is_null($itemMov['cmapel_mde']) && !is_null($itemMov['conu_mde'])){
                    $desItem = $itemMov['des_mde']." *** ".$itemMov['conu_mde'];
                }
            }

            $this->SetFont('times', '', 8);
            $this->setCellPaddings(0, 0, 0, 0);
            $this->SetXY(10, $line);
            $this->Cell(11, 5, $increment, 0, 1, 'C', 0, '', 0,false,'','B');

            $this->SetXY(22, $line);
            $this->Cell(161, 5, $desItem, 0, 1, 'L', 0, '', 1,false,'','B');


            $this->SetXY(186, $line);
            $this->Cell(19, 5, "UN", 0, 1, 'C', 0, '', 0,false,'','B');

            $this->SetXY(205, $line);
            $this->Cell(25, 5, $itemMov['cant_mde'], 0, 1, 'C', 0, '', 0,false,'','B');
            $line = $line + 5;
            $increment++;
        }
    }
}


$pageLayout = array($width, $height);
// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, $pageLayout, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Dante Castañeda Medina');
$pdf->SetTitle('GUIA DE REMISIÓN');
$pdf->SetSubject('CONTROL DE ALMACEN');

$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

/**************************************************************/
$pdf->setDatos_Movimiento($movimiento,$option);

//Close and output PDF document
$filename = "GUIA-".$movimiento['nroguia_mov'].".pdf";
header('Content-type: application/pdf');
$pdf->Output($filename, 'D'); // D  I

//============================================================+
// END OF FILE
//============================================================+