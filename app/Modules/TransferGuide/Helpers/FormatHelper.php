<?php
require_once __DIR__ . '/ValidateHelper.php';
require_once __DIR__ . '/../../../Models/TransferGuide.php';

class FormatHelper{
    public function __construct() {
    }

    public static function parseStoreTransitMovementGuide($data){
        $transports = [];
        if(isset($data['transports'])){
            if(count($data['transports']) > 0){

                if($data['transport_modality'] == 1){
                    $transports = [
                        'en_InformacionTransporteGRR' => [
                            'at_Modalidad' => "0".$data['transport_modality'],
                            'at_FechaInicio' => $data['transports'][0]['start_date'],
                            'ent_TransportePublicoGRR' => [
                                'at_TipoDocumentoIdentidad' => $data['transports'][0]['document_type_code'],
                                'at_NumeroDocumentoIdentidad' => $data['transports'][0]['document'],
                                'at_RazonSocial' => $data['transports'][0]['company_name']
                            ]
                        ]
                    ];

                    if($data['transports'][0]['mtc_number']){
                        $transports['en_InformacionTransporteGRR']['ent_TransportePublicoGRR']['at_NumeroMTC'] = strtoupper($data['transports'][0]['mtc_number']);
                    }
                }
                else{
                    $transports = [
                        'en_InformacionTransporteGRR' => [
                            'at_Modalidad' => "0".$data['transport_modality'],
                            'at_FechaInicio' => $data['transports'][0]['start_date'],
                            'ent_TransportePrivadoGRR' => [
                                'l_ConductorGRR' => [],
                                'l_VehiculoGRR' => []
                            ]
                        ]
                    ];

                    foreach($data['transports'] as $transport){
                        array_push($transports['en_InformacionTransporteGRR']['ent_TransportePrivadoGRR']['l_ConductorGRR'], [
                            'en_ConductorGRR' => [
                                'at_TipoDocumentoIdentidad' => $transport['document_type_code'],
                                'at_NumeroDocumentoIdentidad' => $transport['document'],
                                'at_Licencia' => strtoupper($transport['license']),
                                'at_Nombres' => $transport['name'],
                                'at_Apellidos' => $transport['last_name']
                            ]
                        ]);
                    }

                    foreach($data['vehicles'] as $vehicle){
                        array_push($transports['en_InformacionTransporteGRR']['ent_TransportePrivadoGRR']['l_VehiculoGRR'], [
                            'en_VehiculoGRR' => [
                                'aa_NumeroPlaca' => [
                                    'string' => strtoupper($vehicle['plate'])
                                ]
                            ]
                        ]);
                    }
                }
            }
        }

        $bienes = [];

        foreach($data['details'] as $key => $item){
            $newBien = [
                'at_Cantidad' => $item['quantity'],
                'at_UnidadMedida' => $item['unit_measure'] ?? 'SET',
                'at_Descripcion' => $item['name'],
                'at_Codigo' => $item['code']
            ];
           
            if($item['additional_description']){
                if(trim($item['additional_description']) != ''){
                    $newBien['aa_DescripcionAdicional'] = [
                        'string' => $item['additional_description']
                    ];
                }
            }
            array_push($bienes, [
                'en_BienesGRR' => $newBien
            ]);
        }

        $storeIni = [
            'at_Ubigeo' => $data['start_store']['district']['code'],
            'at_DireccionCompleta' => $data['start_store']['address'] . ' - ' . $data['start_store']['district']['name'] . ' - ' . $data['start_store']['district']['province'] . ' - ' . $data['start_store']['district']['department']
        ];

        $storeEnd = [
            'at_Ubigeo' => $data['end_store']['district']['code'],
            'at_DireccionCompleta' => $data['end_store']['address'] . ' - ' . $data['end_store']['district']['name'] . ' - ' . $data['end_store']['district']['province'] . ' - ' . $data['end_store']['district']['department']
        ];

        if($data['motive_code'] == TransferGuide::BETWEENCOMPANY){
            $storeIni['at_NumeroDocumentoIdentidad'] = $data['start_store']['company']['document'];
            if($data['start_store']['establishment_code'] && $data['start_store']['establishment_code'] != ''){
                $storeIni['at_CodigoEstablecimiento'] = $data['start_store']['establishment_code'];
            }

            $storeEnd['at_NumeroDocumentoIdentidad'] = $data['end_store']['company']['document'];
            if($data['end_store']['establishment_code'] && $data['end_store']['establishment_code'] != '' && !$data['alternative_address']){
                $storeEnd['at_CodigoEstablecimiento'] = $data['end_store']['establishment_code'];
            }
        }

        $transportInformation = [
            'at_CodigoMotivo' => $data['motive_code'],
            'ent_InformacionPesoBrutoGRR' => [
                'at_Peso' => $data['total_witght'],
                'at_UnidadMedida' => 'KGM',
                'at_Cantidad' => $data['total_quantity'],
            ],
            'l_InformacionTransporteGRR' => $transports,
            'ent_PuntoPartidaGRR' => $storeIni,
            'ent_PuntoLlegadaGRR' => $storeEnd,
            'l_BienesGRR' => $bienes
        ];

        $provider = null;
        $buyer = null;

        if(isset($data['indicator_service'])){
            if($data['indicator_service']){
                $transportInformation['aa_IndicadorServicio'] = [
                    'string' => $data['indicator_service']
                ];
            }
        }

        if($data['motive_code'] == TransferGuide::OTHER){
            $transportInformation['at_DescripcionMotivo'] = $data['motive_description'];

            if(isset($data['provider'])){
                $provider = [
                    'at_TipoDocumentoIdentidad' => $data['provider']['document_type_code'],
                    'at_NumeroDocumentoIdentidad' => $data['provider']['document'],
                    'at_RazonSocial' => $data['provider']['name']
                ];
            }

            if(isset($data['buyer'])){
                $buyer = [
                    'at_TipoDocumentoIdentidad' => $data['buyer']['document_type_code'],
                    'at_NumeroDocumentoIdentidad' => $data['buyer']['document'],
                    'at_RazonSocial' => $data['buyer']['name']
                ];
            }
        }

        $documentTypeCode =  $data['end_store']['company']['document_type_code'];
        $documentNumber = $data['end_store']['company']['document'];
        $companyName = $data['end_store']['company']['name'];

        if($data['flag_new_company'] == true || $data['flag_new_company'] == 'true' || $data['flag_new_company'] == 1 || $data['flag_new_company'] == '1'){
            $documentTypeCode =  $data['new_document_type_code'];
            $documentNumber = $data['new_document'];
            $companyName = $data['new_company_name'];
        }

        $result = [
            'ent_GuiaRemisionRemitente' => [
                'ent_RemitenteGRR' => [
                    'at_NumeroDocumentoIdentidad' => $data['start_store']['company']['document'],
                    'at_RazonSocial' => $data['start_store']['company']['name'],
                    'at_NombreComercial' => $data['start_store']['company']['commercial_name'],
                    'at_Telefono' => $data['start_store']['company']['phone'],
                    'at_CorreoContacto' => $data['start_store']['company']['email'],
                    'at_SitioWeb' => $data['start_store']['company']['page_web'],
                    'ent_DireccionFiscal' => [
                        'at_Ubigeo' => $data['start_store']['company']['ubigeo_code'],
                        'at_DireccionDetallada' => $data['start_store']['company']['address'],
                        'at_Provincia' => $data['start_store']['company']['province_name'],
                        'at_Departamento' => $data['start_store']['company']['department_name'],
                        'at_Distrito' => $data['start_store']['company']['district_name'],
                        'at_CodigoPais' => 'PE'
                    ]
                ],
                'ent_DestinatarioGRR' => [
                    'at_TipoDocumentoIdentidad' => $documentTypeCode,
                    'at_NumeroDocumentoIdentidad' => $documentNumber,
                    'at_RazonSocial' => $companyName,
                    'ent_Correo' => [
                        'at_CorreoPrincipal' => $data['end_store']['email_principal']
                    ]
                ],
                'ent_InformacionAdicionalGRR' => [
                    'at_LogoRepresentacionImpresa' => 'SKA'
                ]
            ]
        ];

        if(isset($data['end_store']['email_secondary'])){
            if($data['end_store']['email_secondary']){
                $result['ent_GuiaRemisionRemitente']['ent_DestinatarioGRR']['ent_Correo']['aa_CorreoSecundario'] = [
                    'string' => $data['end_store']['email_secondary']
                ];
            }
        }

        if($provider){
            $result['ent_GuiaRemisionRemitente']['ent_ProveedorGRR'] = $provider;
        }

        if($buyer){
            $result['ent_GuiaRemisionRemitente']['ent_CompradorGRR'] = $buyer;
        }
        
        $result['ent_GuiaRemisionRemitente']['ent_DatosGeneralesGRR'] = [
            'at_FechaEmision' => $data['date_issue'],
            'at_Serie' => $data['serie'],
            'at_Numero' => $data['number'],
            'at_Observacion' => $data['observations'],
            'at_HoraEmision' => $data['time_issue'],
            'ent_InformacionTrasladoGRR' => $transportInformation
        ];

        $result['ent_GuiaRemisionRemitente']['at_ControlOtorgamiento'] = 1;

        return $result;
    }

    public static function parseResponseQueryResponseSUNAT($data){
        $documents = [];
        if(isset($data['l_ResultadoRespuestaComprobante'])){
            if(isset($data['l_ResultadoRespuestaComprobante']['en_ResultadoRespuestaComprobante'])){
                if(is_array($data['l_ResultadoRespuestaComprobante']['en_ResultadoRespuestaComprobante'])){
                    foreach($data['l_ResultadoRespuestaComprobante']['en_ResultadoRespuestaComprobante'] as $response){
                        if(isset($response['ent_RespuestaComprobante'])){
                            $restFormat = [
                                'serie' => $response['at_Serie'],
                                'number' => $response['at_Numero'],
                                'type_response' => $response['ent_RespuestaComprobante']['at_TipoRespuesta'],
                                'code_response' => $response['ent_RespuestaComprobante']['at_CodigoRespuesta'],
                                'description' => $response['ent_RespuestaComprobante']['at_Descripcion'],
                                'date' => $response['ent_RespuestaComprobante']['at_FechaRespuesta'],
                                'messages' => []
                            ];
    
                            if(isset($response['ent_RespuestaComprobante']['at_Mensaje'])){
                                if(isset($response['ent_RespuestaComprobante']['at_Mensaje']['string'])){
                                    if(is_array($response['ent_RespuestaComprobante']['at_Mensaje']['string'])){
                                        $restFormat['messages'] = $response['ent_RespuestaComprobante']['at_Mensaje']['string'];
                                    }
                                    else{
                                        $restFormat['messages'] = [$response['ent_RespuestaComprobante']['at_Mensaje']['string']];
                                    }
                                }
                            }
    
                            array_push($documents, $restFormat);
                        }
                    }
                }
                else{
                    $response = $data['l_ResultadoRespuestaComprobante']['en_ResultadoRespuestaComprobante'];
                    $restFormat = [
                        'serie' => $response['at_Serie'],
                        'number' => $response['at_Numero'],
                        'type_response' => $response['ent_RespuestaComprobante']['at_TipoRespuesta'],
                        'code_response' => $response['ent_RespuestaComprobante']['at_CodigoRespuesta'],
                        'description' => $response['ent_RespuestaComprobante']['at_Descripcion'],
                        'date' => $response['ent_RespuestaComprobante']['at_FechaRespuesta'],
                        'messages' => []
                    ];

                    if(isset($response['ent_RespuestaComprobante']['at_Mensaje'])){
                        if(isset($response['ent_RespuestaComprobante']['at_Mensaje']['string'])){
                            if(is_array($response['ent_RespuestaComprobante']['at_Mensaje']['string'])){
                                $restFormat['messages'] = $response['ent_RespuestaComprobante']['at_Mensaje']['string'];
                            }
                            else{
                                $restFormat['messages'] = [$response['ent_RespuestaComprobante']['at_Mensaje']['string']];
                            }
                        }
                    }

                    array_push($documents, $restFormat);
                }
            }
        }

        return $documents;
    }
    
    public static function parseDownloadPDF($data){
        $result = [
            'ent_ConsultarRI' => [
                'at_NumeroDocumentoIdentidad' => $data['start_store']['company']['document'],
                'ent_Comprobante' => [
                    'at_Serie' => $data['serie'],
                    'at_Numero' => $data['number']
                ]
            ]
        ];

        return $result;
    }

    public static function parseDownloadXML($data){
        $result = [
            'ent_ConsultarXML' => [
                'at_NumeroDocumentoIdentidad' => $data['start_store']['company']['document'],
                'ent_ComprobanteConsultarXML' => [
                    'at_Serie' => $data['serie'],
                    'at_Numero' => $data['number'],
                    'at_NumeroRespuesta' => 1
                ]
            ]
        ];

        return $result;
    }

    public static function parseQueryOneGRR($data){
        $result = [
            'ent_ConsultarComprobanteIndividual' => [
                'at_NumeroDocumentoIdentidad' => $data['start_store']['company']['document'],
                'at_Serie' => $data['serie'],
                'at_Numero' => $data['number']
            ]
        ];

        return $result;
    }

    public static function parseResponseTci($guide, $response){
        $document = null;
        $answer = null;
        if(isset($response['ent_InformacionComprobante'])){
            if(isset($response['ent_InformacionComprobante']['l_respuestas'])){
                if(isset($response['ent_InformacionComprobante']['l_respuestas']['en_Respuestas'])){
                    $answer = $response['ent_InformacionComprobante']['l_respuestas'];
                    $document = [
                        'serie' => $guide['serie'],
                        'number' => $guide['number'],
                        'type_response' => $answer['en_Respuestas']['at_NroRespuesta'],
                        'code_response' => $answer['en_Respuestas']['at_CodigoRespuesta'],
                        'description' => $answer['en_Respuestas']['at_Descripcion'],
                        'date' => $answer['en_Respuestas']['at_FechaSunat'],
                        'messages' => [$answer['en_Respuestas']['at_Descripcion']]
                    ];
                }
            }
        }
        
        return $document;
    }

    public static function parseResumeReversion($data){
        $result = [
            'ent_ResumenReversion' => [
                'ent_Emisor' => [
                    'at_NumeroDocumentoIdentidad' => $data['company']['document'],
                    'at_RazonSocial' => $data['company']['name']
                ],
                'ent_DatoResumenReversion' => [
                    'ent_CabeceraResumenReversion' => [
                        'at_FechaComprobante' => $data['date_issue'],
                        'at_FechaGeneracion' => $data['date_generated'],
                        'at_IdentificadorUnico' => $data['number_reversion'],
                        'l_ComprobantesRevertidos' => [
                            'en_ComprobantesRevertidos' => [
                                'at_TipoComprobante' => '09',
                                'at_Serie' => $data['serie'],
                                'at_Numero' => $data['number'],
                                'at_MotivoReversion' => $data['motive']
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return $result;
    }
}