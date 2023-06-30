<?php
class TransitMovementHelper{
    public function __construct() {
    }

    public static function format($data){
        $response = [];
        if($data){
            $startStoresIds = [];
            $endStoresIds = [];
            $startStore = null;
            $endStore = null;

            foreach($data as $index => $row){
                $indexExist = -1;
                foreach($response as $iRes => $resItem){
                    if($resItem['id'] == $row['id_movt']){
                        $indexExist = $iRes;
                    }
                }

                if($indexExist < 0){
                    $newItem = [];
                    $newItem['id'] = $row['id_movt'];
                    
                    $newItem['start_store'] = [
                        'id' => $row['almacen_ini_id'],
                        'name' => $row['almacen_ini_titulo_alm'],
                        'address' => $row['establishment_ini_id']?$row['establishment_ini_address']:$row['almacen_ini_direccion_alm'],
                        'establishment_id' => $row['establishment_ini_id'],
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
                            'id' => $row['establishment_ini_id']?$row['u_est_ini_id_ubigeo']:$row['u_alm_ini_id_ubigeo'],
                            'name' => $row['establishment_ini_id']?$row['u_est_ini_nombre_ubigeo']:$row['u_alm_ini_nombre_ubigeo'],
                            'code' => $row['establishment_ini_id']?$row['u_est_ini_codigo_inei']:$row['u_alm_ini_codigo_inei']
                        ]
                    ];
                    
                    if($row['almacen_ini_id']){
                        $startStore = $newItem['start_store'];
                    }
        
                    $newItem['end_store'] = [
                        'id' => $row['almacen_des_id'],
                        'name' => $row['almacen_des_titulo_alm'],
                        'address' => $row['establishment_des_id']?$row['establishment_des_address']:$row['almacen_des_direccion_alm'],
                        'establishment_id' => $row['establishment_des_id'],
                        'email_principal' => $row['transfers_guides_email_principal'],
                        'email_secondary' => $row['transfers_guides_email_secondary'],
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
                            'id' => $row['establishment_des_id']?$row['u_est_des_id_ubigeo']:$row['u_alm_des_id_ubigeo'],
                            'name' => $row['establishment_des_id']?$row['u_est_des_nombre_ubigeo']:$row['u_alm_des_nombre_ubigeo'],
                            'code' => $row['establishment_des_id']?$row['u_est_des_codigo_inei']:$row['u_alm_des_codigo_inei']
                        ]
                    ];

                    if($row['almacen_des_id']){
                        $endStore = $newItem['end_store'];
                    }

                    $newItem['detail'] = [
                        [
                            'movement_id' => $row['id_movt'],
                            'movement_detail_id' => $row['id_mtde'],
                            'inventory_id' => $row['id_inv'],
                            'des_mde' => $row['des_mde'],
                            'unit_measure' => $row['um_sunat_code'],
                            'quantity' => $row['cant_mde'],
                            'code' => $row['inventario_cod_inv'],
                        ]
                    ];

                    if(!in_array($row['almacen_ini_id'], $startStoresIds)){
                        array_push($startStoresIds, $row['almacen_ini_id']);
                    }

                    if(!in_array($row['almacen_des_id'], $endStoresIds)){
                        array_push($endStoresIds, $row['almacen_des_id']);
                    }

                    array_push($response, $newItem);
                }
                else{
                    array_push($response[$indexExist]['detail'], [
                        'movement_id' => $row['id_movt'],
                        'movement_detail_id' => $row['id_mtde'],
                        'inventory_id' => $row['id_inv'],
                        'des_mde' => $row['des_mde'],
                        'unit_measure' => $row['um_sunat_code'],
                        'quantity' => $row['cant_mde'],
                        'code' => $row['inventario_cod_inv'],
                    ]);
                }
            }
        }
        
        return [
            'data' => $response,
            'start_store_id' => $startStoresIds,
            'end_store_id' => $endStoresIds,
            'start_store' => $startStore,
            'end_store' => $endStore
        ];
    }
}