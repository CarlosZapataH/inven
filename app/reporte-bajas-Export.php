<?php
require_once '../model/AlmacenModel.php';
require_once '../model/InventarioModel.php';
require_once '../model/FuncionesModel.php';
require_once '../assets/plugins/phpspreadsheet-1.17.1.0/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

try {
    $obj_fn = new FuncionesModel();

    $IdAlmacen = (int)$_REQUEST['almacen'];
    $obj_alm = new AlmacenModel();
    $dtlleAlmacen = $obj_alm->detalle_Almacen_xID($IdAlmacen);


    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    //----- BEGIN ESTILOS ---------------
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

    $titulo_1 = [
        'font' => [
            'bold' => true,
            'italic' => false,
            'underline' => false,
            'strikethrough' => false,
            'color' => ['argb' => '000000'],
            'name' => "calibri",
            'size' => 18
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER
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

    $titulos = array(
        /********* MOVIMIENTOS *********/
        0 => 'ITEM',
        1 => 'CODIGO',
        2 => 'DESCRIPCIÓN',
        3 => 'TIPO',
        4 => 'REALIZADO POR',
        5 => 'REALIZADO A LAS',
        6 => 'MOTIVO BAJA',
    );

    $sheet->mergeCells('A1:G1');
    $sheet->setCellValue('A1', "LISTA DE BAJAS ALMACÉN: ".$dtlleAlmacen['titulo_alm']);
    $sheet->getStyle('A1')->applyFromArray($titulo_1);

    for ($col = 0; $col < sizeof($titulos); $col++) {
        $sheet->setCellValue($letras[$col+65] . '2', $titulos[$col]);
        $sheet->getStyle($letras[$col+65] . '2')->applyFromArray($titulo_2);
    }

    $sheet->getRowDimension(1)->setRowHeight(30);
    $sheet->getRowDimension(2)->setRowHeight(20);

    $obj_inv = new InventarioModel();
    $lstItemBaja = $obj_inv->lista_Items_xBaja_xIdAlmacen($IdAlmacen);

    $line= 3;
    if (!is_null($lstItemBaja)) {
        $obj_fn = new FuncionesModel();
        $incremento = 1;
        foreach ($lstItemBaja as $baja) {
            $sheet->SetCellValue('A' . $line, $incremento);
            $sheet->SetCellValue('B' . $line, $baja['cod_inb']);
            $sheet->SetCellValue('C' . $line, $baja['des_inb']);
            $sheet->SetCellValue('D' . $line, strtoupper($baja['tipo_inb']));
            $sheet->SetCellValue('E' . $line, $baja['persona_us']);
            $sheet->SetCellValue('F' . $line, $obj_fn->fechaHora_ENG_ESP($baja['fechareg_inb']));
            $sheet->SetCellValue('G' . $line, $baja['textbaja_inb']);

            $sheet->getStyle('G' . $line)->getAlignment()->setWrapText(true);
            $sheet->getRowDimension($line)->setRowHeight(15);
            $sheet->getStyle("A" . $line . ":G" . $line)->applyFromArray($celda_left);
            $line++;
            $incremento++;
        }
    }

    $sheet->getColumnDimension('A')->setWidth(8);
    $sheet->getColumnDimension('B')->setWidth(15);
    $sheet->getColumnDimension('C')->setWidth(40);
    $sheet->getColumnDimension('D')->setWidth(18);
    $sheet->getColumnDimension('E')->setWidth(25);
    $sheet->getColumnDimension('F')->setWidth(25);
    $sheet->getColumnDimension('G')->setWidth(40);

    //Definimos el Zoom 92%
    $sheet->setTitle("Datos");
    $sheet->getSheetView()->setZoomScale(85);

    //inmovilizamos la culumna
    $sheet->freezePane('C3');

    $nombreDelDocumento = "Bajas-" . date("d-m-Y") .".xlsx";
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