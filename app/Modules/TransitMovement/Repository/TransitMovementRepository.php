<?php
require_once __DIR__ . '/../../Common/Repositories/CommonRepository.php';
require_once __DIR__ . '/../../../Models/TransitMovement.php';
require_once __DIR__ . '/../Helpers/TransitMovementHelper.php';
require_once __DIR__ . '/../Contract/ITransitMovement.php';

class TransitMovementRepository extends CommonRepository implements ITransitMovement {
    public function __construct() {
        parent::__construct(TransitMovement::class);
    }

    public function getTransitMovement($id){
        try{
            $data = null;
            $result = self::query('
                SELECT 
                    movimientos_transito.id_movt,
                    movimientos_transito.action_mov,
                    movimientos_transito.id_alm_ini,
                    movimientos_transito.id_alm_des, 
                    movimientos_transito.observ_mov,
                    movimientos_transito.nro_mov,
                    movimientos_transito.fechaguia_mov,
                    movimientos_transito_detalle.des_mde,
                    movimientos_transito_detalle.cant_mde,
                    almacen_ini.titulo_alm as almacen_ini_titulo_alm,
                    almacen_ini.direccion_alm as almacen_ini_direccion_alm,
                    almacen_des.titulo_alm as almacen_des_titulo_alm,
                    almacen_des.direccion_alm as almacen_des_direccion_alm,
                    company_ini.name as company_ini_name,
                    company_ini.commercial_name as company_ini_commercial_name,
                    company_ini.document as company_ini_document,
                    company_des.name as company_des_name,
                    company_des.commercial_name as company_des_commercial_name,
                    company_des.document as company_des_document,
                    document_types_ini.code as document_types_ini_code,
                    document_types_des.code as document_types_des_code,
                    document_types_ini.description as document_types_ini_description,
                    document_types_des.description as document_types_des_description,
                    u_alm_ini.id_ubigeo as u_alm_ini_id_ubigeo,
                    u_alm_ini.nombre_ubigeo as u_alm_ini_nombre_ubigeo,
                    u_alm_ini.codigo_inei as u_alm_ini_codigo_inei,
                    u_alm_des.id_ubigeo as u_alm_des_id_ubigeo,
                    u_alm_des.nombre_ubigeo as u_alm_des_nombre_ubigeo,
                    u_alm_des.codigo_inei as u_alm_des_codigo_inei,
                    inventario.um_inv as inventario_um_inv
                FROM movimientos_transito
                INNER JOIN movimientos_transito_detalle ON movimientos_transito_detalle.id_movt = movimientos_transito.id_movt
                LEFT JOIN almacen as almacen_ini ON almacen_ini.id_alm = movimientos_transito.id_alm_ini
                LEFT JOIN almacen as almacen_des ON almacen_des.id_alm = movimientos_transito.id_alm_des
                LEFT JOIN companies as company_ini ON company_ini.id = almacen_ini.company_id
                LEFT JOIN companies as company_des ON company_des.id = almacen_des.company_id
                LEFT JOIN document_types as document_types_ini ON document_types_ini.id = company_ini.document_type_id
                LEFT JOIN document_types as document_types_des ON document_types_des.id = company_des.document_type_id
                LEFT JOIN ubigeo as u_alm_ini ON u_alm_ini.id_ubigeo = almacen_ini.distrito_alm
                LEFT JOIN ubigeo as u_alm_des ON u_alm_des.id_ubigeo = almacen_des.distrito_alm
                LEFT JOIN inventario ON inventario.id_inv = movimientos_transito_detalle.id_inv
                WHERE 
                    movimientos_transito.id_movt = '.$id.'
            ');

            if($result){
                if(is_array($result)){
                    if(count($result) > 0){
                        $data = TransitMovementHelper::format($result);
                    }
                }
            }

            return $data;
        }
        catch(Exception $e){

        }
        return null;
    }
}