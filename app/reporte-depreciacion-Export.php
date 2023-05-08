<?php
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
    $IdAlmacen = (int)$_REQUEST['idAlmacen'];

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
            'size' => 10
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

    $titulo_1 = [
        'font' => [
            'bold' => true,
            'italic' => false,
            'underline' => false,
            'strikethrough' => false,
            'color' => ['argb' => '000000'],
            'name' => "calibri",
            'size' => 15
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
        65 => 'A', 66 => 'B', 67 => 'C', 68 => 'D', 69 => 'E', 70 => 'F', 71 => 'G', 72 => 'H',73 => 'I', 74 => 'J', 75 => 'K', 76 => 'L', 77 => 'M',
        78 => 'N', 79 => 'O', 80 => 'P',81 => 'Q', 82 => 'R', 83 => 'S', 84 => 'T', 85 => 'U', 86 => 'V', 87 => 'W', 88 => 'X',89 => 'Y', 90 => 'Z',
        91  => 'AA', 92  => 'AB', 93  => 'AC', 94  => 'AD', 95  => 'AE', 96 => 'AF',  97  => 'AG', 98  => 'AH', 99  => 'AI', 100 => 'AJ', 101 => 'AK', 102 => 'AL', 103 => 'AM',
        104 => 'AN', 105 => 'AO', 106 => 'AP', 107 => 'AQ', 108 => 'AR', 109 => 'AS', 110 => 'AT', 111 => 'AU', 112 => 'AV', 113 => 'AW', 114 => 'AX', 115 => 'AY', 116 => 'AZ',
        117 => 'BA', 118 => 'BB', 119 => 'BC', 120 => 'BD', 121 => 'BE', 122 => 'BF', 123 => 'BG', 124 => 'BH', 125 => 'BI', 126 => 'BJ', 127 => 'BK', 128 => 'BL', 129 => 'BM',
        130 => 'BN', 131 => 'BO', 132 => 'BP', 133 => 'BQ', 134 => 'BR', 135 => 'BS', 136 => 'BT', 137 => 'BU', 138 => 'BV', 139 => 'BW', 140 => 'BX', 141 => 'BY', 142 => 'BZ',
        143 => 'CA', 144 => 'CB', 145 => 'CC', 146 => 'CD', 147 => 'CE', 148 => 'CF', 149 => 'CG', 150 => 'CH', 151 => 'CI', 152 => 'CJ', 153 => 'CK', 154 => 'CL', 155 => 'CM',
        156 => 'CN', 157 => 'CO', 158 => 'CP', 159 => 'CQ', 160 => 'CR', 161 => 'CS', 162 => 'CT', 163 => 'CU', 164 => 'CV', 165 => 'CW', 166 => 'CX', 167 => 'CY', 168 => 'CZ',
        169 => 'DA', 170 => 'DB', 171 => 'DC', 172 => 'DD', 173 => 'DE', 174 => 'DF', 175 => 'DG', 176 => 'DH', 177 => 'DI', 178 => 'DJ', 179 => 'DK', 180 => 'DL', 181 => 'DM',
        182 => 'DN', 183 => 'DO', 184 => 'DP', 185 => 'DQ', 186 => 'DR', 187 => 'DS', 188 => 'DT', 189 => 'DU', 190 => 'DV', 191 => 'DW', 192 => 'DX', 193 => 'DY', 194 => 'DZ',
        195 => 'EA', 196 => 'EB', 197 => 'EC', 198 => 'ED', 199 => 'EE', 200 => 'EF', 201 => 'EG', 202 => 'EH', 203 => 'EI', 204 => 'EJ', 205 => 'EK', 206 => 'EL', 207 => 'EM',
        208 => 'EN', 209 => 'EO', 210 => 'EP', 211 => 'EQ', 212 => 'ER', 213 => 'ES', 214 => 'ET', 215 => 'EU', 216 => 'EV', 217 => 'EW', 218 => 'EX', 219 => 'EY', 220 => 'EZ',
        221 => 'FA', 222 => 'FB', 223 => 'FC', 224 => 'FD', 225 => 'FE', 226 => 'FF', 227 => 'FG', 228 => 'FH', 229 => 'RI', 230 => 'FJ', 231 => 'FK', 232 => 'FL', 233 => 'FM',
        234 => 'FN', 235 => 'FO', 236 => 'FP', 237 => 'FQ', 238 => 'FR', 239 => 'FS', 240 => 'FT', 241 => 'FU', 242 => 'RV', 243 => 'FW', 244 => 'FX', 245 => 'FY', 246 => 'FZ'
    );


    $titulos = array(
        0 => 'ITEM',
        1 => 'CODIGO',
        2 => 'DESCRIPCIÓN ACTIVO',
        3 => 'NRO. PARTE/SERIE',
        4 => 'STOCK ACTUAL',
        5 => 'STATUS',
        6 => 'COSTO DEL ACTIVO',
        7 => 'FRECUENCIA DEPRECIACION ACTIVO',
        8 => 'VALOR DEPRECIACION MENSUAL',
    );

    $sheet->mergeCells('A1:I1');
    $sheet->setCellValue('A1', "REPORTE DEL VALOR DEL ACTIVO");
    $sheet->getStyle('A1')->applyFromArray($titulo_1);


    for ($colu = 0; $colu < sizeof($titulos); $colu++) {
        $sheet->setCellValue($letras[65+$colu] . '2', $titulos[$colu]);
        $sheet->getStyle($letras[65+$colu] . '2')->applyFromArray($sub_titulo);
        $sheet->getStyle($letras[65+$colu] . '2')->getAlignment()->setWrapText(true);
    }

    $sheet->getRowDimension(1)->setRowHeight(32);
    $sheet->getRowDimension(2)->setRowHeight(39);

    $obj_inv = new InventarioModel();
    $lstActivos = $obj_inv->lista_Depreciacion_Activo_xIdAlmacen($IdAlmacen);

    $line= 3;

    if (!is_null($lstActivos)) {
        $item = 1;
        $arraytamanio = array();
        foreach ($lstActivos as $activo) {
            $sheet->SetCellValue('A' . $line, $item);

            if (!empty(trim($activo['cod_inv'])) && !is_null(trim($activo['cod_inv']))) {
                $sheet->SetCellValue('B' . $line, $activo['cod_inv']);
            }
            if (!empty(trim($activo['des_inv'])) && !is_null(trim($activo['des_inv']))) {
                $sheet->SetCellValue('C' . $line, $activo['des_inv']);
            }
            if (!empty(trim($activo['nroparte_inv'])) && !is_null(trim($activo['nroparte_inv']))) {
                $sheet->SetCellValue('D' . $line, $activo['nroparte_inv']);
            }

            $sheet->SetCellValue('E' . $line, $activo['cant_inv']);

            $txtEstatus = "SIN DATOS";
            if (!is_null($activo['fechadepre_inv']) && (float)$activo['costo_act_inv'] > 0 && (int)$activo['frec_depre_act_inv'] > 0 && (int)$activo['val_depre_mensual_inv'] > 0 ) {
                $txtEstatus = "En Ejecución";
            }
            $sheet->SetCellValue('F' . $line, $txtEstatus);

            if ((float)$activo['costo_act_inv'] > 0) {
                $sheet->SetCellValue('G' . $line, $activo['costo_act_inv']);
            }
            if ((int)$activo['frec_depre_act_inv']> 0) {
                $sheet->SetCellValue('H' . $line, $activo['frec_depre_act_inv']);
            }
            if ((float)$activo['val_depre_mensual_inv'] > 0) {
                $sheet->SetCellValue('I' . $line, $activo['val_depre_mensual_inv']);
            }

            if(!is_null($activo['fechadepre_inv']) && trim($activo['fechadepre_inv']) != "0000-00-00" && (int)$activo['costo_act_inv'] > 0 && (float)$activo['frec_depre_act_inv']> 0 && (int)$activo['val_depre_mensual_inv'] > 0) {
                $col = 74;
                $valDecremento = (float)$activo['costo_act_inv'];
                array_push($arraytamanio,(int)$activo['frec_depre_act_inv']);
                for ($i = 0; $i <= (int)$activo['frec_depre_act_inv']; $i++) {
                    $textValue = "";
                    if ($i == 0) {
                        $textValue = $obj_fn->fechaHora_ENG_ESP($activo['fechadepre_inv']) . " " . $activo['costo_act_inv'];
                    } else {
                        $fechaProxima = $obj_fn->fechaHora_ENG_ESP($obj_fn->sumar_meses_fecha($activo['fechadepre_inv'], $i));
                        $valorCalculado = $valDecremento - (float)$activo['val_depre_mensual_inv'];
                        $valDecremento = $valorCalculado;
                        $textValue = $fechaProxima . "    " . $valorCalculado;
                    }
                    $sheet->SetCellValue($letras[$col + $i] . $line, $textValue);
                    $sheet->getStyle($letras[$col + $i] . $line)->applyFromArray($celda_center);
                    $sheet->getStyle($letras[$col + $i] . $line)->getAlignment()->setWrapText(true);
                    $sheet->getColumnDimension($letras[$col + $i])->setWidth(11);
                }
            }

            $sheet->getRowDimension($line)->setRowHeight(32);
            $sheet->getStyle("A" . $line . ":I" . $line)->applyFromArray($celda_center);
            $sheet->getStyle("A" . $line . ":I" . $line)->getAlignment()->setWrapText(true);
            $line++;
            $item++;
        }

        //Obtenemos maximo tamaño del arreglo
        $tamanioMax = max($arraytamanio);
        if((int)$tamanioMax > 0) {
            $sheet->mergeCells('J2:' . $letras[74 + $tamanioMax] . '2');
            $sheet->setCellValue('J2', "PROYECCION DE DEPRECIACIÓN DEL ACTIVO");
            $sheet->getStyle('J2')->applyFromArray($sub_titulo);
        }
    }

    $sheet->getColumnDimension('A')->setWidth(6);
    $sheet->getColumnDimension('B')->setWidth(15);
    $sheet->getColumnDimension('C')->setWidth(50);
    $sheet->getColumnDimension('D')->setWidth(17);
    $sheet->getColumnDimension('E')->setWidth(17);
    $sheet->getColumnDimension('F')->setWidth(17);
    $sheet->getColumnDimension('G')->setWidth(11);
    $sheet->getColumnDimension('H')->setWidth(16);
    $sheet->getColumnDimension('I')->setWidth(13);

    //Definimos el Zoom 92%
    $sheet->setTitle("Depreciacion");
    $sheet->getSheetView()->setZoomScale(80);

    //inmovilizamos la culumna
    $sheet->freezePane('E3');

    $nombreDelDocumento = "DEPRECIA-" . date("d-m-Y") .".xlsx";
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