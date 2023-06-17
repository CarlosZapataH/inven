<?php
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
}