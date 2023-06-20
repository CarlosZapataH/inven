<?php
require_once __DIR__ . '/../Repository/TransferGuideRepository.php';
require_once __DIR__ . '/../../../Modules/Setting/Repository/SettingRepository.php';

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

    public static function generateSerialNumber(){
        $transferGuideRepository = new TransferGuideRepository();
        $settingRepository = new SettingRepository();

        $settingsGuide = $settingRepository->findAllBy('group', 'gruides_between_company');

        $serie = "T001";
        $number = 1;
        $lengthNumber = 4;
        $completeCh = "000";

        if($settingsGuide){
            foreach($settingsGuide as $setting){
                if($setting['name'] == 'serie'){
                    $serie = $setting['value']; 
                }
                else if($setting['name'] == 'number'){
                    $number = $setting['value']; 
                }
                else if($setting['name'] == 'number_length'){
                    $lengthNumber = $setting['value']; 
                }
            }
        }
        
        $findLastCode = $transferGuideRepository->getMaxSerieNumber($serie);
        if($findLastCode){
            $completeCh = "";
            $number = ((int)$findLastCode['max_number']) + 1;

            $lengthCurrent = strlen(strval($number));
            if($lengthCurrent < $lengthNumber){
                for($i = 0; $i < ($lengthNumber - $lengthCurrent); $i++){
                    $completeCh .= "0";
                }
            }
        }

        $newCode = $serie . '-' . $completeCh . $number;
        echo $newCode;
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
                                    'aditional_description' => $row['inventory_additional_description'],
                                    'unit_measure' => $row['unit_measure_sunat'],
                                    'quantity' => $row['inventory_quantity'],
                                    'code' => $row['inventory_code']
                                ]
                            ],
                            'transports' => [
                                [
                                    'id' => $row['transports_id'],
                                    'document_type_code' => $row['transports_document_type_code'],
                                    'document' => $row['transports_document'],
                                    'start_date' => $row['transports_start_date'],
                                    'company_name' => $row['transports_company_name'],
                                    'mtc_number' => $row['transports_mtc_number'],
                                    'license' => $row['transports_license'],
                                    'name' => $row['transports_name'],
                                    'last_name' => $row['transports_last_name']
                                ]
                            ],
                            'vehicles' => [
                                [
                                    'id' => $row['vehicles_id'],
                                    'plate' => $row['vehicles_plate']
                                ]
                            ]
                        ];

                        array_push($response, $newRow);
                    }
                    else{
                        if($iDetail < 0){
                            array_push($response[$iGuide]['details'], [
                                'id' => $row['transfer_guide_detail_id'],
                                'name' => $row['inventory_name'],
                                'aditional_description' => $row['inventory_additional_description'],
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

                        if($iVehicle < 0){
                            array_push($response[$iGuide]['vehicles'], [
                                'id' => $row['vehicles_id'],
                                'plate' => $row['vehicles_plate']
                            ]);
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