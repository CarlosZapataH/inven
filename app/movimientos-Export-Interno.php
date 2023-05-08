<?php
require_once '../model/AlmacenModel.php';
require_once '../model/MovimientoModel.php';
require_once '../model/InventarioModel.php';
require_once '../model/UsuarioModel.php';
require_once '../model/PersonaModel.php';
require_once '../model/FuncionesModel.php';
require_once '../assets/plugins/phpspreadsheet-1.17.1.0/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

try {

    $obj_fn = new FuncionesModel();
    if (!empty($_REQUEST['almacen'])) {
        $where[] = " id_alm_ini = " . (int)$_REQUEST['almacen'];
    }

    if (!empty($_REQUEST['transac'])) {
        $arrayTransac = explode(",",$_REQUEST['transac']);
        for($t= 0; $t<sizeof($arrayTransac);$t++) {
            if (trim($arrayTransac[$t]) == "SO"){
                $arrayTransac[] = "SW";
                $arrayTransac[] = "PT";
            }
        }

        $txtAction = "";
        for($i=0; $i<sizeof($arrayTransac); $i++){
            if($i == sizeof($arrayTransac) - 1){
                $txtAction .=  " action_mov = '" . trim($arrayTransac[$i])."' ";
            }
            else{
                $txtAction .=  " action_mov = '" . trim($arrayTransac[$i])."'  OR ";
            }
        }
        $where[] = "(".$txtAction.") ";
    }

    if (!empty($_REQUEST['fecha'])) {
        $fecha = explode("to", $_REQUEST['fecha']);
        $where[] = " ( fecha_mov BETWEEN '" . $obj_fn->fecha_ESP_ENG(trim($fecha[0])) . "' AND '" . $obj_fn->fecha_ESP_ENG(trim($fecha[1])) . "' ) ";
    }

    if (is_array($where)) {
        $where = implode(" AND ", $where);
    }


    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    //----- BEGIN ESTILOS ---------------
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

    $celda_left = [
        'font' => [
            'bold' => true,
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

    $titulo_1 = [
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

    $titulo_2 = [
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

    $borders = [
        'borders' => [
            'top' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => '000000']
            ],
            'bottom' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => '000000']
            ],
            'left' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => '000000']
            ],
            'right' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => '000000']
            ]
        ]
    ];

    $celda_left_center = [
        'font' => [
            'bold' => true,
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

    //----- END ESTILOS ---------------

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

    $cabecera = array(
        0 => 'MOVIMIENTOS',
        1 => 'ÍTEMS'
    );

    $titulos = array(
        /********* MOVIMIENTOS *********/
        0 => 'COD. TRANSAC.',
        1 => 'NRO. TRANSAC.',
        2 => 'ALMACÉN INICIO',
        3 => 'ALMACÉN DESTINO',
        4 => 'ATENCION A',
        5 => 'NRO DOC(DNI/CE)',
        6 => 'AUTORIZADO POR',
        7 => 'ENTREGADO POR',
        8 => 'OBSERVACIÓN.',
        9 => 'MOTIVO',
        10=> 'FECHA TRANSAC.',
        11=> 'USUARIO TRANSAC',
        12=> 'NRO. VALE',
        /********* DETALLE MOVIMIENTOS *********/
        13=> 'CODIGO',
        14=> 'DESCRIPCION',
        15=> 'UNIDAD MEDIDA',
        16=> 'NRO. PARTE',
        17=> 'CANTIDAD TRANSAC',
        18=> 'STOCK ITEM',
        19=> 'COD. ACTIVO',
        20=> 'COD. INVENTARIO',
        21=> 'COD. MAPEL',
        22=> 'COD. ONU'
    );

    $sheet->mergeCells('A1:M1');
    $sheet->setCellValue('A1', $cabecera[0]);
    $sheet->getStyle('A1')->applyFromArray($titulo_1);
    $sheet->mergeCells('N1:W1');
    $sheet->setCellValue('N1', $cabecera[1]);
    $sheet->getStyle('N1')->applyFromArray($titulo_2);
    $sheet->getStyle('A1:W1')->applyFromArray($borders);

    for ($col = 0; $col < sizeof($titulos); $col++) {
        $sheet->setCellValue($letras[$col+65] . '2', $titulos[$col]);
        $sheet->getStyle($letras[$col+65] . '2')->applyFromArray($sub_titulo);
    }

    $sheet->getRowDimension(1)->setRowHeight(20);
    $sheet->getRowDimension(2)->setRowHeight(20);

    $obj_mov = new MovimientoModel();
    $lstMovimientos = $obj_mov->listar_Movimientos_xAlmacen($where);

    $line= 3;
    if (!is_null($lstMovimientos)) {
        $obj_alm = new AlmacenModel();
        $obj_us = new UsuarioModel();
        $obj_per = new PersonaModel();
        foreach ($lstMovimientos as $movimiento) {
            $lineIni = $line;

            $obj_inv = new InventarioModel();
            $lstInventario = $obj_inv->lista_inventario_xIdMovimiento($movimiento['id_mov']);
            $incremento = 0;
            if(is_array($lstInventario)) {
                foreach ($lstInventario as $items) {

                    if (!empty(trim($items['cod_mde'])) && !is_null(trim($items['cod_mde']))) {
                        $sheet->SetCellValue('N' . $line, $items['cod_mde']);
                    }
                    if (!empty(trim($items['des_mde'])) && !is_null(trim($items['des_mde']))) {
                        $sheet->SetCellValue('O' . $line, $obj_fn->quitar_caracteresEspeciales($items['des_mde']));
                    }
                    if (!empty(trim($items['um_mde'])) && !is_null(trim($items['um_mde']))) {
                        $sheet->SetCellValue('P' . $line, $items['um_mde']);
                    }
                    if (!empty(trim($items['nparte_mde'])) && !is_null(trim($items['nparte_mde']))) {
                        $sheet->SetCellValue('Q' . $line, $items['nparte_mde']);
                    }

                    $sheet->SetCellValue('R' . $line, $items['cant_mde']);
                    $sheet->SetCellValue('S' . $line, $items['stock_mde']);

                    if (!empty(trim($items['cactivo_mde'])) && !is_null($items['cactivo_mde'])) {
                        $sheet->SetCellValue('T' . $line, $items['cactivo_mde']);
                    }
                    if (!empty(trim($items['cinventario_mde'])) && !is_null($items['cinventario_mde'])) {
                        $sheet->SetCellValue('U' . $line, $items['cinventario_mde']);
                    }
                    if (!empty(trim($items['cmapel_inv'])) && !is_null($items['cmapel_inv'])) {
                        $sheet->setCellValue('V' . $line, $items['cmapel_inv']);
                    }
                    if (!empty(trim($items['conu_mde'])) && !is_null($items['conu_mde'])) {
                        $sheet->setCellValue('W' . $line, $items['conu_mde']);
                    }

                    $sheet->getRowDimension($line)->setRowHeight(15);
                    $sheet->getStyle("N" . $line . ":W" . $line)->applyFromArray($celda_left);
                    $line++;
                    $incremento++;
                }
            }

            if($incremento > 1){
                $lineaColFin = $line - 1;
                $sheet->mergeCells('A'.$lineIni.':A'.$lineaColFin);
                $sheet->mergeCells('B'.$lineIni.':B'.$lineaColFin);
                $sheet->mergeCells('C'.$lineIni.':C'.$lineaColFin);
                $sheet->mergeCells('D'.$lineIni.':D'.$lineaColFin);
                $sheet->mergeCells('E'.$lineIni.':E'.$lineaColFin);
                $sheet->mergeCells('F'.$lineIni.':F'.$lineaColFin);
                $sheet->mergeCells('G'.$lineIni.':G'.$lineaColFin);
                $sheet->mergeCells('H'.$lineIni.':H'.$lineaColFin);
                $sheet->mergeCells('I'.$lineIni.':I'.$lineaColFin);
                $sheet->mergeCells('J'.$lineIni.':J'.$lineaColFin);
                $sheet->mergeCells('K'.$lineIni.':K'.$lineaColFin);
                $sheet->mergeCells('L'.$lineIni.':L'.$lineaColFin);
                $sheet->mergeCells('M'.$lineIni.':M'.$lineaColFin);

                $sheet->setCellValue('A'.$lineIni, $movimiento['action_mov']);
                $sheet->SetCellValue('B' . $lineIni, $movimiento['nro_mov']);
                if ((int)$movimiento['id_alm_ini'] != 0) {
                    $dtlleAlmacen = $obj_alm->detalle_Almacen_xID($movimiento['id_alm_ini']);
                    $sheet->setCellValue('C' . $lineIni, $obj_fn->quitar_caracteresEspeciales($dtlleAlmacen['titulo_alm']));
                }

                if ((int)$movimiento['id_alm_des'] != 0) {
                    $dtlleAlmacen = $obj_alm->detalle_Almacen_xID($movimiento['id_alm_des']);
                    $sheet->setCellValue('D' . $lineIni, $obj_fn->quitar_caracteresEspeciales($dtlleAlmacen['titulo_alm']));
                }
                if (!empty(trim($movimiento['solicitado_mov'])) && !is_null(trim($movimiento['solicitado_mov']))) {
                    $sheet->setCellValue('E' . $lineIni, $obj_fn->quitar_caracteresEspeciales($movimiento['solicitado_mov']));
                }
                if (!empty(trim($movimiento['recibido_mov'])) && !is_null(trim($movimiento['recibido_mov']))) {
                    $sheet->setCellValue('F' . $lineIni, $obj_fn->quitar_caracteresEspeciales($movimiento['recibido_mov']));
                }
                if (!empty(trim($movimiento['autorizado_mov'])) && !is_null(trim($movimiento['autorizado_mov']))) {
                    $sheet->setCellValue('G' . $lineIni, $obj_fn->quitar_caracteresEspeciales($movimiento['autorizado_mov']));
                }
                if (!empty(trim($movimiento['entregado_mov'])) && !is_null(trim($movimiento['entregado_mov']))) {
                    $sheet->setCellValue('H' . $lineIni, $obj_fn->quitar_caracteresEspeciales($movimiento['entregado_mov']));
                }
                if (!empty(trim($movimiento['observ_mov'])) && !is_null(trim($movimiento['observ_mov']))) {
                    $sheet->setCellValue('I' . $lineIni, $obj_fn->quitar_caracteresEspeciales($movimiento['observ_mov']));
                }
                if (!empty(trim($movimiento['motivo_mov'])) && !is_null(trim($movimiento['motivo_mov']))) {
                    $sheet->setCellValue('J' . $lineIni, $obj_fn->quitar_caracteresEspeciales($movimiento['motivo_mov']));
                }
                if (!empty(trim($movimiento['fecha_mov'])) && !is_null(trim($movimiento['fecha_mov']))) {
                    $sheet->setCellValue('K' . $lineIni, $obj_fn->fecha_ENG_ESP($movimiento['fecha_mov']));
                }
                if ((int)$movimiento['id_us'] != 0) {
                    $dtlleUsuario = $obj_us->detalle_Usuario_xID($movimiento['id_us']);
                    $datPersona = "";
                    if(is_array($dtlleUsuario)){
                        $dtllePersona = $obj_per->detalle_Persona_xID($dtlleUsuario['id_per']);
                        if(is_array($dtllePersona)){
                            $datPersona = $dtllePersona['ape_pa_per']." ".$dtllePersona['nombres_per'];
                        }
                    }
                    $sheet->setCellValue('L' . $lineIni, $datPersona);
                }

                if (!empty(trim($movimiento['nrovale_mov'])) && !is_null($movimiento['nrovale_mov'])) {
                    $sheet->setCellValue('M' . $lineIni, trim($movimiento['nrovale_mov']));
                }
                $sheet->getStyle("A" . $lineIni . ":M" . $lineIni)->applyFromArray($celda_left_center);

            }
            else{
                $sheet->SetCellValue('A' . $lineIni, $movimiento['action_mov']);
                $sheet->SetCellValue('B' . $lineIni, $movimiento['nro_mov']);
                if ((int)$movimiento['id_alm_ini'] != 0) {
                    $dtlleAlmacen = $obj_alm->detalle_Almacen_xID($movimiento['id_alm_ini']);
                    $sheet->setCellValue('C' . $lineIni, $obj_fn->quitar_caracteresEspeciales($dtlleAlmacen['titulo_alm']));
                }
                if ((int)$movimiento['id_alm_des'] != 0) {
                    $dtlleAlmacen = $obj_alm->detalle_Almacen_xID($movimiento['id_alm_des']);
                    $sheet->setCellValue('D' . $lineIni, $obj_fn->quitar_caracteresEspeciales($dtlleAlmacen['titulo_alm']));
                }
                if (!empty(trim($movimiento['solicitado_mov'])) && !is_null(trim($movimiento['solicitado_mov']))) {
                    $sheet->setCellValue('E' . $lineIni, $obj_fn->quitar_caracteresEspeciales($movimiento['solicitado_mov']));
                }
                if (!empty(trim($movimiento['recibido_mov'])) && !is_null(trim($movimiento['recibido_mov']))) {
                    $sheet->setCellValue('F' . $lineIni, $obj_fn->quitar_caracteresEspeciales($movimiento['recibido_mov']));
                }
                if (!empty(trim($movimiento['autorizado_mov'])) && !is_null(trim($movimiento['autorizado_mov']))) {
                    $sheet->setCellValue('G' . $lineIni, $obj_fn->quitar_caracteresEspeciales($movimiento['autorizado_mov']));
                }
                if (!empty(trim($movimiento['entregado_mov'])) && !is_null(trim($movimiento['entregado_mov']))) {
                    $sheet->setCellValue('H' . $lineIni, $obj_fn->quitar_caracteresEspeciales($movimiento['entregado_mov']));
                }
                if (!empty(trim($movimiento['observ_mov'])) && !is_null(trim($movimiento['observ_mov']))) {
                    $sheet->setCellValue('I' . $lineIni, $obj_fn->quitar_caracteresEspeciales($movimiento['observ_mov']));
                }
                if (!empty(trim($movimiento['motivo_mov'])) && !is_null(trim($movimiento['motivo_mov']))) {
                    $sheet->setCellValue('J' . $lineIni, $obj_fn->quitar_caracteresEspeciales($movimiento['motivo_mov']));
                }
                if (!empty(trim($movimiento['fecha_mov'])) && !is_null(trim($movimiento['fecha_mov']))) {
                    $sheet->setCellValue('K' . $lineIni, $obj_fn->fecha_ENG_ESP($movimiento['fecha_mov']));
                }
                if ((int)$movimiento['id_us'] != 0) {
                    $dtlleUsuario = $obj_us->detalle_Usuario_xID($movimiento['id_us']);
                    $datPersona = "";
                    if(is_array($dtlleUsuario)){
                        $dtllePersona = $obj_per->detalle_Persona_xID($dtlleUsuario['id_per']);
                        if(is_array($dtllePersona)){
                            $datPersona = $dtllePersona['ape_pa_per']." ".$dtllePersona['nombres_per'];
                        }
                    }
                    $sheet->setCellValue('L' . $lineIni, $datPersona);
                }

                if (!empty(trim($movimiento['nrovale_mov'])) && !is_null($movimiento['nrovale_mov'])) {
                    $sheet->setCellValue('M' . $lineIni, trim($movimiento['nrovale_mov']));
                }

                $sheet->getStyle("A" . $lineIni . ":M" . $lineIni)->applyFromArray($celda_left);
            }

        }
    }

    $sheet->getColumnDimension('A')->setWidth(15);
    $sheet->getColumnDimension('B')->setWidth(15);
    $sheet->getColumnDimension('C')->setWidth(23);
    $sheet->getColumnDimension('D')->setWidth(23);
    $sheet->getColumnDimension('E')->setWidth(24);
    $sheet->getColumnDimension('F')->setWidth(24);
    $sheet->getColumnDimension('G')->setWidth(24);
    $sheet->getColumnDimension('H')->setWidth(24);
    $sheet->getColumnDimension('I')->setWidth(40);
    $sheet->getColumnDimension('J')->setWidth(40);
    $sheet->getColumnDimension('K')->setWidth(21);
    $sheet->getColumnDimension('L')->setWidth(16);
    $sheet->getColumnDimension('M')->setWidth(20);
    $sheet->getColumnDimension('N')->setWidth(20);
    $sheet->getColumnDimension('O')->setWidth(60);
    $sheet->getColumnDimension('P')->setWidth(17);
    $sheet->getColumnDimension('Q')->setWidth(30);
    $sheet->getColumnDimension('R')->setWidth(22);
    $sheet->getColumnDimension('S')->setWidth(21);
    $sheet->getColumnDimension('T')->setWidth(33);
    $sheet->getColumnDimension('U')->setWidth(17);
    $sheet->getColumnDimension('V')->setWidth(17);
    $sheet->getColumnDimension('W')->setWidth(17);

    //Definimos el Zoom 92%
    $sheet->setTitle("Movimientos");
    $sheet->getSheetView()->setZoomScale(40);

    //inmovilizamos la culumna
    $sheet->freezePane('C3');

    $nombreDelDocumento = "MOV-" . date("d-m-Y") .".xlsx";
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