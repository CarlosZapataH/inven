<?php
class TransitMovementHelper{
    public function __construct() {
    }

    public static function format($data){
        $response = [];
        if($data){
            $transportsIds = [];
            $vehiclesIds = [];

            foreach($data as $index => $row){
                if($index == 0){
                    // PRINCIPAL
                    $response['id_movt'] = $row['id_movt'];
                    $response['action_mov'] = $row['action_mov'];
                    $response['id_alm_ini'] = $row['id_alm_ini'];
                    $response['id_alm_des'] = $row['id_alm_des'];
                    $response['observ_mov'] = $row['observ_mov'];
                    $response['nro_mov'] = $row['nro_mov'];
                    $response['fechaguia_mov'] = $row['fechaguia_mov'];

                    $response['transfer_guide_id'] = $row['transfers_guides_id'];
                    $response['fecha_emision'] = $row['transfers_guides_date_issue'];
                    $response['serie'] = $row['transfers_guides_serie'];
                    $response['numero'] = $row['transfers_guides_number'];
                    $response['observacion'] = $row['transfers_guides_observations'];
                    $response['hora_emision'] = $row['transfers_guides_time_issue'];
                    $response['peso'] = $row['transfers_guides_total_witght'];
                    $response['flag_sent'] = $row['transfers_guides_flag_sent'];
                    $response['sent_attempts'] = $row['transfers_guides_sent_attempts'] ?? 0;
                    $response['tci_send'] = $row['transfers_guides_tci_send'];
                    $response['tci_response'] = $row['transfers_guides_tci_response'];
                    $response['modalidad_transporte'] = $row['transfers_guides_transport_modality'];

                    // DETAIL
                    $response['detalle'] = [];

                    // TRANSPORT
                    $response['transports'] = [];

                    // VEHICLES
                    $response['vehicles'] = [];
        
                    // START
                    $response['almacen_partida'] = [
                        'id' => $row['almacen_ini_id'],
                        'titulo_alm' => $row['almacen_ini_titulo_alm'],
                        'direccion_alm' => $row['almacen_ini_direccion_alm'],
                        'company' => [
                            'id' => $row['company_ini_id'],
                            'name' => $row['company_ini_name'],
                            'commercial_name' => $row['company_ini_commercial_name'],
                            'document_type' => $row['document_types_ini_description'],
                            'document_type_code' => $row['document_types_ini_code'],
                            'document' => $row['company_ini_document']
                        ],
                        'district' => [
                            'id' => $row['u_alm_ini_id_ubigeo'],
                            'name' => $row['u_alm_ini_nombre_ubigeo'],
                            'code' => $row['u_alm_ini_codigo_inei']
                        ]
                    ];
        
                    // END
                    $response['almacen_destino'] = [
                        'id' => $row['almacen_des_id'],
                        'titulo_alm' => $row['almacen_des_titulo_alm'],
                        'direccion_alm' => $row['almacen_des_direccion_alm'],
                        'email_principal' => $row['transfers_guides_email_principal'],
                        'email_secondary' => $row['transfers_guides_email_secondary'],
                        'company' => [
                            'id' => $row['company_des_id'],
                            'name' => $row['company_des_name'],
                            'commercial_name' => $row['company_des_commercial_name'],
                            'document_type' => $row['document_types_des_description'],
                            'document_type_code' => $row['document_types_des_code'],
                            'document' => $row['company_des_document']
                        ],
                        'district' => [
                            'id' => $row['u_alm_des_id_ubigeo'],
                            'name' => $row['u_alm_des_nombre_ubigeo'],
                            'code' => $row['u_alm_des_codigo_inei']
                        ]
                    ];
                }

                // TRANSPORT
                if(!in_array($row['transports_id'], $transportsIds)){
                    array_push($response['transports'], [
                        'id' => $row['transports_id'],
                        'modality' => $row['transports_modalidad'],
                        'start_date' => $row['transports_fecha_inicio'],
                        'document_type' => $row['transports_tipo_documento'],
                        'document' => $row['transports_documento'],
                        'company_name' => $row['transports_razon_social'],
                        'mtc_number' => $row['transports_numero_mtc'],
                        'name' => $row['transports_name'],
                        'last_name' => $row['transports_last_name'],
                        'license' => $row['transports_license']
                    ]);
                    array_push($transportsIds, $row['transports_id']);
                }

                // VAHICLE
                if(!in_array($row['vehicles_id'], $vehiclesIds)){
                    array_push($response['vehicles'], [
                        'plate' => $row['vehicles_plate']
                    ]);
                    array_push($vehiclesIds, $row['vehicles_id']);
                }

                // DETAIL
                array_push($response['detalle'], [
                    'des_mde' => $row['des_mde'],
                    'um_mde' => $row['inventario_um_inv'],
                    'cant_mde' => $row['cant_mde'],
                    'cod_inv' => $row['inventario_cod_inv'],
                ]);
            }
        }
        return $response;
    }
}