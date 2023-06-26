<?php
require_once __DIR__ . '/../Repository/TransferGuideRepository.php';
require_once __DIR__ . '/../../../Modules/Establishment/Repository/EstablishmentRepository.php';

class TransferGuideHelper{

    public function __construct() {
    }

    public static function format($data){
        $response = [];
        if($data){
            if(count($data) > 0){
                $response = [
                    'serie' => $data[0]['transfers_guides_serie'],
                    'number' => $data[0]['transfers_guides_number'],
                    'date_issue' => $data[0]['transfers_guides_date_issue'],
                    'time_issue' => $data[0]['transfers_guides_time_issue'],
                    'total_witght' => $data[0]['transfers_guides_total_witght'],
                    'total_quantity' => $data[0]['transfers_guides_total_quantity'],
                    'transport_modality' => $data[0]['transfers_guides_transport_modality'],
                    'motive_code' => $data[0]['transfers_guides_motive_code'],
                    'unit_measure' => $data[0]['transfers_guides_unit_measure'],
                    'observation' => $data[0]['transfers_guides_observations'],
                    'start_store' => [],
                    'end_store' => [
                        'email_principal' => $data[0]['transfers_guides_email_principal'],
                        'email_secondary' => $data[0]['transfers_guides_email_secondary']
                    ],
                    'detail' => [],
                    'transports' => [],
                    'vehicles' => []
                ];


                foreach($data as $index => $row){
                    // START STORE
                    if(!isset($response['start_store']['document_type']) && $row['document_types_ini_code']){
                       $response['start_store']['document_type'] = $row['document_types_ini_code'];
                    }  
                    if(!isset($response['start_store']['name']) && $row['company_ini_name']){
                        $response['start_store']['name'] = $row['company_ini_name'];
                    }  
                    if(!isset($response['start_store']['commercial_name']) && $row['company_ini_commercial_name']){
                        $response['start_store']['commercial_name'] = $row['company_ini_commercial_name'];
                    }  
                    if(!isset($response['start_store']['ubigeo']) && $row['u_alm_ini_codigo_inei']){
                        $response['start_store']['ubigeo'] = $row['u_alm_ini_codigo_inei'];
                    }  
                    if(!isset($response['start_store']['address']) && $row['almacen_ini_direccion_alm']){
                        $response['start_store']['address'] = $row['almacen_ini_direccion_alm'];
                    }  
                    if(!isset($response['start_store']['document']) && $row['company_ini_document']){
                        $response['start_store']['document'] = $row['company_ini_document'];
                    }  

                    // END STORE
                    if(!isset($response['end_store']['document_type']) && $row['document_types_des_code']){
                        $response['end_store']['document_type'] = $row['document_types_des_code'];
                    }  
                    if(!isset($response['end_store']['name']) && $row['company_des_name']){
                         $response['end_store']['name'] = $row['company_des_name'];
                    }  
                    if(!isset($response['end_store']['commercial_name']) && $row['company_des_commercial_name']){
                         $response['end_store']['commercial_name'] = $row['company_des_commercial_name'];
                    }
                    if(!isset($response['end_store']['ubigeo']) && $row['u_alm_des_codigo_inei']){
                         $response['end_store']['ubigeo'] = $row['u_alm_des_codigo_inei'];
                    }
                    if(!isset($response['end_store']['address']) && $row['almacen_des_direccion_alm']){
                         $response['end_store']['address'] = $row['almacen_des_direccion_alm'];
                    }
                    if(!isset($response['end_store']['document']) && $row['company_des_document']){
                         $response['end_store']['document'] = $row['company_des_document'];
                    }

                    $existTransport = false;
                    foreach($response['transports'] as $rTrans){
                        if($row['transports_id'] == $rTrans['id']){
                            $existTransport = true;
                        }
                    }

                    if(!$existTransport){
                        array_push($response['transports'], [
                            'id' => $row['transports_id'],
                            'start_date' => $row['transports_fecha_inicio'],
                            'document_type' => $row['transports_tipo_documento'],
                            'document' => $row['transports_documento'],
                            'company_name' => $row['transports_razon_social'],
                            'mtc_number' => $row['transports_numero_mtc'],
                            'license' => $row['transports_license'],
                            'name' => $row['transports_name'],
                            'last_name' => $row['transports_last_name']
                        ]);
                    }

                    $existVehicle = false;
                    foreach($response['vehicles'] as $rVeh){
                        if($row['vehicles_id'] == $rVeh['id']){
                            $existVehicle = true;
                        }
                    }

                    if(!$existVehicle){
                        array_push($response['vehicles'], [
                            'id' => $row['vehicles_id'],
                            'plate' => $row['vehicles_plate']
                        ]);
                    }

                    array_push($response['detail'], [
                        'cant_mde' => $row['cant_mde'],
                        'um_sunat_code' => $row['um_sunat_code'],
                        'des_mde' => $row['des_mde'],
                        'cod_inv' => $row['inventario_cod_inv'],
                        'additional_description' => $row['additional_description']
                    ]);
                }
            }
        }
        
        return $response;
    }

    public static function generateSerialNumber($establishmentId){
        $transferGuideRepository = new TransferGuideRepository();
        $establishmentRepository = new EstablishmentRepository();
        $establishment = $establishmentRepository->findBy('id', $establishmentId);

        $serie = $establishment['start_serie'];
        $number = $establishment['start_number'];
        $lengthNumber = $establishment['length_number'];
        $completeCh = "";
        
        $findLastCode = $transferGuideRepository->getMaxSerieNumber($serie);
        if($findLastCode){
            if($findLastCode['max_number']){
                $number = ((int)$findLastCode['max_number']) + 1;
            }
        }
        
        $lengthCurrent = strlen(strval($number));
        if($lengthCurrent < $lengthNumber){
            for($i = 0; $i < ($lengthNumber - $lengthCurrent); $i++){
                $completeCh .= "0";
            }
        }

        $newCode = [
            'serie' => $serie,
            'number' => $completeCh . $number
        ];
        return $newCode;
    }

    public static function formatList($data){
        $response = [];
        if($data){
            if(count($data) > 0){
                foreach($data as $row){
                    $iGuide = -1;
                    $iTransport = -1;
                    $iDetail = -1;
                    $iVehicle = -1;

                    foreach($response as $keyResponse => $itemResponse){
                        if($itemResponse['id'] == $row['id']){
                            $iGuide = $keyResponse;

                            foreach($itemResponse['details'] as $keyItemDetail => $itemDetail){
                                if($itemDetail['id'] == $row['transfer_guide_detail_id']){
                                    $iDetail = $keyItemDetail;
                                }
                            }

                            foreach($itemResponse['transports'] as $keyItemTransport => $itemTransport){
                                if($itemTransport['id'] == $row['transports_id']){
                                    $iTransport = $keyItemTransport;
                                }
                            }

                            foreach($itemResponse['vehicles'] as $keyItemVehicle => $itemVehicle){
                                if($itemVehicle['id'] == $row['vehicles_id']){
                                    $iVehicle = $keyItemVehicle;
                                }
                            }
                        }
                    }

                    if($iGuide < 0){
                        $newRow = [
                            'id' => $row['id'],
                            'serie' => $row['serie'],
                            'number' => $row['number'],
                            'date_issue' => $row['date_issue'],
                            'time_issue' => $row['time_issue'],
                            'observations' => $row['observations'],
                            'motive_code' => $row['motive_code'],
                            'motive_description' => $row['motive_description'],
                            'total_witght' => $row['total_witght'],
                            'unit_measure' => $row['unit_measure'],
                            'total_quantity' => $row['total_quantity'],
                            'transport_modality' => $row['transport_modality'],
                            'flag_sent' => $row['flag_sent'],
                            'sent_attempts' => $row['sent_attempts'],
                            'tci_response_code' => $row['tci_response_code'],
                            'tci_response_type' => $row['tci_response_type'],
                            'tci_response_description' => $row['tci_response_description'],
                            'tci_response_date' => $row['tci_response_date'],
                            'start_store' => [
                                'id' => $row['almacen_ini_id'],
                                'name' => $row['almacen_ini_titulo_alm'],
                                'address' => $row['establishment_ini_address'],
                                'establishment_id' => $row['establishment_ini_id'],
                                'establishment_code' => $row['establishment_ini_code'],
                                'company' => [
                                    'id' => $row['company_ini_id'],
                                    'name' => $row['company_ini_name'],
                                    'commercial_name' => $row['company_ini_commercial_name'],
                                    'document_type_id' => $row['document_types_ini_id'],
                                    'document_type' => $row['document_types_ini_description'],
                                    'document_type_code' => $row['document_types_ini_code'],
                                    'document' => $row['company_ini_document']
                                ],
                                'district' => [
                                    'id' => $row['u_est_ini_id_ubigeo'],
                                    'name' => $row['u_est_ini_nombre_ubigeo'],
                                    'code' => $row['u_est_ini_codigo_inei']
                                ],
                            ],
                            'end_store' => [
                                'id' => $row['almacen_des_id'],
                                'name' => $row['almacen_des_titulo_alm'],
                                'address' => $row['establishment_des_address'],
                                'establishment_id' => $row['establishment_des_id'],
                                'establishment_code' => $row['establishment_des_code'],
                                'email_principal' => $row['email_principal'],
                                'email_secondary' => $row['email_secondary'],
                                'company' => [
                                    'id' => $row['company_des_id'],
                                    'name' => $row['company_des_name'],
                                    'commercial_name' => $row['company_des_commercial_name'],
                                    'document_type_id' => $row['document_types_des_id'],
                                    'document_type' => $row['document_types_des_description'],
                                    'document_type_code' => $row['document_types_des_code'],
                                    'document' => $row['company_des_document']
                                ],
                                'district' => [
                                    'id' => $row['u_est_des_id_ubigeo'],
                                    'name' => $row['u_est_des_nombre_ubigeo'],
                                    'code' => $row['u_est_des_codigo_inei']
                                ],
                            ],
                            'details' => [
                                [
                                    'id' => $row['transfer_guide_detail_id'],
                                    'name' => $row['inventory_name'],
                                    'additional_description' => $row['inventory_additional_description'],
                                    'unit_measure' => $row['unit_measure_sunat'],
                                    'quantity' => $row['inventory_quantity'],
                                    'code' => $row['inventory_code']
                                ]
                            ],
                            'transports' => [],
                            'vehicles' => [],
                            'provider' => null,
                            'buyer' => null
                        ];

                        if($row['transports_id']){
                            array_push($newRow['transports'], [
                                'id' => $row['transports_id'],
                                'document_type_code' => $row['transports_document_type_code'],
                                'document' => $row['transports_document'],
                                'start_date' => $row['transports_start_date'],
                                'company_name' => $row['transports_company_name'],
                                'mtc_number' => $row['transports_mtc_number'],
                                'license' => $row['transports_license'],
                                'name' => $row['transports_name'],
                                'last_name' => $row['transports_last_name']
                            ]);
                        }


                        if($row['transport_modality'] == 2){
                            if($row['vehicles_id']){
                                array_push($newRow['vehicles'], [
                                    'id' => $row['vehicles_id'],
                                    'plate' => $row['vehicles_plate']
                                ]);
                            }
                        }

                        if($row['providers_id']){
                            $newRow['provider'] = [
                                'document_type_code' => $row['providers_document_type_code'],
                                'document' => $row['providers_document'],
                                'name' => $row['providers_name']
                            ];
                        }

                        if($row['buyers_id']){
                            $newRow['buyer'] = [
                                'document_type_code' => $row['buyers_document_type_code'],
                                'document' => $row['buyers_document'],
                                'name' => $row['buyers_name']
                            ];
                        }

                        array_push($response, $newRow);
                    }
                    else{
                        if($iDetail < 0){
                            array_push($response[$iGuide]['details'], [
                                'id' => $row['transfer_guide_detail_id'],
                                'name' => $row['inventory_name'],
                                'additional_description' => $row['inventory_additional_description'],
                                'unit_measure' => $row['unit_measure_sunat'],
                                'quantity' => $row['inventory_quantity'],
                                'code' => $row['inventory_code']
                            ]);
                        }

                        if($iTransport < 0){
                            array_push($response[$iGuide]['transports'], [
                                'id' => $row['transports_id'],
                                'document_type_code' => $row['transports_document_type_code'],
                                'document' => $row['transports_document'],
                                'start_date' => $row['transports_start_date'],
                                'company_name' => $row['transports_company_name'],
                                'mtc_number' => $row['transports_mtc_number'],
                                'license' => $row['transports_license'],
                                'name' => $row['transports_name'],
                                'last_name' => $row['transports_last_name']
                            ]);
                        }

                        if($row['transport_modality'] == 2){
                            if($iVehicle < 0){
                                array_push($response[$iGuide]['vehicles'], [
                                    'id' => $row['vehicles_id'],
                                    'plate' => $row['vehicles_plate']
                                ]);
                            }
                        }
                    }
                }


                /* foreach($data as $index => $row){
                    // START STORE
                    if(!isset($response['start_store']['document_type']) && $row['document_types_ini_code']){
                       $response['start_store']['document_type'] = $row['document_types_ini_code'];
                    }  
                    if(!isset($response['start_store']['name']) && $row['company_ini_name']){
                        $response['start_store']['name'] = $row['company_ini_name'];
                    }  
                    if(!isset($response['start_store']['commercial_name']) && $row['company_ini_commercial_name']){
                        $response['start_store']['commercial_name'] = $row['company_ini_commercial_name'];
                    }  
                    if(!isset($response['start_store']['ubigeo']) && $row['u_alm_ini_codigo_inei']){
                        $response['start_store']['ubigeo'] = $row['u_alm_ini_codigo_inei'];
                    }  
                    if(!isset($response['start_store']['address']) && $row['almacen_ini_direccion_alm']){
                        $response['start_store']['address'] = $row['almacen_ini_direccion_alm'];
                    }  
                    if(!isset($response['start_store']['document']) && $row['company_ini_document']){
                        $response['start_store']['document'] = $row['company_ini_document'];
                    }  

                    // END STORE
                    if(!isset($response['end_store']['document_type']) && $row['document_types_des_code']){
                        $response['end_store']['document_type'] = $row['document_types_des_code'];
                    }  
                    if(!isset($response['end_store']['name']) && $row['company_des_name']){
                         $response['end_store']['name'] = $row['company_des_name'];
                    }  
                    if(!isset($response['end_store']['commercial_name']) && $row['company_des_commercial_name']){
                         $response['end_store']['commercial_name'] = $row['company_des_commercial_name'];
                    }
                    if(!isset($response['end_store']['ubigeo']) && $row['u_alm_des_codigo_inei']){
                         $response['end_store']['ubigeo'] = $row['u_alm_des_codigo_inei'];
                    }
                    if(!isset($response['end_store']['address']) && $row['almacen_des_direccion_alm']){
                         $response['end_store']['address'] = $row['almacen_des_direccion_alm'];
                    }
                    if(!isset($response['end_store']['document']) && $row['company_des_document']){
                         $response['end_store']['document'] = $row['company_des_document'];
                    }

                    $existTransport = false;
                    foreach($response['transports'] as $rTrans){
                        if($row['transports_id'] == $rTrans['id']){
                            $existTransport = true;
                        }
                    }

                    if(!$existTransport){
                        array_push($response['transports'], [
                            'id' => $row['transports_id'],
                            'start_date' => $row['transports_fecha_inicio'],
                            'document_type' => $row['transports_tipo_documento'],
                            'document' => $row['transports_documento'],
                            'company_name' => $row['transports_razon_social'],
                            'mtc_number' => $row['transports_numero_mtc'],
                            'license' => $row['transports_license'],
                            'name' => $row['transports_name'],
                            'last_name' => $row['transports_last_name']
                        ]);
                    }

                    $existVehicle = false;
                    foreach($response['vehicles'] as $rVeh){
                        if($row['vehicles_id'] == $rVeh['id']){
                            $existVehicle = true;
                        }
                    }

                    if(!$existVehicle){
                        array_push($response['vehicles'], [
                            'id' => $row['vehicles_id'],
                            'plate' => $row['vehicles_plate']
                        ]);
                    }

                    array_push($response['detail'], [
                        'cant_mde' => $row['cant_mde'],
                        'um_sunat_code' => $row['um_sunat_code'],
                        'des_mde' => $row['des_mde'],
                        'cod_inv' => $row['inventario_cod_inv'],
                        'additional_description' => $row['additional_description']
                    ]);
                } */
            }
        }
        
        return $response;
    }
}