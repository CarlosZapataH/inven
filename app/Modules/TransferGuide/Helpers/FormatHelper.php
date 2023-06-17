<?php
require_once __DIR__ . '/ValidateHelper.php';

class FormatHelper{
    public function __construct() {
    }

    public static function parseStoreTransitMovementGuide($data){


        $transports = [];
        if(ValidateHelper::validateProperty($data, ['transports'])){
            if(count($data['transports']) > 0){

                if($data['transport_modality'] == 1){
                    $transports = [
                        'en_InformacionTransporteGRR' => [
                            'at_Modalidad' => "0".$data['transport_modality'],
                            'at_FechaInicio' => $data['transports'][0]['start_date'],
                            'ent_TransportePublicoGRR' => [
                                'at_TipoDocumentoIdentidad' => $data['transports'][0]['document_type'],
                                'at_NumeroDocumentoIdentidad' => $data['transports'][0]['document'],
                                'at_RazonSocial' => $data['transports'][0]['company_name'],
                                'at_NumeroMTC' => $data['transports'][0]['mtc_number']
                            ]
                        ]
                    ];
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
                                'at_TipoDocumentoIdentidad' => $transport['document_type'],
                                'at_NumeroDocumentoIdentidad' => $transport['document'],
                                'at_Licencia' => $transport['license'],
                                'at_Nombres' => $transport['name'],
                                'at_Apellidos' => $transport['last_name']
                            ]
                        ]);
                    }

                    foreach($data['vehicles'] as $vehicle){
                        array_push($transports['en_InformacionTransporteGRR']['ent_TransportePrivadoGRR']['l_VehiculoGRR'], [
                            'en_VehiculoGRR' => [
                                'aa_NumeroPlaca' => [
                                    'string' => $vehicle['plate']
                                ]
                            ]
                        ]);
                    }
                }
            }
        }

        $bienes = [];

        foreach($data['detail'] as $key => $item){
            $newBien = [
                'at_Cantidad' => $item['cant_mde'],
                'at_UnidadMedida' => $item['um_sunat_code'] ?? 'SET',
                'at_Descripcion' => $item['des_mde'],
                'at_Codigo' => $item['cod_inv']
            ];
            if($item['additional_description']){
                $newBien['additional_description'];
            }
            array_push($bienes, [
                'en_BienesGRR' => $newBien
            ]);
        }

        $result = [
            'ent_GuiaRemisionRemitente' => [
                'ent_RemitenteGRR' => [
                    'at_NumeroDocumentoIdentidad' => $data['start_store']['document'],
                    'at_RazonSocial' => $data['start_store']['name'],
                    'at_NombreComercial' => $data['start_store']['commercial_name']
                ],
                'ent_DestinatarioGRR' => [
                    'at_TipoDocumentoIdentidad' => $data['end_store']['document_type'],
                    'at_NumeroDocumentoIdentidad' => $data['end_store']['document'],
                    'at_RazonSocial' => $data['end_store']['name'],
                    'ent_Correo' => [
                        'at_CorreoPrincipal' => $data['end_store']['email_principal'],
                        'aa_CorreoSecundario' => [
                            'string' => $data['end_store']['email_secondary']
                        ]
                    ]
                ],
                'ent_DatosGeneralesGRR' => [
                    'at_FechaEmision' => $data['date_issue'],
                    'at_Serie' => $data['serie'],
                    'at_Numero' => $data['number'],
                    'at_Observacion' => $data[''],
                    'at_HoraEmision' => $data['time_issue'],
                    'ent_InformacionTrasladoGRR' => [
                        'at_CodigoMotivo' => '0'.$data['motive_code'],
                        'ent_InformacionPesoBrutoGRR' => [
                            'at_Peso' => $data['total_witght'],
                            'at_UnidadMedida' => 'KGM',
                            'at_Cantidad' => $data['total_quantity'],
                        ],
                        'l_InformacionTransporteGRR' => $transports,
                        'ent_PuntoPartidaGRR' => [
                            'at_Ubigeo' => $data['start_store']['ubigeo'],
                            'at_DireccionCompleta' => $data['start_store']['address'],
                            'at_CodigoEstablecimiento' => '0000',
                            'at_NumeroDocumentoIdentidad' => $data['start_store']['document']
                        ],
                        'ent_PuntoLlegadaGRR' => [
                            'at_Ubigeo' => $data['end_store']['ubigeo'],
                            'at_DireccionCompleta' => $data['end_store']['address'],
                            'at_CodigoEstablecimiento' => '0003',
                            'at_NumeroDocumentoIdentidad' => $data['end_store']['document']
                        ],
                        'l_BienesGRR' => $bienes
                    ]
                ],
                'at_ControlOtorgamiento' => 1
            ]
        ];

        return $result;
    }

    public static function parseResponseQueryResponseSUNAT($data){
        $documents = [];
        if(isset($data['l_ResultadoRespuestaComprobante'])){
            if(isset($data['l_ResultadoRespuestaComprobante']['en_ResultadoRespuestaComprobante'])){
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
        }

        return $documents;
    }

    public static function parseStoreDevolutionGuide($movement, $data = []){
        $remitenteDocumento = ValidateHelper::validateProperty($data, ['almacen_partida.document'])
            ?$data['almacen_partida']['document']
            :$movement['almacen_partida']['company']['document'];

        $remitenteRazonSocial = ValidateHelper::validateProperty($data, ['almacen_partida.name'])
            ?$data['almacen_partida']['name']
            :$movement['almacen_partida']['company']['name'];

        $remitenteNombreComercial = ValidateHelper::validateProperty($data, ['almacen_partida.commercial_name'])
            ?$data['almacen_partida']['commercial_name']
            :$movement['almacen_partida']['company']['commercial_name'];

        $destinatarioTipoDocumento = ValidateHelper::validateProperty($data, ['almacen_destino.document_type_code'])
            ?$data['almacen_destino']['document_type_code']
            :$movement['almacen_destino']['company']['document_type_code'];

        $destinatarioDocumento = ValidateHelper::validateProperty($data, ['almacen_destino.document'])
            ?$data['almacen_destino']['document']
            :$movement['almacen_destino']['company']['document'];
        $destinatarioRazonSocial = ValidateHelper::validateProperty($data, ['almacen_destino.name'])
            ?$data['almacen_destino']['name']
            :$movement['almacen_destino']['company']['name'];
    
        $destinatarioCorreoPrincipal = ValidateHelper::validateProperty($data, ['almacen_destino.email_principal'])
            ?$data['almacen_destino']['email_principal']
            :$movement['almacen_destino']['email_principal'];

        $destinatarioCorreoSecundario =  ValidateHelper::validateProperty($data, ['almacen_destino.email_secondary'])
            ?$data['almacen_destino']['email_secondary']
            :$movement['almacen_destino']['email_secondary'];

        $generalFechaEmision = ValidateHelper::validateProperty($data, ['fecha_emision'])
            ?$data['fecha_emision']
            :$movement['fecha_emision'];

        $generalSerie = ValidateHelper::validateProperty($data, ['serie'])
            ?$data['serie']
            :$movement['serie'];

        $generalNumero = ValidateHelper::validateProperty($data, ['numero'])
            ?$data['numero']
            :$movement['numero'];

        $generalObservacion = ValidateHelper::validateProperty($data, ['observacion'])
            ?$data['observacion']
            :$movement['observacion'];
            
        $generalHoraEmision = ValidateHelper::validateProperty($data, ['hora_emision'])
            ?$data['hora_emision']
            :$movement['hora_emision'];
        
        $trasladoPeso = ValidateHelper::validateProperty($data, ['peso'])
            ?$data['peso']
            :$movement['peso'];
            
        $trasladoCantidad = ValidateHelper::validateProperty($data, ['cantidad'])
            ?$data['cantidad']
            :$movement['cantidad']; 

        $transporteModalidad = ValidateHelper::validateProperty($data, ['modalidad_transporte'])
            ?$data['modalidad_transporte']
            :$movement['modalidad_transporte'];

        $transports = [];
        if(ValidateHelper::validateProperty($data, ['transports'])){
            if(is_array($data['transports'])){
                if(count($data['transports']) > 0){
                    $transporteFechaInicio = $data['transports'][0]['start_date'];

                    if($transporteModalidad == 1){
                        $transports = [
                            'en_InformacionTransporteGRR' => [
                                'at_Modalidad' => "0".$transporteModalidad,
                                'at_FechaInicio' => $transporteFechaInicio,
                                'ent_TransportePublicoGRR' => [
                                    'at_TipoDocumentoIdentidad' => $data['transports'][0]['document_type'],
                                    'at_NumeroDocumentoIdentidad' => $data['transports'][0]['document'],
                                    'at_RazonSocial' => $data['transports'][0]['company_name'],
                                    'at_NumeroMTC' => $data['transports'][0]['mtc_number']
                                ]
                            ]
                        ];
                    }
                    else{
                        $transports = [
                            'en_InformacionTransporteGRR' => [
                                'at_Modalidad' => "0".$transporteModalidad,
                                'at_FechaInicio' => $transporteFechaInicio,
                                'ent_TransportePrivadoGRR' => [
                                    'l_ConductorGRR' => [],
                                    'l_VehiculoGRR' => []
                                ]
                            ]
                        ];

                        foreach($data['transports'] as $transport){
                            array_push($transports['en_InformacionTransporteGRR']['ent_TransportePrivadoGRR']['l_ConductorGRR'], [
                                'en_ConductorGRR' => [
                                    'at_TipoDocumentoIdentidad' => $transport['document_type'],
                                    'at_NumeroDocumentoIdentidad' => $transport['document'],
                                    'at_Licencia' => $transport['license'],
                                    'at_Nombres' => $transport['name'],
                                    'at_Apellidos' => $transport['last_name']
                                ]
                            ]);
                        }

                        foreach($data['vehicles'] as $vehicle){
                            array_push($transports['en_InformacionTransporteGRR']['ent_TransportePrivadoGRR']['l_VehiculoGRR'], [
                                'en_VehiculoGRR' => [
                                    'aa_NumeroPlaca' => [
                                        'string' => $vehicle['plate']
                                    ]
                                ]
                            ]);
                        }
                    }
                }
            }
        }
        else{
            if(is_array($movement['transports'])){
                if(count($movement['transports']) > 0){
                    if($transporteModalidad == 1){
                        $transports = [
                            'en_InformacionTransporteGRR' => [
                                'at_Modalidad' => "0".$transporteModalidad,
                                'at_FechaInicio' => $movement['transports'][0]['start_date'],
                                'ent_TransportePublicoGRR' => [
                                    'at_TipoDocumentoIdentidad' => $movement['transports'][0]['document_type'],
                                    'at_NumeroDocumentoIdentidad' => $movement['transports'][0]['document'],
                                    'at_RazonSocial' => $movement['transports'][0]['company_name'],
                                    'at_NumeroMTC' => $movement['transports'][0]['mtc_number']
                                ]
                            ]
                        ];
                    }
                    else{
                        $transports = [
                            'en_InformacionTransporteGRR' => [
                                'at_Modalidad' => "0".$transporteModalidad,
                                'at_FechaInicio' => $movement['transports'][0]['start_date'],
                                'ent_TransportePrivadoGRR' => [
                                    'l_ConductorGRR' => [],
                                    'l_VehiculoGRR' => []
                                ]
                            ]
                        ];

                        foreach($movement['transports'] as $transport){
                            array_push($transports['en_InformacionTransporteGRR']['ent_TransportePrivadoGRR']['l_ConductorGRR'], [
                                'en_ConductorGRR' => [
                                    'at_TipoDocumentoIdentidad' => $transport['document_type'],
                                    'at_NumeroDocumentoIdentidad' => $transport['document'],
                                    'at_Licencia' => $transport['license'],
                                    'at_Nombres' => $transport['name'],
                                    'at_Apellidos' => $transport['last_name']
                                ]
                            ]);
                        }

                        foreach($movement['vehicles'] as $vehicle){
                            array_push($transports['en_InformacionTransporteGRR']['ent_TransportePrivadoGRR']['l_VehiculoGRR'], [
                                'en_VehiculoGRR' => [
                                    'aa_NumeroPlaca' => [
                                        'string' => $vehicle['plate']
                                    ]
                                ]
                            ]);
                        }
                    }
                }
            }
        }

        

        $puntoPartidaUbigeo = ValidateHelper::validateProperty($data, ['almacen_partida.ubigeo'])
            ?$data['almacen_partida']['ubigeo']
            :$movement['almacen_partida']['district']['code'];
            
        $puntoPartidaDireccion = ValidateHelper::validateProperty($data, ['almacen_partida.address'])
            ?$data['almacen_partida']['address']
            :$movement['almacen_partida']['direccion_alm'];

        $puntoPartidaDocumento = ValidateHelper::validateProperty($data, ['almacen_partida.document'])
            ?$data['almacen_partida']['document']
            :$movement['almacen_partida']['company']['document'];
            
        $puntoLlegadaUbigeo = ValidateHelper::validateProperty($data, ['almacen_destino.ubigeo'])
            ?$data['almacen_destino']['ubigeo']
            :$movement['almacen_destino']['district']['code'];

        $puntoLlegadaDireccion = ValidateHelper::validateProperty($data, ['almacen_destino.address'])
            ?$data['almacen_destino']['address']
            :$movement['almacen_destino']['direccion_alm'];

        $puntoLlegadaDocumento = ValidateHelper::validateProperty($data, ['almacen_destino.document'])
            ?$data['almacen_destino']['document']
            :$movement['almacen_destino']['company']['document'];

        $bienes = [];

        foreach($movement['detalle'] as $key => $item){
            array_push($bienes, [
                'en_BienesGRR' => [
                    'at_Cantidad' => $item['cant_mde'],
                    'at_UnidadMedida' => $item['um_sunat_code'] ?? 'SET',
                    'at_Descripcion' => $item['des_mde'],
                    'at_Codigo' => $item['cod_inv']
                ]
            ]);
        }

        $result = [
            'ent_GuiaRemisionRemitente' => [
                'ent_RemitenteGRR' => [
                    'at_NumeroDocumentoIdentidad' => $remitenteDocumento,
                    'at_RazonSocial' => $remitenteRazonSocial,
                    'at_NombreComercial' => $remitenteNombreComercial
                ],
                'ent_DestinatarioGRR' => [
                    'at_TipoDocumentoIdentidad' => $destinatarioTipoDocumento,
                    'at_NumeroDocumentoIdentidad' => $destinatarioDocumento,
                    'at_RazonSocial' => $destinatarioRazonSocial,
                    'ent_Correo' => [
                        'at_CorreoPrincipal' => $destinatarioCorreoPrincipal,
                        'aa_CorreoSecundario' => [
                            'string' => $destinatarioCorreoSecundario
                        ]
                    ]
                ],
                'ent_DatosGeneralesGRR' => [
                    'at_FechaEmision' => $generalFechaEmision,
                    'at_Serie' => $generalSerie,
                    'at_Numero' => $generalNumero,
                    'at_Observacion' => $generalObservacion,
                    'at_HoraEmision' => $generalHoraEmision,
                    'ent_InformacionTrasladoGRR' => [
                        'at_CodigoMotivo' => '06',
                        'ent_InformacionPesoBrutoGRR' => [
                            'at_Peso' => $trasladoPeso,
                            'at_UnidadMedida' => 'KGM',
                            'at_Cantidad' => $trasladoCantidad,
                        ],
                        'l_InformacionTransporteGRR' => $transports,
                        'ent_PuntoPartidaGRR' => [
                            'at_Ubigeo' => $puntoPartidaUbigeo,
                            'at_DireccionCompleta' => $puntoPartidaDireccion
                        ],
                        'ent_PuntoLlegadaGRR' => [
                            'at_Ubigeo' => $puntoLlegadaUbigeo,
                            'at_DireccionCompleta' => $puntoLlegadaDireccion
                        ],
                        'l_BienesGRR' => $bienes
                    ]
                ],
                'at_ControlOtorgamiento' => 1
            ]
        ];

        return $result;
    }

    public static function parseStoreOthersGuide($movement, $data = []){
        $remitenteDocumento = ValidateHelper::validateProperty($data, ['almacen_partida.document'])
            ?$data['almacen_partida']['document']
            :$movement['almacen_partida']['company']['document'];

        $remitenteRazonSocial = ValidateHelper::validateProperty($data, ['almacen_partida.name'])
            ?$data['almacen_partida']['name']
            :$movement['almacen_partida']['company']['name'];

        $remitenteNombreComercial = ValidateHelper::validateProperty($data, ['almacen_partida.commercial_name'])
            ?$data['almacen_partida']['commercial_name']
            :$movement['almacen_partida']['company']['commercial_name'];

        $destinatarioTipoDocumento = ValidateHelper::validateProperty($data, ['almacen_destino.document_type_code'])
            ?$data['almacen_destino']['document_type_code']
            :$movement['almacen_destino']['company']['document_type_code'];

        $destinatarioDocumento = ValidateHelper::validateProperty($data, ['almacen_destino.document'])
            ?$data['almacen_destino']['document']
            :$movement['almacen_destino']['company']['document'];
        $destinatarioRazonSocial = ValidateHelper::validateProperty($data, ['almacen_destino.name'])
            ?$data['almacen_destino']['name']
            :$movement['almacen_destino']['company']['name'];
    
        $destinatarioCorreoPrincipal = ValidateHelper::validateProperty($data, ['almacen_destino.email_principal'])
            ?$data['almacen_destino']['email_principal']
            :$movement['almacen_destino']['email_principal'];

        $destinatarioCorreoSecundario =  ValidateHelper::validateProperty($data, ['almacen_destino.email_secondary'])
            ?$data['almacen_destino']['email_secondary']
            :$movement['almacen_destino']['email_secondary'];

        $generalFechaEmision = ValidateHelper::validateProperty($data, ['fecha_emision'])
            ?$data['fecha_emision']
            :$movement['fecha_emision'];

        $generalSerie = ValidateHelper::validateProperty($data, ['serie'])
            ?$data['serie']
            :$movement['serie'];

        $generalNumero = ValidateHelper::validateProperty($data, ['numero'])
            ?$data['numero']
            :$movement['numero'];

        $generalObservacion = ValidateHelper::validateProperty($data, ['observacion'])
            ?$data['observacion']
            :$movement['observacion'];
            
        $generalHoraEmision = ValidateHelper::validateProperty($data, ['hora_emision'])
            ?$data['hora_emision']
            :$movement['hora_emision'];
        
        $trasladoPeso = ValidateHelper::validateProperty($data, ['peso'])
            ?$data['peso']
            :$movement['peso'];
            
        $trasladoCantidad = ValidateHelper::validateProperty($data, ['cantidad'])
            ?$data['cantidad']
            :$movement['cantidad']; 

        $descriptionTransfer = ValidateHelper::validateProperty($data, ['description_transfer'])
            ?$data['description_transfer']
            :$movement['description_transfer']; 

        $transporteModalidad = ValidateHelper::validateProperty($data, ['modalidad_transporte'])
            ?$data['modalidad_transporte']
            :$movement['modalidad_transporte'];

        $transports = [];
        if(ValidateHelper::validateProperty($data, ['transports'])){
            if(is_array($data['transports'])){
                if(count($data['transports']) > 0){
                    $transporteFechaInicio = $data['transports'][0]['start_date'];

                    if($transporteModalidad == 1){
                        $transports = [
                            'en_InformacionTransporteGRR' => [
                                'at_Modalidad' => "0".$transporteModalidad,
                                'at_FechaInicio' => $transporteFechaInicio,
                                'ent_TransportePublicoGRR' => [
                                    'at_TipoDocumentoIdentidad' => $data['transports'][0]['document_type'],
                                    'at_NumeroDocumentoIdentidad' => $data['transports'][0]['document'],
                                    'at_RazonSocial' => $data['transports'][0]['company_name'],
                                    'at_NumeroMTC' => $data['transports'][0]['mtc_number']
                                ]
                            ]
                        ];
                    }
                    else{
                        $transports = [
                            'en_InformacionTransporteGRR' => [
                                'at_Modalidad' => "0".$transporteModalidad,
                                'at_FechaInicio' => $transporteFechaInicio,
                                'ent_TransportePrivadoGRR' => [
                                    'l_ConductorGRR' => [],
                                    'l_VehiculoGRR' => []
                                ]
                            ]
                        ];

                        foreach($data['transports'] as $transport){
                            array_push($transports['en_InformacionTransporteGRR']['ent_TransportePrivadoGRR']['l_ConductorGRR'], [
                                'en_ConductorGRR' => [
                                    'at_TipoDocumentoIdentidad' => $transport['document_type'],
                                    'at_NumeroDocumentoIdentidad' => $transport['document'],
                                    'at_Licencia' => $transport['license'],
                                    'at_Nombres' => $transport['name'],
                                    'at_Apellidos' => $transport['last_name']
                                ]
                            ]);
                        }

                        foreach($data['vehicles'] as $vehicle){
                            array_push($transports['en_InformacionTransporteGRR']['ent_TransportePrivadoGRR']['l_VehiculoGRR'], [
                                'en_VehiculoGRR' => [
                                    'aa_NumeroPlaca' => [
                                        'string' => $vehicle['plate']
                                    ]
                                ]
                            ]);
                        }
                    }
                }
            }
        }
        else{
            if(is_array($movement['transports'])){
                if(count($movement['transports']) > 0){
                    if($transporteModalidad == 1){
                        $transports = [
                            'en_InformacionTransporteGRR' => [
                                'at_Modalidad' => "0".$transporteModalidad,
                                'at_FechaInicio' => $movement['transports'][0]['start_date'],
                                'ent_TransportePublicoGRR' => [
                                    'at_TipoDocumentoIdentidad' => $movement['transports'][0]['document_type'],
                                    'at_NumeroDocumentoIdentidad' => $movement['transports'][0]['document'],
                                    'at_RazonSocial' => $movement['transports'][0]['company_name'],
                                    'at_NumeroMTC' => $movement['transports'][0]['mtc_number']
                                ]
                            ]
                        ];
                    }
                    else{
                        $transports = [
                            'en_InformacionTransporteGRR' => [
                                'at_Modalidad' => "0".$transporteModalidad,
                                'at_FechaInicio' => $movement['transports'][0]['start_date'],
                                'ent_TransportePrivadoGRR' => [
                                    'l_ConductorGRR' => [],
                                    'l_VehiculoGRR' => []
                                ]
                            ]
                        ];

                        foreach($movement['transports'] as $transport){
                            array_push($transports['en_InformacionTransporteGRR']['ent_TransportePrivadoGRR']['l_ConductorGRR'], [
                                'en_ConductorGRR' => [
                                    'at_TipoDocumentoIdentidad' => $transport['document_type'],
                                    'at_NumeroDocumentoIdentidad' => $transport['document'],
                                    'at_Licencia' => $transport['license'],
                                    'at_Nombres' => $transport['name'],
                                    'at_Apellidos' => $transport['last_name']
                                ]
                            ]);
                        }

                        foreach($movement['vehicles'] as $vehicle){
                            array_push($transports['en_InformacionTransporteGRR']['ent_TransportePrivadoGRR']['l_VehiculoGRR'], [
                                'en_VehiculoGRR' => [
                                    'aa_NumeroPlaca' => [
                                        'string' => $vehicle['plate']
                                    ]
                                ]
                            ]);
                        }
                    }
                }
            }
        }

        // SUPPLIER
        $supplierDocumentType = ValidateHelper::validateProperty($data, ['supplier.document_type'])
            ?$data['supplier']['document_type']
            :$movement['supplier']['document_type'];

        $supplierDocument = ValidateHelper::validateProperty($data, ['supplier.document'])
            ?$data['supplier']['document']
            :$movement['supplier']['document'];

        $supplierName = ValidateHelper::validateProperty($data, ['supplier.name'])
            ?$data['supplier']['name']
            :$movement['supplier']['name'];


        // BUYER
        $buyerDocumentType = ValidateHelper::validateProperty($data, ['buyer.document_type'])
            ?$data['buyer']['document_type']
            :$movement['buyer']['document_type'];

        $buyerDocument = ValidateHelper::validateProperty($data, ['buyer.document'])
            ?$data['buyer']['document']
            :$movement['buyer']['document'];

        $buyerName = ValidateHelper::validateProperty($data, ['buyer.name'])
            ?$data['buyer']['name']
            :$movement['buyer']['name'];
        

        $puntoPartidaUbigeo = ValidateHelper::validateProperty($data, ['almacen_partida.ubigeo'])
            ?$data['almacen_partida']['ubigeo']
            :$movement['almacen_partida']['district']['code'];
            
        $puntoPartidaDireccion = ValidateHelper::validateProperty($data, ['almacen_partida.address'])
            ?$data['almacen_partida']['address']
            :$movement['almacen_partida']['direccion_alm'];

        $puntoPartidaDocumento = ValidateHelper::validateProperty($data, ['almacen_partida.document'])
            ?$data['almacen_partida']['document']
            :$movement['almacen_partida']['company']['document'];
            
        $puntoLlegadaUbigeo = ValidateHelper::validateProperty($data, ['almacen_destino.ubigeo'])
            ?$data['almacen_destino']['ubigeo']
            :$movement['almacen_destino']['district']['code'];

        $puntoLlegadaDireccion = ValidateHelper::validateProperty($data, ['almacen_destino.address'])
            ?$data['almacen_destino']['address']
            :$movement['almacen_destino']['direccion_alm'];

        $puntoLlegadaDocumento = ValidateHelper::validateProperty($data, ['almacen_destino.document'])
            ?$data['almacen_destino']['document']
            :$movement['almacen_destino']['company']['document'];

        $bienes = [];

        foreach($movement['detalle'] as $key => $item){
            array_push($bienes, [
                'en_BienesGRR' => [
                    'at_Cantidad' => $item['cant_mde'],
                    'at_UnidadMedida' => $item['um_sunat_code'] ?? 'SET',
                    'at_Descripcion' => $item['des_mde'],
                    'at_Codigo' => $item['cod_inv']
                ]
            ]);
        }

        $result = [
            'ent_GuiaRemisionRemitente' => [
                'ent_RemitenteGRR' => [
                    'at_NumeroDocumentoIdentidad' => $remitenteDocumento,
                    'at_RazonSocial' => $remitenteRazonSocial,
                    'at_NombreComercial' => $remitenteNombreComercial
                ],
                'ent_DestinatarioGRR' => [
                    'at_TipoDocumentoIdentidad' => $destinatarioTipoDocumento,
                    'at_NumeroDocumentoIdentidad' => $destinatarioDocumento,
                    'at_RazonSocial' => $destinatarioRazonSocial,
                    'ent_Correo' => [
                        'at_CorreoPrincipal' => $destinatarioCorreoPrincipal,
                        'aa_CorreoSecundario' => [
                            'string' => $destinatarioCorreoSecundario
                        ]
                    ]
                ],
                'ent_ProveedorGRR' => [
                    'at_TipoDocumentoIdentidad' => $supplierDocumentType,
                    'at_NumeroDocumentoIdentidad' => $supplierDocument,
                    'at_RazonSocial' => $supplierName
                ],
                'ent_CompradorGRR' => [
                    'at_TipoDocumentoIdentidad' => $buyerDocumentType,
                    'at_NumeroDocumentoIdentidad' => $buyerDocument,
                    'at_RazonSocial' => $buyerName
                ],
                'ent_DatosGeneralesGRR' => [
                    'at_FechaEmision' => $generalFechaEmision,
                    'at_Serie' => $generalSerie,
                    'at_Numero' => $generalNumero,
                    'at_Observacion' => $generalObservacion,
                    'at_HoraEmision' => $generalHoraEmision,
                    'ent_InformacionTrasladoGRR' => [
                        'at_CodigoMotivo' => '13',
                        'at_DescripcionMotivo' => $descriptionTransfer,
                        'ent_InformacionPesoBrutoGRR' => [
                            'at_Peso' => $trasladoPeso,
                            'at_UnidadMedida' => 'KGM',
                            'at_Cantidad' => $trasladoCantidad,
                        ],
                        'l_InformacionTransporteGRR' => $transports,
                        'ent_PuntoPartidaGRR' => [
                            'at_Ubigeo' => $puntoPartidaUbigeo,
                            'at_DireccionCompleta' => $puntoPartidaDireccion
                        ],
                        'ent_PuntoLlegadaGRR' => [
                            'at_Ubigeo' => $puntoLlegadaUbigeo,
                            'at_DireccionCompleta' => $puntoLlegadaDireccion
                        ],
                        'l_BienesGRR' => $bienes
                    ]
                ],
                'at_ControlOtorgamiento' => 1
            ]
        ];

        return $result;
    }
}