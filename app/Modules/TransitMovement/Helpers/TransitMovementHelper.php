<?php
class TransitMovementHelper{
    public function __construct() {
    }

    public static function format($data){
        $response = [];
        if($data){
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

                    // DETAIL
                    $response['detalle'] = [];
        
                    // START
                    $response['almacen_partida'] = [
                        'titulo_alm' => $row['almacen_ini_titulo_alm'],
                        'direccion_alm' => $row['almacen_ini_direccion_alm'],
                        'company' => [
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
                        'titulo_alm' => $row['almacen_des_titulo_alm'],
                        'direccion_alm' => $row['almacen_des_direccion_alm'],
                        'company' => [
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

                // DETAIL
                array_push($response['detalle'], [
                    'des_mde' => $row['des_mde'],
                    'um_mde' => $row['inventario_um_inv'],
                    'cant_mde' => $row['cant_mde']
                ]);
            }
        }
        return $response;
    }
}