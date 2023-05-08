<?php
date_default_timezone_set("America/Lima");
setlocale(LC_TIME, 'es_PE.UTF-8');
require_once '../model/InventarioModel.php';
require_once '../model/FuncionesModel.php';
require_once '../assets/plugins/phpspreadsheet-1.17.1.0/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

try {
    $obj_fn = new FuncionesModel();
    $tipoReporte = (int)$_REQUEST['tipo'];
    $idAlm = (int)$_REQUEST['almacen'];

    $obj_alm = new InventarioModel();
    $lstInventario = array();
    if($tipoReporte == 1) {
        $lstInventario = $obj_alm->listar_Inventario_xAlmacen_Rpte($idAlm);
    }
    else if($tipoReporte == 2) {
        $idbai = (int)$_REQUEST['corte'];
        $lstInventario = $obj_alm->listar_Inventario_xCorte_Rpte($idbai);
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    //----- BEGIN ESTILOS ---------------
    $titulo_av = [
        'font' => [
            'bold' => true,
            'italic' => false,
            'underline' => false,
            'strikethrough' => false,
            'color' => ['argb' => '000000'],
            'name' => "calibri",
            'size' => 14
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'color' => ['argb' => '999999']
        ]
    ];
    $titulo_ord = [
        'font' => [
            'bold' => true,
            'italic' => false,
            'underline' => false,
            'strikethrough' => false,
            'color' => ['argb' => '000000'],
            'name' => "calibri",
            'size' => 14
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'color' => ['argb' => '92D050']
        ]
    ];
    $sub_titulo = [
        'font' => [
            'bold' => true,
            'italic' => false,
            'underline' => false,
            'strikethrough' => false,
            'color' => ['argb' => 'FFFFFF'],
            'name' => "calibri",
            'size' => 12
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'color' => ['argb' => 'C0504D']
        ]
    ];
    $celda_center = [
        'font' => [
            'bold' => false,
            'italic' => false,
            'underline' => false,
            'strikethrough' => false,
            'color' => ['argb' => '000000'],
            'name' => "calibri",
            'size' => 10
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER
        ]
    ];
    $celda_left = [
        'font' => [
            'bold' => false,
            'italic' => false,
            'underline' => false,
            'strikethrough' => false,
            'color' => ['argb' => '000000'],
            'name' => "calibri",
            'size' => 10
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_LEFT,
            'vertical' => Alignment::VERTICAL_CENTER
        ]
    ];
    $letras = array(
        65 => 'A', 66 => 'B', 67 => 'C', 68 => 'D', 69 => 'E', 70 => 'F', 71 => 'G', 72 => 'H',
        73 => 'I', 74 => 'J', 75 => 'K', 76 => 'L', 77 => 'M', 78 => 'N', 79 => 'O', 80 => 'P',
        81 => 'Q', 82 => 'R', 83 => 'S', 84 => 'T', 85 => 'U', 86 => 'V', 87 => 'W', 88 => 'X',
        89 => 'Y', 90 => 'Z', 91 => 'AA', 92 => 'AB', 93 => 'AC', 94 => 'AD', 95 => 'AE', 96 => 'AF',
        97 => 'AG', 98 => 'AH', 99 => 'AI', 100 => 'AJ', 101 => 'AK', 102 => 'AL', 103 => 'AM', 104 => 'AN',
        105 => 'AO', 106 => 'AP', 107 => 'AQ', 108 => 'AR', 109 => 'AS', 110 => 'AT', 111 => 'AU', 112 => 'AV',
        113 => 'AW', 114 => 'AX', 115 => 'AY', 116 => 'AZ', 117 => 'BA', 118 => 'BB', 119 => 'BC', 120 => 'BD',
        121 => 'BE', 122 => 'BF', 123 => 'BG', 124 => 'BH', 125 => 'BI', 126 => 'BJ', 127 => 'BK', 128 => 'BL'
    );

    $titulos = array(
        0 => 'UNIDAD',
        1 => 'CODIGO',
        2 => 'CANTIDAD',
        3 => 'DESCRIPCIÓN',
        4 => 'UNID.MEDIDA',
        5 => 'UBICACIÓN',
        6 => 'NRO. PARTE',
        7 => 'RESERVA',
        8 => 'ORDEN MANTTO.',
        9 => 'FECHA PEDIDO',
        10=> 'FECHA REC.',
        11=> 'MARCA',
        12=> 'C.UNIT',
        13=> 'TOTAL',
        14=> 'FECHA INS.',
        15=> 'MEC.',
        16=> 'ORDEN COMPRA',
        17=> 'NRO.VALE',
        18=> 'FECHA RECEPCION ALMACEN',
        19=> 'TOTAL ITEMS PEDIDO',
        20=> 'MOROSIDAD MENSUAL',
        21=> 'ESTADO PEDIDO',
        22=> 'OBSERVACIONES',
        23=> 'CLASIFICACIÓN',
        24=> 'NRO. GUIA',
        25=> 'F.ULT.CALIBRA.',
        26=> 'FRECUENCIA CALIBRA.'
    );

    for ($col = 0; $col < sizeof($titulos); $col++) {
        $sheet->setCellValue($letras[$col+65] . '1', $titulos[$col]);
        $sheet->getStyle($letras[$col+65] . '1')->applyFromArray($sub_titulo);
        $sheet->getStyle($letras[$col+65] . '1')->getAlignment()->setWrapText(true);
    }

    $line = 2;
    if (!is_null($lstInventario)) {
        $obj_fn = new FuncionesModel();
        foreach ($lstInventario as $inventario) {
            $sheet->SetCellValue('A' . $line, $inventario['und_inv']);
            $sheet->SetCellValue('B' . $line, $inventario['cod_inv']);
            $sheet->SetCellValue('C' . $line, $inventario['cant_inv']);
            $sheet->setCellValue('D' . $line, $obj_fn->quitar_caracteresEspeciales($inventario['des_inv']));
            $sheet->setCellValue('E' . $line, $inventario['um_inv']);
            $sheet->setCellValue('F' . $line, $inventario['ubic_inv']);
            $sheet->setCellValue('G' . $line, $inventario['nroparte_inv']);
            $sheet->setCellValue('H' . $line, $inventario['reserva_inv']);
            $sheet->setCellValue('I' . $line, $inventario['om_inv']);

            if (!empty(trim($inventario['fechapedido_inv'])) && trim($inventario['fechapedido_inv']) != "0000-00-00") {
                $sheet->setCellValue('J' . $line, $obj_fn->fecha_ENG_ESP($inventario['fechapedido_inv']));
            }
            $fechaReception = "";
            if (!empty(trim($inventario['fecharec_inv'])) && trim($inventario['fecharec_inv']) != "0000-00-00") {
                $sheet->setCellValue('K' . $line, $obj_fn->fecha_ENG_ESP($inventario['fecharec_inv']));
                $fechaReception = $inventario['fecharec_inv'];
            }
            if (!empty(trim($inventario['marca_inv']))) {
                $sheet->setCellValue('L' . $line, $inventario['marca_inv']);
            }
            if (!empty(trim($inventario['cunit_inv']))) {
                $sheet->setCellValue('M' . $line, $inventario['cunit_inv']);
                $sheet->getStyle('M' . $line)->getNumberFormat()->setFormatCode('0.00');
            }
            if (!empty(trim($inventario['total_inv']))) {
                $sheet->setCellValue('N' . $line, number_format($inventario['total_inv'],2));
                $sheet->getStyle('N' . $line)->getNumberFormat()->setFormatCode('0.00');
            }
            if (!empty(trim($inventario['fechains_inv'])) && trim($inventario['fechains_inv']) != "0000-00-00") {
                $sheet->setCellValue('O' . $line, $obj_fn->fecha_ENG_ESP($inventario['fechains_inv']));
            }
            if (!empty(trim($inventario['mecanico_inv']))) {
                $sheet->setCellValue('P' . $line, $obj_fn->quitar_caracteresEspeciales($inventario['mecanico_inv']));
            }
            if (!empty(trim($inventario['ordencompra_inv']))) {
                $sheet->setCellValue('Q' . $line, $inventario['ordencompra_inv']);
            }
            if (!empty(trim($inventario['numerovale_inv'])) && !is_null($inventario['numerovale_inv'])) {
                $sheet->setCellValue('R' . $line, $inventario['numerovale_inv']);
            }
            if (!empty(trim($inventario['fecharecep_inv'])) && trim($inventario['fecharecep_inv']) != "0000-00-00" && !is_null($inventario['fecharecep_inv'])) {
                $sheet->setCellValue('S' . $line, $obj_fn->fecha_ENG_ESP($inventario['fecharecep_inv']));
            }
            if (!empty(trim($inventario['itempedido_inv'])) && !is_null($inventario['numerovale_inv']) && (int)$inventario['numerovale_inv'] > 0) {
                $sheet->setCellValue('T' . $line, $inventario['itempedido_inv']);
            }
            $morosidadMensual = "SIN REGISTRO";
            if(!empty($fechaReception)){
                $numberMeses = $obj_fn->difMeses($fechaReception,date("Y-m-d"));
                $morosidadMensual = "MENOS DE 1 MES";
                if((int)$numberMeses > 0){
                    $morosidadMensual = $numberMeses;
                }
            }
            $sheet->setCellValue('U' . $line, $morosidadMensual);
            if((int)$inventario['itempedido_inv'] > 0 && !empty(trim($inventario['om_inv']))){
                if($tipoReporte == 1) {
                    $cantRepuesto = $obj_alm->cantidad_Repuesto_Inventario_xAlmacen_OM($idAlm,$inventario['om_inv']);
                }
                else if($tipoReporte == 2) {
                    $cantRepuesto = $obj_alm->cantidad_Repuesto_InventarioBK_xAlmacen_OM($idbai,$inventario['om_inv']);
                }

                $estadoPedido = "INCOMPLETO";
                if((int)$cantRepuesto['cantidad'] == (int)$inventario['itempedido_inv']){ $estadoPedido = "COMPLETO"; }
                $sheet->setCellValue('V' . $line, $estadoPedido);
            }
            if (!empty(trim($inventario['observ_inv']))) {
                $sheet->setCellValue('W' . $line, $obj_fn->quitar_caracteresEspeciales($inventario['observ_inv']));
            }

            if ((int)$inventario['id_cla'] == 1) {
                $sheet->setCellValue('X' . $line, "ACTIVO");
            }
            else if ((int)$inventario['id_cla'] == 2) {
                $sheet->setCellValue('X' . $line, "INSTRUMENTO");
            }
            else if ((int)$inventario['id_cla'] == 3) {
                $sheet->setCellValue('X' . $line, "HERRAMIENTA");
            }
            else if ((int)$inventario['id_cla'] == 4) {
                $sheet->setCellValue('X' . $line, "REPUESTO");
            }
            else{
                $sheet->setCellValue('X' . $line, "OTROS");
            }

            if (!is_null(trim($inventario['nguia_inv']))) {
                $sheet->setCellValue('Y' . $line, trim($inventario['nguia_inv']));
            }

            if (!is_null(trim($inventario['fechaultcalibra_inv']))) {
                if(trim($inventario['fechaultcalibra_inv']) != "0000-00-00") {
                    $sheet->setCellValue('Z' . $line, trim($inventario['fechaultcalibra_inv']));
                }
            }

            if ((int)$inventario['freccalibra_inv'] > 0) {
                $sheet->setCellValue('AA' . $line, (int)$inventario['fechaultcalibra_inv']." Meses");
            }

            $sheet->getRowDimension($line)->setRowHeight(15);
            $sheet->getStyle("A" . $line . ":AA" . $line)->applyFromArray($celda_left);
            $line++;
        }
    }

    $sheet->getColumnDimension('A')->setWidth(10);
    $sheet->getColumnDimension('B')->setWidth(13);
    $sheet->getColumnDimension('C')->setWidth(10);
    $sheet->getColumnDimension('D')->setWidth(45);
    $sheet->getColumnDimension('E')->setWidth(15);
    $sheet->getColumnDimension('F')->setWidth(40);
    $sheet->getColumnDimension('G')->setWidth(13);
    $sheet->getColumnDimension('H')->setWidth(17);
    $sheet->getColumnDimension('I')->setWidth(16);
    $sheet->getColumnDimension('J')->setWidth(16);
    $sheet->getColumnDimension('K')->setWidth(14);
    $sheet->getColumnDimension('L')->setWidth(14);
    $sheet->getColumnDimension('M')->setWidth(14);
    $sheet->getColumnDimension('N')->setWidth(14);
    $sheet->getColumnDimension('O')->setWidth(14);
    $sheet->getColumnDimension('P')->setWidth(14);
    $sheet->getColumnDimension('Q')->setWidth(14);
    $sheet->getColumnDimension('R')->setWidth(22);
    $sheet->getColumnDimension('S')->setWidth(30);
    $sheet->getColumnDimension('T')->setWidth(20);
    $sheet->getColumnDimension('U')->setWidth(18);
    $sheet->getColumnDimension('V')->setWidth(18);
    $sheet->getColumnDimension('W')->setWidth(42);

    $sheet->getColumnDimension('X')->setWidth(16);
    $sheet->getColumnDimension('Y')->setWidth(17);
    $sheet->getColumnDimension('Z')->setWidth(17);
    $sheet->getColumnDimension('AA')->setWidth(22);

    //INMOVILIZAMOS EL PANEL
    $sheet->freezePane('E2');

    //Definimos el Zoom 92%
    $sheet->setTitle("INVENTARIO");
    $sheet->getSheetView()->setZoomScale(86);

    $nombreDelDocumento = "INV-" . date("d-m-Y") .".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $nombreDelDocumento . '"');
    header('Cache-Control: max-age=0');

    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
    exit;
}
catch(Exception $e) {
    die('Error generated file '.$e->getMessage());
}