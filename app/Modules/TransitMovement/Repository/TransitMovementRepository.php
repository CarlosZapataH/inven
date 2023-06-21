<?php
require_once __DIR__ . '/../../Common/Repositories/CommonRepository.php';
require_once __DIR__ . '/../../../Models/TransitMovement.php';
require_once __DIR__ . '/../Helpers/TransitMovementHelper.php';
require_once __DIR__ . '/../Contract/ITransitMovement.php';

class TransitMovementRepository extends CommonRepository implements ITransitMovement {

    public function __construct() {
        parent::__construct(TransitMovement::class);
    }

    public function findWithDetails($id){
        try{
            $data = null;
            $query = self::getQueryDefault();
            if($id != ''){
                $query .= " WHERE movimientos_transito.id_movt IN({$id}) AND flag_available = 1";
            }
            $result = self::query($query);

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
            echo $e->getMessage();
        }
        return null;
    }

    public function findOneWithDetails($id){
        try{
            $data = null;
            $result = self::query(self::getQueryDefault().' 
                WHERE 
                    movimientos_transito.id_movt = '.$id.'
            ');

            if($result){
                if(is_array($result)){
                    if(count($result) > 0){
                        $data = TransitMovementHelper::format($result)[0];
                    }
                }
            }

            return $data;
        }
        catch(Exception $e){

        }
        return null;
    }

    private function getQueryDefault(){
        $query = '
                SELECT 
                    movimientos_transito.id_movt,
                    movimientos_transito.id_alm_ini,
                    movimientos_transito.id_alm_des, 
                    movimientos_transito_detalle.id_mtde,
                    movimientos_transito_detalle.des_mde,
                    movimientos_transito_detalle.cant_mde,
                    almacen_ini.id_alm as almacen_ini_id,
                    almacen_ini.titulo_alm as almacen_ini_titulo_alm,
                    almacen_ini.direccion_alm as almacen_ini_direccion_alm,
                    almacen_des.id_alm as almacen_des_id,
                    almacen_des.titulo_alm as almacen_des_titulo_alm,
                    almacen_des.direccion_alm as almacen_des_direccion_alm,
                    company_ini.id as company_ini_id,
                    company_ini.name as company_ini_name,
                    company_ini.commercial_name as company_ini_commercial_name,
                    company_ini.document as company_ini_document,
                    company_des.id as company_des_id,
                    company_des.name as company_des_name,
                    company_des.commercial_name as company_des_commercial_name,
                    company_des.document as company_des_document,
                    document_types_ini.id as document_types_ini_id,
                    document_types_des.id as document_types_des_id,
                    document_types_ini.code as document_types_ini_code,
                    document_types_des.code as document_types_des_code,
                    document_types_ini.description as document_types_ini_description,
                    document_types_des.description as document_types_des_description,
                    u_est_ini.id_ubigeo as u_est_ini_id_ubigeo,
                    u_est_ini.nombre_ubigeo as u_est_ini_nombre_ubigeo,
                    u_est_ini.codigo_inei as u_est_ini_codigo_inei,
                    u_est_des.id_ubigeo as u_est_des_id_ubigeo,
                    u_est_des.nombre_ubigeo as u_est_des_nombre_ubigeo,
                    u_est_des.codigo_inei as u_est_des_codigo_inei,
                    inventario.id_inv,
                    inventario.um_inv as inventario_um_inv,
                    inventario.cod_inv as inventario_cod_inv,
                    establishment_ini.id as establishment_ini_id,
                    establishment_ini.address as establishment_ini_address,
                    establishment_des.id as establishment_des_id,
                    establishment_des.address as establishment_des_address,
                    (
                        SELECT 
                            unidad_medida.sunat_code 
                        FROM unidad_medida 
                        WHERE CONVERT(unidad_medida.cod_um USING utf8) = CONVERT(inventario_um_inv USING utf8) LIMIT 1
                    ) as um_sunat_code
                FROM movimientos_transito
                INNER JOIN movimientos_transito_detalle ON movimientos_transito_detalle.id_movt = movimientos_transito.id_movt
                LEFT JOIN almacen as almacen_ini ON almacen_ini.id_alm = movimientos_transito.id_alm_ini
                LEFT JOIN almacen as almacen_des ON almacen_des.id_alm = movimientos_transito.id_alm_des
                LEFT JOIN companies as company_ini ON company_ini.id = almacen_ini.company_id
                LEFT JOIN companies as company_des ON company_des.id = almacen_des.company_id
                LEFT JOIN establishments as establishment_ini ON establishment_ini.id = almacen_ini.establishment_id
                LEFT JOIN establishments as establishment_des ON establishment_des.id = almacen_des.establishment_id
                LEFT JOIN document_types as document_types_ini ON document_types_ini.id = company_ini.document_type_id
                LEFT JOIN document_types as document_types_des ON document_types_des.id = company_des.document_type_id
                LEFT JOIN ubigeo as u_est_ini ON u_est_ini.id_ubigeo = establishment_ini.ubigeo_id
                LEFT JOIN ubigeo as u_est_des ON u_est_des.id_ubigeo = establishment_des.ubigeo_id
                LEFT JOIN inventario ON inventario.id_inv = movimientos_transito_detalle.id_inv
        ';

        return $query;
    }

    public function updateAvailable($id, $value){
        $success = false;
        try{
            $result = self::query("
                UPDATE movimientos_transito SET flag_available = {$value} WHERE id_movt IN({$id})
            ");

            if($result){
                $success = $result;
            }

        }
        catch(Exception $e){
            echo $e->getMessage();
        }
        return $success;

    }
}