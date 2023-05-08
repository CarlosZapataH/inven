<?php
require_once '../model/FuncionesModel.php';
require_once '../model/MaterialModel.php';
require_once '../assets/plugins/phpspreadsheet-1.17.1.0/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;


try {

    $idAlmacen = 0;
    if (!empty($_REQUEST['almacen'])) { $idAlmacen = (int)$_REQUEST['almacen'];}

    $fechaInicio = null;
    $fechaFin = null;
    $obj_fn = new FuncionesModel();
    if (!empty($_REQUEST['fechaini'])) {
        $fechaInicio = $obj_fn->fecha_ESP_ENG($_REQUEST['fechaini']);
    }
    if (!empty($_REQUEST['fechafin'])) {
        $fechaFin = $obj_fn->fecha_ESP_ENG($_REQUEST['fechafin']);
    }

    $rangoFecha = "";
    if(!is_null($fechaInicio) && !is_null($fechaFin)){
        $rangoFecha = "DEL ".$obj_fn->fecha_ENG_ESP($fechaInicio)." AL ".$obj_fn->fecha_ENG_ESP($fechaFin);
        if(trim($fechaInicio) == trim($fechaFin)){
            $rangoFecha = "DEL ".$obj_fn->fecha_ENG_ESP($fechaInicio);
        }
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    //----- BEGIN ESTILOS ---------------
    $titulo = [
        'font' => [
            'bold' => true,
            'italic' => false,
            'underline' => false,
            'strikethrough' => false,
            'color' => ['argb' => '000000'],
            'name' => "calibri",
            'size' => 16
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER
        ]
    ];
    $columnas = [
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
            'color' => ['argb' => '000058']
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
        0 => 'ALMACEN',
        1 => 'COLABORADOR',
        2 => 'AREA',
        3 => 'FECHA TRANSAC.',
        4 => 'HORA TRANSAC.',
        5 => 'DESPACHADO POR',
        6 => 'NRO. REGISTRO',
        7 => 'CLASIFICACIÓN',
        8 => 'CÓDIGO',
        9 => 'DESCRIPCIÓN',
        10=> 'U.M.',
        11=> 'CANT. RETIRADA'
    );

    $sheet->mergeCells('A1:L1');
    $sheet->setCellValue('A1', "REPORTE DE CONSUMO DE STOCK ".$rangoFecha);
    $sheet->getStyle('A1')->applyFromArray($titulo);

    for ($col = 0; $col < sizeof($titulos); $col++) {
        $sheet->setCellValue($letras[$col+65] . '3', $titulos[$col]);
        $sheet->getStyle($letras[$col+65] . '3')->applyFromArray($columnas);
    }

    $sheet->getRowDimension(1)->setRowHeight(21);
    $sheet->getRowDimension(2)->setRowHeight(15);

    $datosSearch[0] = $idAlmacen;
    $datosSearch[1] = trim($fechaInicio);
    $datosSearch[2] = trim($fechaFin);
    $obj_mat = new MaterialModel();
    $lstConsumos = $obj_mat->lista_Consumos_Rango_xAlmacen($datosSearch);

    $line= 4;
    if (!is_null($lstConsumos)) {
        foreach ($lstConsumos as $movimiento) {
            $codigo = explode("-",$movimiento['codigodes']);
            $sheet->SetCellValue('A' . $line, $movimiento['almacen']);
            $sheet->SetCellValue('B' . $line, $movimiento['colaborador']);
            $sheet->SetCellValue('C' . $line, $movimiento['area']);
            $sheet->SetCellValue('D' . $line, $movimiento['fechaentrega']);
            $sheet->SetCellValue('E' . $line, $movimiento['horaentrega']);
            $sheet->SetCellValue('F' . $line, $movimiento['creadopor']);
            $sheet->SetCellValue('G' . $line, str_pad((int)$codigo[2],3,"0",STR_PAD_LEFT));
            $sheet->SetCellValue('H' . $line, $movimiento['clasificacion']);
            $sheet->SetCellValue('I' . $line, $movimiento['codigo']);
            $sheet->SetCellValue('J' . $line, $movimiento['descripcion']);
            $sheet->SetCellValue('K' . $line, $movimiento['unidadm']);
            $sheet->SetCellValue('L' . $line, $movimiento['cantidad']);

            $sheet->getRowDimension($line)->setRowHeight(15);
            $sheet->getStyle("A" . $line . ":L" . $line)->applyFromArray($celda_center);
            $line++;
        }
    }

    $sheet->getColumnDimension('A')->setWidth(24);
    $sheet->getColumnDimension('B')->setWidth(32);
    $sheet->getColumnDimension('C')->setWidth(18);
    $sheet->getColumnDimension('D')->setWidth(19);
    $sheet->getColumnDimension('E')->setWidth(17);
    $sheet->getColumnDimension('F')->setWidth(35);
    $sheet->getColumnDimension('G')->setWidth(17);
    $sheet->getColumnDimension('H')->setWidth(40);
    $sheet->getColumnDimension('I')->setWidth(12);
    $sheet->getColumnDimension('J')->setWidth(60);
    $sheet->getColumnDimension('K')->setWidth(9);
    $sheet->getColumnDimension('L')->setWidth(18);

    //Definimos el Zoom 92%
    $sheet->setTitle("Data");
    $sheet->getSheetView()->setZoomScale(90);

    //inmovilizamos la culumna
    $sheet->freezePane('C4');

    $nombreDelDocumento = "CONSUMOS-".$idAlmacen."-".date("dmY") .".xlsx";
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