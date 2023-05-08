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
    $idDespacho = 0;
    if (!empty($_REQUEST['idDespacho'])) { $idDespacho = $obj_fn->encrypt_decrypt("decrypt",$_REQUEST['idDespacho']);}

    $obj_mat = new MaterialModel();
    $detalleDespacho = $obj_mat->detalle_Despacho_xID($idDespacho);
    $totalColab = 0;
    $obj_col = new ColaboradorModel();
    $fechaDespacho = date("dmY");
    if(!is_null($detalleDespacho)){
        $fechades = explode("-",$detalleDespacho['fecha_des']);
        $fechaDespacho = $fechades[2].$fechades[1].$fechades[0];
        $nregistrosCol = $obj_col->numero_Colaborador_xServicio($detalleDespacho['id_serv']);
        if(!is_null($nregistrosCol)){
            $totalColab = (int)$nregistrosCol['registros']; // esto esta mal
        }
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
    $sheet->mergeCells('A1:I2');
    $sheet->setCellValue('A1', "RUC 20357259976");
    $sheet->getStyle('A1')->applyFromArray($fndRuc);
    $sheet->getStyle('A1:I2')->applyFromArray($borderAll);

    $drawing = new Drawing();
    $drawing->setName('imagenconfi');
    $drawing->setDescription('Logo');
    $drawing->setPath('../assets/img/reporte/confi.png'); /* put your path and image here */
    $drawing->setCoordinates('B1');
    $drawing->setOffsetX(25);
    $drawing->setOffsetY(2);
    $drawing->setHeight(60);
    $drawing->setWorksheet($spreadsheet->getActiveSheet());

    $sheet->mergeCells('J1:Y1');
    $sheet->setCellValue('J1', "CONFIPETROL");
    $sheet->getStyle('J1')->applyFromArray($fndNormalBold12);
    $sheet->getStyle('J1:Y1')->applyFromArray($borderAll);
    $sheet->mergeCells('Z1:AC2');
    $sheet->setCellValue('Z1', "Código: HSEQ-S&SO1-F-79\nVersión: 0\nFecha:  27-10-2016\nPágina:  1  de 1");
    $sheet->getStyle('Z1')->applyFromArray($fndCodigo);
    $sheet->getStyle('Z1:AC2')->applyFromArray($borderAll)->getAlignment()->setWrapText(true);
    /***************** Linea 01 ***************/
    $sheet->getRowDimension(2)->setRowHeight(29.40);
    $sheet->mergeCells('J2:Y2');
    $sheet->setCellValue('J2', "REGISTRO DE EQUIPOS DE SEGURIDAD");
    $sheet->getStyle('J2')->applyFromArray($fndNormalBold12);
    $sheet->getStyle('J2:Y2')->applyFromArray($borderAll);
    /***************** Linea 03 ***************/
    $sheet->getRowDimension(3)->setRowHeight(7.20);
    /***************** Linea 04 ***************/
    $sheet->getRowDimension(7)->setRowHeight(16.80);
    $sheet->mergeCells('A4:G4');
    $sheet->setCellValue('A4', "N° DE REGISTRO");
    $sheet->getStyle('A4')->applyFromArray($fndCeleste_center);
    $sheet->getStyle('A4:G4')->applyFromArray($borderAll);
    $sheet->mergeCells('H4:N4');
    $sheet->setCellValue('H4', str_pad($detalleDespacho['codigo_des'],6,"0",STR_PAD_LEFT));
    $sheet->getStyle('H4')->applyFromArray($fndNormal11);
    $sheet->getStyle('H4:N4')->applyFromArray($borderAll);
    /***************** Linea 05 ***************/
    $sheet->getRowDimension(5)->setRowHeight(7.20);
    /***************** Linea 06 ***************/
    $sheet->getRowDimension(6)->setRowHeight(15.80);
    $sheet->mergeCells('A6:AC6');
    $sheet->setCellValue('A6', "DATOS DEL EMPLEADOR");
    $sheet->getStyle('A6')->applyFromArray($fndCeleste_left);
    $sheet->getStyle('A6:AC6')->applyFromArray($borderAll);
    /***************** Linea 07 ***************/
    $sheet->getRowDimension(7)->setRowHeight(16.80);
    $sheet->mergeCells('A7:L7');
    $sheet->setCellValue('A7', "RAZÓN SOCIAL");
    $sheet->getStyle('A7')->applyFromArray($fndNormalBold);
    $sheet->getStyle('A7:L7')->applyFromArray($borderAll);
    $sheet->mergeCells('M7:P7');
    $sheet->setCellValue('M7', "RUC");
    $sheet->getStyle('M7')->applyFromArray($fndNormalBold);
    $sheet->getStyle('M7:P7')->applyFromArray($borderAll);
    $sheet->mergeCells('Q7:U7');
    $sheet->setCellValue('Q7', "DOMICILIO (Dirección, distrito, provincia, departamento)");
    $sheet->getStyle('Q7')->applyFromArray($fndNormalBold);
    $sheet->getStyle('Q7:U7')->applyFromArray($borderAll);
    $sheet->mergeCells('V7:AA7');
    $sheet->setCellValue('V7', "ACTIVIDAD ECONÓMICA");
    $sheet->getStyle('V7')->applyFromArray($fndNormalBold);
    $sheet->getStyle('V7:AA7')->applyFromArray($borderAll);
    $sheet->mergeCells('AB7:AC7');
    $sheet->setCellValue('AB7', "N° TRABAJADORES EN EL\nCENTRO LABORAL");
    $sheet->getStyle('AB7')->applyFromArray($fndNormalBold6);
    $sheet->getStyle('AB7:AC7')->applyFromArray($borderAll);
    $sheet->getStyle('AB7:AC7')->getAlignment()->setWrapText(true);
    /***************** Linea 08 ***************/
    $sheet->getRowDimension(8)->setRowHeight(16.80);
    $sheet->mergeCells('A8:L8');
    $sheet->setCellValue('A8', "CONFIPETROL ANDINA SA");
    $sheet->getStyle('A8')->applyFromArray($fndNormal10);
    $sheet->getStyle('A8:L8')->applyFromArray($borderAll);
    $sheet->mergeCells('M8:P8');
    $sheet->setCellValue('M8', "20357259976");
    $sheet->getStyle('M8')->applyFromArray($fndNormal10);
    $sheet->getStyle('M8:P8')->applyFromArray($borderAll);
    $sheet->mergeCells('Q8:U8');
    $sheet->setCellValue('Q8', "AV. SANTO TORIBIO 173 TORRE REAL 102 SAN ISIDRO");
    $sheet->getStyle('Q8')->applyFromArray($fndNormal10);
    $sheet->getStyle('Q8:U8')->applyFromArray($borderAll);
    $sheet->mergeCells('V8:AA8');
    $sheet->setCellValue('V8', "SERVICIO");
    $sheet->getStyle('V8')->applyFromArray($fndNormal10);
    $sheet->getStyle('V8:AA8')->applyFromArray($borderAll);
    $sheet->mergeCells('AB8:AC8');
    $sheet->setCellValue('AB8',$totalColab);
    $sheet->getStyle('AB8')->applyFromArray($fndNormal10);
    $sheet->getStyle('AB8:AC8')->applyFromArray($borderAll);
    /***************** Linea 09 ***************/
    $sheet->getRowDimension(9)->setRowHeight(15.80);
    $sheet->mergeCells('A9:AC9');
    $sheet->setCellValue('A9', "DATOS DEL TRABAJADOR");
    $sheet->getStyle('A9')->applyFromArray($fndCeleste_left);
    $sheet->getStyle('A9:AC9')->applyFromArray($borderAll);
    /***************** Linea 10 ***************/
    $sheet->getRowDimension(10)->setRowHeight(16.50);
    $sheet->mergeCells('A10:P10');
    $sheet->setCellValue('A10', "NOMBRES Y APELLIDOS DEL TRABAJADOR: ");
    $sheet->getStyle('A10')->applyFromArray($fndNormalBold9);
    $sheet->getStyle('A10:P10')->applyFromArray($borderLR);
    $sheet->mergeCells('Q10:R10');
    $sheet->setCellValue('Q10', "DNI:");
    $sheet->getStyle('Q10')->applyFromArray($fndNormalBold9);
    $sheet->getStyle('Q10:R10')->applyFromArray($borderLR);
    $sheet->mergeCells('S10:W10');
    $sheet->setCellValue('S10', "SERVICIO:");
    $sheet->getStyle('S10')->applyFromArray($fndNormalBold9);
    $sheet->getStyle('S10:W10')->applyFromArray($borderLR);
    $sheet->mergeCells('X10:AC10');
    $sheet->setCellValue('X10', "ALMACÉN:");
    $sheet->getStyle('X10')->applyFromArray($fndNormalBold9);
    $sheet->getStyle('X10:AC10')->applyFromArray($borderLR);
    /***************** Linea 11 ***************/
    $sheet->getRowDimension(11)->setRowHeight(16.50);
    $sheet->mergeCells('A11:P11');
    $sheet->setCellValue('A11', $detalleDespacho['solicitadopor_des']);
    $sheet->getStyle('A11')->applyFromArray($fndNormal10);
    $sheet->getStyle('A11:P11')->applyFromArray($borderLR);
    $sheet->mergeCells('Q11:R11');
    $sheet->setCellValue('Q11', $detalleDespacho['ndoc_des']);
    $sheet->getStyle('Q11')->applyFromArray($fndNormal10);
    $sheet->getStyle('Q11:R11')->applyFromArray($borderLR);
    $sheet->mergeCells('S11:W11');
    $sheet->setCellValue('S11', $detalleDespacho['desserv_des']);
    $sheet->getStyle('S11')->applyFromArray($fndNormal10);
    $sheet->getStyle('S11:W11')->applyFromArray($borderLR);
    $sheet->mergeCells('X11:AC11');
    $sheet->setCellValue('X11', $detalleDespacho['desalm_des']);
    $sheet->getStyle('X11')->applyFromArray($fndNormal10);
    $sheet->getStyle('X11:AC11')->applyFromArray($borderLR);
    /***************** Linea 12 ***************/
    $sheet->getRowDimension(12)->setRowHeight(15.80);
    $sheet->mergeCells('A12:AC12');
    $sheet->setCellValue('A12', "TIPO DE EQUIPO DE SEGURIDAD O EMERGENCIA ENTREGADO");
    $sheet->getStyle('A12')->applyFromArray($fndCeleste_left);
    $sheet->getStyle('A12:AC12')->applyFromArray($borderAll);
    /***************** Linea 13 ***************/
    $sheet->getRowDimension(13)->setRowHeight(19.20);
    $sheet->mergeCells('A13:T13');
    $sheet->setCellValue('A13', "EQUIPO DE PROTECCIÓN PERSONAL");
    $sheet->getStyle('A13')->applyFromArray($fndNormalBold);
    $sheet->getStyle('A13:T13')->applyFromArray($borderAll);
    $sheet->mergeCells('U13:AC13');
    $sheet->setCellValue('U13', "EQUIPO DE EMERGENCIA");
    $sheet->getStyle('U13')->applyFromArray($fndNormalBold);
    $sheet->getStyle('U13:AC13')->applyFromArray($borderAll);
    /***************** Linea 14 ***************/
    $sheet->getRowDimension(14)->setRowHeight(15.80);
    $sheet->mergeCells('A14:T14');
    $sheet->setCellValue('A14', "Casco, Lentes de Seguridad, protector auditivo, respirador y filtros, ropa de trabajo, guantes, zapatos de seguridad y otros");
    $sheet->getStyle('A14')->applyFromArray($fndNormalC);
    $sheet->getStyle('A14:T14')->applyFromArray($borderAll);
    $sheet->mergeCells('U14:AC14');
    $sheet->setCellValue('U14', "*");
    $sheet->getStyle('U14')->applyFromArray($fndNormalC);
    $sheet->getStyle('U14:AC14')->applyFromArray($borderAll);
    /***************** Linea 15 ***************/
    $sheet->getRowDimension(15)->setRowHeight(19.20);
    $sheet->mergeCells('A15:AC15');
    $sheet->setCellValue('A15', "NOMBRE DEL EQUIPO DE SEGURIDAD O EMERGENCIA ENTREGADOS:");
    $sheet->getStyle('A15')->applyFromArray($fndPlomo);
    $sheet->getStyle('A15:AC15')->applyFromArray($borderAll);
    /***************** Linea 16 ***************/
    $sheet->getRowDimension(16)->setRowHeight(31.80);
    $sheet->setCellValue('A16', "N°");
    $sheet->getStyle('A16')->applyFromArray($fndNormalBold9C);
    $sheet->getStyle('A16')->applyFromArray($borderAll);
    $sheet->setCellValue('B16', "CODIGO");
    $sheet->getStyle('B16')->applyFromArray($fndNormalBold9C);
    $sheet->getStyle('B16')->applyFromArray($borderAll);
    $sheet->mergeCells('C16:O16');
    $sheet->setCellValue('C16', "DESCRIPCION DEL EPP");
    $sheet->getStyle('C16')->applyFromArray($fndNormalBold9C);
    $sheet->getStyle('C16:O16')->applyFromArray($borderAll);
    $sheet->mergeCells('P16:Q16');
    $sheet->setCellValue('P16', "U.M.");
    $sheet->getStyle('P16')->applyFromArray($fndNormalBold9C);
    $sheet->getStyle('P16:Q16')->applyFromArray($borderAll);
    $sheet->setCellValue('R16', "CANTIDAD");
    $sheet->getStyle('R16')->applyFromArray($fndNormalBold9C);
    $sheet->getStyle('R16')->applyFromArray($borderAll);
    $sheet->setCellValue('S16', "AREA");
    $sheet->getStyle('S16')->applyFromArray($fndNormalBold9C);
    $sheet->getStyle('S16')->applyFromArray($borderAll);
    $sheet->mergeCells('T16:V16');
    $sheet->setCellValue('T16', "FECHA DE\nENTREGA");
    $sheet->getStyle('T16')->applyFromArray($fndNormalBold9C);
    $sheet->getStyle('T16:V16')->applyFromArray($borderAll);
    $sheet->getStyle('T16:V16')->getAlignment()->setWrapText(true);
    $sheet->mergeCells('W16:Z16');
    $sheet->setCellValue('W16', "FECHA DE RENOVACION\nESTIMADA");
    $sheet->getStyle('W16')->applyFromArray($fndNormalBold9C);
    $sheet->getStyle('W16:Z16')->applyFromArray($borderAll);
    $sheet->getStyle('W16:Z16')->getAlignment()->setWrapText(true);
    $sheet->mergeCells('AA16:AC16');
    $sheet->setCellValue('AA16', "FIRMA");
    $sheet->getStyle('AA16')->applyFromArray($fndNormalBold9C);
    $sheet->getStyle('AA16:AC16')->applyFromArray($borderAll);

    /**************************** **********************************/
    $line= 17;
    if(!is_null($detalleDespacho)){
        $lstMateriales = $obj_mat->lista_Materiales_xIdDespacho($detalleDespacho['id_des']);
        if(!is_null($lstMateriales)){
            $incremento = 1;
            foreach ($lstMateriales as $material){
                $sheet->setCellValue('A'.$line, $incremento);
                $sheet->getStyle('A'.$line)->applyFromArray($fndNormalC);
                $sheet->getStyle('A'.$line)->applyFromArray($borderAll);
                $sheet->setCellValue('B'.$line, $material['codigo']);
                $sheet->getStyle('B'.$line)->applyFromArray($fndNormalC);
                $sheet->getStyle('B'.$line)->applyFromArray($borderAll);
                $sheet->mergeCells('C'.$line.':O'.$line);
                $sheet->setCellValue('C'.$line, $material['descripcion']);
                $sheet->getStyle('C'.$line)->applyFromArray($fndNormalL);
                $sheet->getStyle('C'.$line.':O'.$line)->applyFromArray($borderAll);
                $sheet->mergeCells('P'.$line.':Q'.$line);
                $sheet->setCellValue('P'.$line, $material['unidadm']);
                $sheet->getStyle('P'.$line)->applyFromArray($fndNormalC);
                $sheet->getStyle('P'.$line.':Q'.$line)->applyFromArray($borderAll);
                $sheet->setCellValue('R'.$line, $material['cantidad']);
                $sheet->getStyle('R'.$line)->applyFromArray($fndNormalC);
                $sheet->getStyle('R'.$line)->applyFromArray($borderAll);
                $sheet->setCellValue('S'.$line, $material['area']);
                $sheet->getStyle('S'.$line)->applyFromArray($fndNormalC);
                $sheet->getStyle('S'.$line)->applyFromArray($borderAll);
                $sheet->mergeCells('T'.$line.':V'.$line);
                $sheet->setCellValue('T'.$line, $material['fechaentrega']);
                $sheet->getStyle('T'.$line)->applyFromArray($fndNormalC);
                $sheet->getStyle('T'.$line.':V'.$line)->applyFromArray($borderAll);
                $sheet->mergeCells('W'.$line.':Z'.$line);
                if((int)$material['periodo']> 0){
                    $sheet->setCellValue('W'.$line, $material['periodo']." MESES");
                    $sheet->getStyle('W'.$line)->applyFromArray($fndNormalC);
                }
                $sheet->getStyle('W'.$line.':Z'.$line)->applyFromArray($borderAll);
                $sheet->mergeCells('AA'.$line.':AC'.$line);
                $sheet->setCellValue('AA'.$line, "");
                $sheet->getStyle('AA'.$line)->applyFromArray($fndNormalC);
                $sheet->getStyle('AA'.$line.':AC'.$line)->applyFromArray($borderAll);
                $sheet->getRowDimension($line)->setRowHeight(31.80);
                $line++;
                $incremento++;
            }

            if(sizeof($lstMateriales) < 20){
                $inc = $incremento;
                $limit =  20 - sizeof($lstMateriales);
                $line1 = 0;
                for ($i = 0; $i<$limit; $i++){
                    $line1 = $line + $i;
                    $sheet->setCellValue('A'.$line1, $inc+$i);
                    $sheet->getStyle('A'.$line1)->applyFromArray($fndNormalC);
                    $sheet->getStyle('A'.$line1)->applyFromArray($borderAll);
                    $sheet->getStyle('B'.$line1)->applyFromArray($borderAll);
                    $sheet->mergeCells('C'.$line1.':O'.$line1);
                    $sheet->getStyle('C'.$line1.':O'.$line1)->applyFromArray($borderAll);
                    $sheet->mergeCells('P'.$line1.':Q'.$line1);
                    $sheet->getStyle('P'.$line1.':Q'.$line1)->applyFromArray($borderAll);
                    $sheet->getStyle('R'.$line1)->applyFromArray($borderAll);
                    $sheet->getStyle('S'.$line1)->applyFromArray($borderAll);
                    $sheet->mergeCells('T'.$line1.':V'.$line1);
                    $sheet->getStyle('T'.$line1.':V'.$line1)->applyFromArray($borderAll);
                    $sheet->mergeCells('W'.$line1.':Z'.$line1);
                    $sheet->getStyle('W'.$line.':Z'.$line1)->applyFromArray($borderAll);
                    $sheet->mergeCells('AA'.$line1.':AC'.$line1);
                    $sheet->setCellValue('AA'.$line1, "");
                    $sheet->getStyle('AA'.$line1.':AC'.$line1)->applyFromArray($borderAll);
                    $sheet->getRowDimension($line1)->setRowHeight(31.80);
                }
                $line = $line1+1;
            }
        }
    }

    $detalleRegistrador = $obj_col->buscar_colaborador_xnDoc(trim($detalleDespacho['ndoccreadopor_des']));
    $regisCargo = "";
    $regisFirma = "";
    if(!is_null($detalleRegistrador)){
        $regisCargo = trim($detalleRegistrador['cargo_col']);
    }


    $sheet->mergeCells('A'.$line.':AC'.$line);
    $sheet->setCellValue('A'.$line, "DATOS DEL RESPONSABLE DEL REGISTRO:");
    $sheet->getStyle('A'.$line)->applyFromArray($fndCeleste_center);
    $sheet->getStyle('A'.$line.':AC'.$line)->applyFromArray($borderAll);

    $sheet->mergeCells('A'.($line+1).':E'.($line+1));
    $sheet->setCellValue('A'.($line+1), "NOMBRE");
    $sheet->getStyle('A'.($line+1))->applyFromArray($fndNormalBold9);
    $sheet->getStyle('A'.($line+1).':E'.($line+1))->applyFromArray($borderAll);

    $sheet->mergeCells('F'.($line+1).':AC'.($line+1));
    $sheet->setCellValue('F'.($line+1), $detalleDespacho['creadopor_des']);
    $sheet->getStyle('F'.($line+1))->applyFromArray($fndNormalBold9);
    $sheet->getStyle('F'.($line+1).':AC'.($line+1))->applyFromArray($borderAll);

    $sheet->mergeCells('A'.($line+2).':E'.($line+2));
    $sheet->setCellValue('A'.($line+2), "CARGO");
    $sheet->getStyle('A'.($line+2))->applyFromArray($fndNormalBold9);
    $sheet->getStyle('A'.($line+2).':E'.($line+2))->applyFromArray($borderAll);

    $sheet->mergeCells('F'.($line+2).':AC'.($line+2));
    $sheet->setCellValue('F'.($line+2), $regisCargo);
    $sheet->getStyle('F'.($line+2))->applyFromArray($fndNormalBold9);
    $sheet->getStyle('F'.($line+2).':AC'.($line+2))->applyFromArray($borderAll);

    $sheet->mergeCells('A'.($line+3).':E'.($line+3));
    $sheet->setCellValue('A'.($line+3), "FECHA");
    $sheet->getStyle('A'.($line+3))->applyFromArray($fndNormalBold9);
    $sheet->getStyle('A'.($line+3).':E'.($line+3))->applyFromArray($borderAll);

    $sheet->mergeCells('F'.($line+3).':AC'.($line+3));
    $sheet->setCellValue('F'.($line+3), $obj_fn->fecha_ENG_ESP($detalleDespacho['fecha_des']));
    $sheet->getStyle('F'.($line+3))->applyFromArray($fndNormalBold9);
    $sheet->getStyle('F'.($line+3).':AC'.($line+3))->applyFromArray($borderAll);

    $sheet->mergeCells('A'.($line+4).':E'.($line+4));
    $sheet->setCellValue('A'.($line+4), "FIRMA");
    $sheet->getStyle('A'.($line+4))->applyFromArray($fndNormalBold9);
    $sheet->getStyle('A'.($line+4).':E'.($line+4))->applyFromArray($borderAll);

    $sheet->mergeCells('F'.($line+4).':AC'.($line+4));
    $sheet->setCellValue('F'.($line+4), $regisFirma);
    $sheet->getStyle('F'.($line+4))->applyFromArray($fndNormalBold9);
    $sheet->getStyle('F'.($line+4).':AC'.($line+4))->applyFromArray($borderAll);

    $sheet->getColumnDimension('A')->setWidth(3.5);
    $sheet->getColumnDimension('B')->setWidth(9);
    $sheet->getColumnDimension('C')->setWidth(2.8);
    $sheet->getColumnDimension('D')->setWidth(2.8);
    $sheet->getColumnDimension('E')->setWidth(2.8);
    $sheet->getColumnDimension('F')->setWidth(2.8);
    $sheet->getColumnDimension('G')->setWidth(2.8);
    $sheet->getColumnDimension('H')->setWidth(2.8);
    $sheet->getColumnDimension('I')->setWidth(2.8);
    $sheet->getColumnDimension('J')->setWidth(3.2);
    $sheet->getColumnDimension('K')->setWidth(3.2);
    $sheet->getColumnDimension('L')->setWidth(3.2);
    $sheet->getColumnDimension('M')->setWidth(3.2);
    $sheet->getColumnDimension('O')->setWidth(3.2);
    $sheet->getColumnDimension('P')->setWidth(3.4);
    $sheet->getColumnDimension('Q')->setWidth(3.4);
    $sheet->getColumnDimension('R')->setWidth(11);
    $sheet->getColumnDimension('S')->setWidth(25);
    $sheet->getColumnDimension('T')->setWidth(2.8);
    $sheet->getColumnDimension('U')->setWidth(2.8);
    $sheet->getColumnDimension('V')->setWidth(2.8);
    $sheet->getColumnDimension('W')->setWidth(4.7);
    $sheet->getColumnDimension('X')->setWidth(4.7);
    $sheet->getColumnDimension('Y')->setWidth(4.7);
    $sheet->getColumnDimension('Z')->setWidth(4.7);
    $sheet->getColumnDimension('AA')->setWidth(8.6);
    $sheet->getColumnDimension('AB')->setWidth(8.6);
    $sheet->getColumnDimension('AC')->setWidth(8.6);

    //Definimos el Zoom 92%
    $sheet->setTitle("REG. EQUIP. DE SEGURIDAD");
    $sheet->getSheetView()->setZoomScale(120);

    $nombreDelDocumento = "FRM-".$idDespacho."-".$fechaDespacho.".xlsx";
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