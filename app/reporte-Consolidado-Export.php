<?php
require_once '../model/FuncionesModel.php';
require_once '../model/MaterialModel.php';
require_once '../model/ColaboradorModel.php';
require_once '../assets/plugins/phpspreadsheet-1.17.1.0/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

try {
    $obj_fn = new FuncionesModel();
    $idColaborador = 0;
    if (!empty($_REQUEST['idcol'])) { $idColaborador = $obj_fn->encrypt_decrypt("decrypt",$_REQUEST['idcol']);}
    $obj_col = new ColaboradorModel();
    $dtlleCol = $obj_col->detalle_Colaborador_xId($idColaborador);


    $fechaInicio = null;
    $fechaFin = null;
    if (!empty($_REQUEST['fDesde_his'])) {
        $fechaInicio = $obj_fn->fecha_ESP_ENG($_REQUEST['fDesde_his']);
    }
    if (!empty($_REQUEST['fHasta_his'])) {
        $fechaFin = $obj_fn->fecha_ESP_ENG($_REQUEST['fHasta_his']);
    }

    $obj_mat = new MaterialModel();
    if(is_null($fechaInicio) && is_null($fechaFin)) {
        $lstDespachos = $obj_mat->lista_Despachos_Detalle_xColaborador($idColaborador);
    }
    else{
        $datosSearch[0] = $idColaborador;
        $datosSearch[1] = trim($fechaInicio);
        $datosSearch[2] = trim($fechaFin);
        $lstDespachos = $obj_mat->lista_Despachos_Detalle_Rango_xColaborador($datosSearch);
    }


    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    //----- BEGIN ESTILOS ---------------
    $fndRuc = [
        'font' => [
            'bold' =>  true,
            'italic' => false,
            'underline' => false,
            'strikethrough' => false,
            'color' => ['argb' => '000000'],
            'name' => "calibri",
            'size' => 10
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_BOTTOM
        ]
    ];
    $fndCodigo = [
        'font' => [
            'bold' =>  true,
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
        ],
        'borders' => [
            'outline' => [
                'borderStyle' => Border::BORDER_THICK,
                'color' => ['argb' => '000000'],
            ],
        ]
    ];
    $fndWhite = [
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'color' => ['argb' => 'FFFFFF']
        ]
    ];
    $fndCeleste_center = [
        'font' => [
            'bold' =>  true,
            'italic' => false,
            'underline' => false,
            'strikethrough' => false,
            'color' => ['argb' => '000000'],
            'name' => "calibri",
            'size' => 11
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'color' => ['argb' => 'BDD7EE']
        ]
    ];
    $fndCeleste_left = [
        'font' => [
            'bold' =>  true,
            'italic' => false,
            'underline' => false,
            'strikethrough' => false,
            'color' => ['argb' => '000000'],
            'name' => "calibri",
            'size' => 11
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_LEFT,
            'vertical' => Alignment::VERTICAL_CENTER
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'color' => ['argb' => 'BDD7EE']
        ]
    ];
    $fndNormalBold = [
        'font' => [
            'bold' =>  true,
            'italic' => false,
            'underline' => false,
            'strikethrough' => false,
            'color' => ['argb' => '000000'],
            'name' => "calibri",
            'size' => 8
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER
        ]
    ];
    $fndNormalBold6 = [
        'font' => [
            'bold' =>  true,
            'italic' => false,
            'underline' => false,
            'strikethrough' => false,
            'color' => ['argb' => '000000'],
            'name' => "calibri",
            'size' => 6
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER
        ]
    ];
    $fndNormalBold9 = [
        'font' => [
            'bold' =>  true,
            'italic' => false,
            'underline' => false,
            'strikethrough' => false,
            'color' => ['argb' => '000000'],
            'name' => "calibri",
            'size' => 9
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_LEFT,
            'vertical' => Alignment::VERTICAL_CENTER
        ]
    ];
    $fndNormalBold9C = [
        'font' => [
            'bold' =>  true,
            'italic' => false,
            'underline' => false,
            'strikethrough' => false,
            'color' => ['argb' => '000000'],
            'name' => "calibri",
            'size' => 9
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER
        ]
    ];
    $fndNormalBold12 = [
        'font' => [
            'bold' =>  true,
            'italic' => false,
            'underline' => false,
            'strikethrough' => false,
            'color' => ['argb' => '000000'],
            'name' => "calibri",
            'size' => 12
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER
        ]
    ];
    $fndNormalC = [
        'font' => [
            'bold' => false,
            'italic' => false,
            'underline' => false,
            'strikethrough' => false,
            'color' => ['argb' => '000000'],
            'name' => "calibri",
            'size' => 8
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER
        ]
    ];
    $fndNormalL = [
        'font' => [
            'bold' => false,
            'italic' => false,
            'underline' => false,
            'strikethrough' => false,
            'color' => ['argb' => '000000'],
            'name' => "calibri",
            'size' => 8
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_LEFT,
            'vertical' => Alignment::VERTICAL_CENTER
        ]
    ];
    $fndNormal10 = [
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
    $fndNormal11 = [
        'font' => [
            'bold' => false,
            'italic' => false,
            'underline' => false,
            'strikethrough' => false,
            'color' => ['argb' => '000000'],
            'name' => "calibri",
            'size' => 11
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER
        ]
    ];
    $fndPlomo = [
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
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'color' => ['argb' => 'BFBFBF']
        ]
    ];
    $borderAll = [
        'borders' => [
            'outline' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => '000000'],
            ],
        ]
    ];
    $borderLR = [
        'borders' => [
            'left' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => '000000'],
            ],
            'right' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => '000000'],
            ]
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

    $cabeceras = array(
        0 => 'CODIGO',
        1 => 'DESCRIPCIÓN DEL EPP',
        2 => 'U.M.',
        3 => 'CANTIDAD',
        4 => 'AREA',
        5 => 'FECHA ENTREGADA',
        6 => 'FECHA RENOVACIÓN ESTIMADA',
        7 => 'FIRMA'
    );

    $spreadsheet
        ->getActiveSheet()
        ->getStyle('A1:AK3000')
        ->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()
        ->setARGB('ffffff');

    /**************************** **********************************/
    /***************** Linea 01 ***************/
    $sheet->getRowDimension(1)->setRowHeight(29.40);
    $sheet->mergeCells('A1:C2');
    $sheet->setCellValue('A1', "RUC 20357259976");
    $sheet->getStyle('A1')->applyFromArray($fndRuc);
    $sheet->getStyle('A1:C2')->applyFromArray($borderAll);

    $drawing = new Drawing();
    $drawing->setName('imagenconfi');
    $drawing->setDescription('Logo');
    $drawing->setPath('../assets/img/reporte/confi.png'); /* put your path and image here */
    $drawing->setCoordinates('B1');
    $drawing->setOffsetX(25);
    $drawing->setOffsetY(2);
    $drawing->setHeight(60);
    $drawing->setWorksheet($spreadsheet->getActiveSheet());
    $sheet->mergeCells('D1:AA1');
    $sheet->setCellValue('D1', "CONFIPETROL");
    $sheet->getStyle('D1')->applyFromArray($fndNormalBold12);
    $sheet->getStyle('D1:AA1')->applyFromArray($borderAll);
    /***************** Linea 02 ***************/
    $sheet->getRowDimension(2)->setRowHeight(29.40);
    $sheet->mergeCells('D2:AA2');
    $sheet->setCellValue('D2', "REPORTE DE REGISTRO DE EQUIPOS DE SEGURIDAD");
    $sheet->getStyle('D2')->applyFromArray($fndNormalBold12);
    $sheet->getStyle('D2:AA2')->applyFromArray($borderAll);
    /***************** Linea 03 ***************/
    $sheet->getRowDimension(3)->setRowHeight(7.20);
    /***************** Linea 04 ***************/
    $sheet->getRowDimension(4)->setRowHeight(15.80);
    $sheet->mergeCells('A4:AA4');
    $sheet->setCellValue('A4', "DATOS DEL EMPLEADOR");
    $sheet->getStyle('A4')->applyFromArray($fndCeleste_left);
    $sheet->getStyle('A4:AA4')->applyFromArray($borderAll);
    /***************** Linea 04 ***************/
    $sheet->getRowDimension(5)->setRowHeight(16.80);
    $sheet->mergeCells('A5:L5');
    $sheet->setCellValue('A5', "RAZÓN SOCIAL");
    $sheet->getStyle('A5')->applyFromArray($fndNormalBold);
    $sheet->getStyle('A5:L5')->applyFromArray($borderAll);
    $sheet->mergeCells('M5:P5');
    $sheet->setCellValue('M5', "RUC");
    $sheet->getStyle('M5')->applyFromArray($fndNormalBold);
    $sheet->getStyle('M5:P5')->applyFromArray($borderAll);
    $sheet->mergeCells('Q5:U5');
    $sheet->setCellValue('Q5', "DOMICILIO (Dirección, distrito, provincia, departamento)");
    $sheet->getStyle('Q5')->applyFromArray($fndNormalBold);
    $sheet->getStyle('Q5:U5')->applyFromArray($borderAll);
    $sheet->mergeCells('V5:Y5');
    $sheet->setCellValue('V5', "ACTIVIDAD ECONÓMICA");
    $sheet->getStyle('V5')->applyFromArray($fndNormalBold);
    $sheet->getStyle('V5:Y5')->applyFromArray($borderAll);
    $sheet->mergeCells('Z5:AA5');
    $sheet->setCellValue('Z5', "N° TRABAJADORES EN EL\nCENTRO LABORAL");
    $sheet->getStyle('Z5')->applyFromArray($fndNormalBold6);
    $sheet->getStyle('Z5:AA5')->applyFromArray($borderAll);
    $sheet->getStyle('Z5:AA5')->getAlignment()->setWrapText(true);
    /***************** Linea 06 ***************/
    $sheet->getRowDimension(6)->setRowHeight(16.80);
    $sheet->mergeCells('A6:L6');
    $sheet->setCellValue('A6', "CONFIPETROL ANDINA SA");
    $sheet->getStyle('A6')->applyFromArray($fndNormal10);
    $sheet->getStyle('A6:L6')->applyFromArray($borderAll);
    $sheet->mergeCells('M6:P6');
    $sheet->setCellValue('M6', "20357259976");
    $sheet->getStyle('M6')->applyFromArray($fndNormal10);
    $sheet->getStyle('M6:P6')->applyFromArray($borderAll);
    $sheet->mergeCells('Q6:U6');
    $sheet->setCellValue('Q6', "AV. SANTO TORIBIO 173 TORRE REAL 102 SAN ISIDRO");
    $sheet->getStyle('Q6')->applyFromArray($fndNormal10);
    $sheet->getStyle('Q6:U6')->applyFromArray($borderAll);
    $sheet->mergeCells('V6:Y6');
    $sheet->setCellValue('V6', "SERVICIO");
    $sheet->getStyle('V6')->applyFromArray($fndNormal10);
    $sheet->getStyle('V6:Y6')->applyFromArray($borderAll);
    $sheet->mergeCells('Z6:AA6');
    $sheet->setCellValue('Z6',"0");
    $sheet->getStyle('Z6')->applyFromArray($fndNormal10);
    $sheet->getStyle('Z6:AA6')->applyFromArray($borderAll);
    /***************** Linea 07 ***************/
    $sheet->getRowDimension(7)->setRowHeight(15.80);
    $sheet->mergeCells('A7:AA7');
    $sheet->setCellValue('A7', "DATOS DEL TRABAJADOR");
    $sheet->getStyle('A7')->applyFromArray($fndCeleste_left);
    $sheet->getStyle('A7:AA7')->applyFromArray($borderAll);
    /***************** Linea 8 ***************/
    $sheet->getRowDimension(8)->setRowHeight(16.50);
    $sheet->mergeCells('A8:V8');
    $sheet->setCellValue('A8', "NOMBRES Y APELLIDOS DEL TRABAJADOR: ");
    $sheet->getStyle('A8')->applyFromArray($fndNormalBold9);
    $sheet->getStyle('A8:V8')->applyFromArray($borderLR);
    $sheet->mergeCells('W8:AA8');
    $sheet->setCellValue('W8', "DNI:");
    $sheet->getStyle('W8')->applyFromArray($fndNormalBold9);
    $sheet->getStyle('W8:AA8')->applyFromArray($borderLR);
    /***************** Linea 9 ***************/
    $sheet->getRowDimension(9)->setRowHeight(16.50);
    $sheet->mergeCells('A9:V9');
    $sheet->setCellValue('A9', $dtlleCol['apa_col']." ".$dtlleCol['ama_col'].", ".$dtlleCol['nombres_col']);
    $sheet->getStyle('A9')->applyFromArray($fndNormal10);
    $sheet->getStyle('A9:V9')->applyFromArray($borderLR);
    $sheet->mergeCells('W9:AA9');
    $sheet->setCellValue('W9', $dtlleCol['ndoc_col']);
    $sheet->getStyle('W9')->applyFromArray($fndNormal10);
    $sheet->getStyle('W9:AA9')->applyFromArray($borderLR);
    /***************** Linea 10 ***************/
    $sheet->getRowDimension(10)->setRowHeight(15.80);
    $sheet->mergeCells('A10:AA10');
    $sheet->setCellValue('A10', "TIPO DE EQUIPO DE SEGURIDAD O EMERGENCIA ENTREGADO");
    $sheet->getStyle('A10')->applyFromArray($fndCeleste_left);
    $sheet->getStyle('A10:AA10')->applyFromArray($borderAll);
    /***************** Linea 11 ***************/
    $sheet->getRowDimension(11)->setRowHeight(19.20);
    $sheet->mergeCells('A11:T11');
    $sheet->setCellValue('A11', "EQUIPO DE PROTECCIÓN PERSONAL");
    $sheet->getStyle('A11')->applyFromArray($fndNormalBold);
    $sheet->getStyle('A11:T11')->applyFromArray($borderAll);
    $sheet->mergeCells('U11:AA11');
    $sheet->setCellValue('U11', "EQUIPO DE EMERGENCIA");
    $sheet->getStyle('U11')->applyFromArray($fndNormalBold);
    $sheet->getStyle('U11:AA11')->applyFromArray($borderAll);
    /***************** Linea 12 ***************/
    $sheet->getRowDimension(12)->setRowHeight(15.80);
    $sheet->mergeCells('A12:T12');
    $sheet->setCellValue('A12', "Casco, Lentes de Seguridad, protector auditivo, respirador y filtros, ropa de trabajo, guantes, zapatos de seguridad y otros");
    $sheet->getStyle('A12')->applyFromArray($fndNormalC);
    $sheet->getStyle('A12:T12')->applyFromArray($borderAll);
    $sheet->mergeCells('U12:AA12');
    $sheet->setCellValue('U12', "*");
    $sheet->getStyle('U12')->applyFromArray($fndNormalC);
    $sheet->getStyle('U12:AA12')->applyFromArray($borderAll);
    /***************** Linea 13 ***************/
    $sheet->getRowDimension(13)->setRowHeight(19.20);
    $sheet->mergeCells('A13:AA13');
    $sheet->setCellValue('A13', "NOMBRE DEL EQUIPO DE SEGURIDAD O EMERGENCIA ENTREGADOS:");
    $sheet->getStyle('A13')->applyFromArray($fndPlomo);
    $sheet->getStyle('A13:AA13')->applyFromArray($borderAll);
    /***************** Linea 14 ***************/
    $sheet->getRowDimension(14)->setRowHeight(31.80);
    $sheet->setCellValue('A14', "N°");
    $sheet->getStyle('A14')->applyFromArray($fndNormalBold9C);
    $sheet->getStyle('A14')->applyFromArray($borderAll);
    $sheet->setCellValue('B14', "SERVICIO");
    $sheet->getStyle('B14')->applyFromArray($fndNormalBold9C);
    $sheet->getStyle('B14')->applyFromArray($borderAll);
    $sheet->setCellValue('C14', "ALMACÉN");
    $sheet->getStyle('C14')->applyFromArray($fndNormalBold9C);
    $sheet->getStyle('C14')->applyFromArray($borderAll);
    $sheet->setCellValue('D14', "NRO\nREGISTRO");
    $sheet->getStyle('D14')->applyFromArray($fndNormalBold9C);
    $sheet->getStyle('D14')->applyFromArray($borderAll);
    $sheet->getStyle('D14')->getAlignment()->setWrapText(true);
    $sheet->setCellValue('E14', "CODIGO");
    $sheet->getStyle('E14')->applyFromArray($fndNormalBold9C);
    $sheet->getStyle('E14')->applyFromArray($borderAll);
    $sheet->mergeCells('F14:Q14');
    $sheet->setCellValue('F14', "DESCRIPCION DEL EPP");
    $sheet->getStyle('F14')->applyFromArray($fndNormalBold9C);
    $sheet->getStyle('F14:Q14')->applyFromArray($borderAll);
    $sheet->setCellValue('R14', "U.M.");
    $sheet->getStyle('R14')->applyFromArray($fndNormalBold9C);
    $sheet->getStyle('R14')->applyFromArray($borderAll);
    $sheet->setCellValue('S14', "CANTIDAD");
    $sheet->getStyle('S14')->applyFromArray($fndNormalBold9C);
    $sheet->getStyle('S14')->applyFromArray($borderAll);
    $sheet->mergeCells('T14:W14');
    $sheet->setCellValue('T14', "AREA");
    $sheet->getStyle('T14')->applyFromArray($fndNormalBold9C);
    $sheet->getStyle('T14:W14')->applyFromArray($borderAll);
    $sheet->mergeCells('X14:Y14');
    $sheet->setCellValue('X14', "FECHA DE\nENTREGA");
    $sheet->getStyle('X14')->applyFromArray($fndNormalBold9C);
    $sheet->getStyle('X14:Y14')->applyFromArray($borderAll);
    $sheet->getStyle('X14:Y14')->getAlignment()->setWrapText(true);

    $sheet->mergeCells('Z14:AA14');
    $sheet->setCellValue('Z14', "FECHA DE RENOVACION\nESTIMADA");
    $sheet->getStyle('Z14')->applyFromArray($fndNormalBold9C);
    $sheet->getStyle('Z14:AA14')->applyFromArray($borderAll);
    $sheet->getStyle('Z14:AA14')->getAlignment()->setWrapText(true);

    /**************************** **********************************/
    $line= 15;
    if(!is_null($lstDespachos)){
        $incremento = 1;
        foreach ($lstDespachos as $material){
            $codigo = explode("-",$material['codigodes']);
            $sheet->setCellValue('A'.$line, $incremento);
            $sheet->getStyle('A'.$line)->applyFromArray($fndNormalC);
            $sheet->getStyle('A'.$line)->applyFromArray($borderAll);
            $sheet->setCellValue('B'.$line, $material['servicio']);
            $sheet->getStyle('B'.$line)->applyFromArray($fndNormalC);
            $sheet->getStyle('B'.$line)->applyFromArray($borderAll);
            $sheet->setCellValue('C'.$line, $material['almacen']);
            $sheet->getStyle('C'.$line)->applyFromArray($fndNormalC);
            $sheet->getStyle('C'.$line)->applyFromArray($borderAll);
            $sheet->setCellValue('D'.$line, (int)$codigo[2]);
            $sheet->getStyle('D'.$line)->applyFromArray($fndNormalC);
            $sheet->getStyle('D'.$line)->applyFromArray($borderAll);
            $sheet->setCellValue('E'.$line, $material['codigo']);
            $sheet->getStyle('E'.$line)->applyFromArray($fndNormalC);
            $sheet->getStyle('E'.$line)->applyFromArray($borderAll);
            $sheet->mergeCells('F'.$line.':Q'.$line);
            $sheet->setCellValue('F'.$line, $material['descripcion']);
            $sheet->getStyle('F'.$line)->applyFromArray($fndNormalL);
            $sheet->getStyle('F'.$line.':Q'.$line)->applyFromArray($borderAll);
            $sheet->setCellValue('R'.$line, $material['unidadm']);
            $sheet->getStyle('R'.$line)->applyFromArray($fndNormalC);
            $sheet->getStyle('R'.$line)->applyFromArray($borderAll);
            $sheet->setCellValue('S'.$line, $material['cantidad']);
            $sheet->getStyle('S'.$line)->applyFromArray($fndNormalC);
            $sheet->getStyle('S'.$line)->applyFromArray($borderAll);
            $sheet->mergeCells('T'.$line.':W'.$line);
            $sheet->setCellValue('T'.$line, $material['area']);
            $sheet->getStyle('T'.$line)->applyFromArray($fndNormalC);
            $sheet->getStyle('T'.$line.':W'.$line)->applyFromArray($borderAll);
            $sheet->mergeCells('X'.$line.':Y'.$line);
            $sheet->setCellValue('X'.$line, $material['fechaentrega']);
            $sheet->getStyle('X'.$line)->applyFromArray($fndNormalC);
            $sheet->getStyle('X'.$line.':Y'.$line)->applyFromArray($borderAll);

            $sheet->mergeCells('Z'.$line.':AA'.$line);
            if((int)$material['periodo']> 0){
                $sheet->setCellValue('Z'.$line, $material['periodo']." MESES");
                $sheet->getStyle('Z'.$line)->applyFromArray($fndNormalC);
            }
            $sheet->getStyle('Z'.$line.':AA'.$line)->applyFromArray($borderAll);

            $sheet->getRowDimension($line)->setRowHeight(31.80);
            $line++;
            $incremento++;
        }
    }

    $sheet->getColumnDimension('A')->setWidth(3);
    $sheet->getColumnDimension('B')->setWidth(19);
    $sheet->getColumnDimension('C')->setWidth(18);
    $sheet->getColumnDimension('D')->setWidth(9);
    $sheet->getColumnDimension('E')->setWidth(9);
    $sheet->getColumnDimension('F')->setWidth(2.4);
    $sheet->getColumnDimension('G')->setWidth(2.4);
    $sheet->getColumnDimension('H')->setWidth(2.4);
    $sheet->getColumnDimension('I')->setWidth(2.4);
    $sheet->getColumnDimension('J')->setWidth(2.4);
    $sheet->getColumnDimension('K')->setWidth(2.4);
    $sheet->getColumnDimension('L')->setWidth(2.4);
    $sheet->getColumnDimension('M')->setWidth(4);
    $sheet->getColumnDimension('N')->setWidth(4);
    $sheet->getColumnDimension('O')->setWidth(4);
    $sheet->getColumnDimension('P')->setWidth(4);
    $sheet->getColumnDimension('Q')->setWidth(4);
    $sheet->getColumnDimension('R')->setWidth(8.5);
    $sheet->getColumnDimension('S')->setWidth(8.5);
    $sheet->getColumnDimension('T')->setWidth(12);
    $sheet->getColumnDimension('U')->setWidth(12);
    $sheet->getColumnDimension('V')->setWidth(3.5);
    $sheet->getColumnDimension('W')->setWidth(3.5);
    $sheet->getColumnDimension('X')->setWidth(6.2);
    $sheet->getColumnDimension('Y')->setWidth(6.2);
    $sheet->getColumnDimension('Z')->setWidth(8.22);
    $sheet->getColumnDimension('AA')->setWidth(8.22);

    //Definimos el Zoom 92%
    $sheet->setTitle("REG. EQUIP. DE SEGURIDAD");
    $sheet->getSheetView()->setZoomScale(115);

    $nombreDelDocumento = "HIST-".$idColaborador."-".date("dmY").".xlsx";
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