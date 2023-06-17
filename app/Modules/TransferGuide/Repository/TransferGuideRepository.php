<?php
require_once __DIR__ . '/../../Common/Repositories/CommonRepository.php';
require_once __DIR__ . '/../../../Models/TransferGuide.php';
require_once __DIR__ . '/../Contract/ITransferGuide.php';
require_once __DIR__ . '/../Helpers/TransferGuideHelper.php';

class TransferGuideRepository extends CommonRepository implements ITransferGuide {
    public function __construct() {
        parent::__construct(TransferGuide::class);
    }

    public function findWithPaginate($filters){
        try{
            $data = null;

            $query = '
                SELECT 
                    movimientos_transito.id_movt,
                    movimientos_transito.action_mov,
                    movimientos_transito.id_alm_ini,
                    movimientos_transito.id_alm_des, 
                    movimientos_transito.observ_mov,
                    movimientos_transito.nro_mov,
                    movimientos_transito.fechaguia_mov,
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
                    transfers_guides.id as transfers_guides_id,
                    transfers_guides.serie as transfers_guides_serie,
                    transfers_guides.number as transfers_guides_number,
                    transfers_guides.date_issue as transfers_guides_date_issue,
                    transfers_guides.time_issue as transfers_guides_time_issue,
                    transfers_guides.observations as transfers_guides_observations,
                    transfers_guides.motive_code as transfers_guides_motive_code,
                    transfers_guides.motive_description as transfers_guides_motive_description,
                    transfers_guides.total_witght as transfers_guides_total_witght,
                    transfers_guides.unit_measure as transfers_guides_unit_measure,
                    transfers_guides.email_principal as transfers_guides_email_principal,
                    transfers_guides.email_secondary as transfers_guides_email_secondary,
                    transfers_guides.total_quantity as transfers_guides_total_quantity,
                    transfers_guides.transport_modality as transfers_guides_transport_modality,
                    transfers_guides.tci_response_code as tci_response_code,
                    transfers_guides.tci_response_type as tci_response_type,
                    transfers_guides.tci_response_description as tci_response_description,
                    transfers_guides.tci_response_date as tci_response_date,
                    transports.modality as transports_modalidad,
                    transports.start_date as transports_fecha_inicio,
                    transports.document_type as transports_tipo_documento,
                    transports.document as transports_documento,
                    transports.company_name as transports_razon_social,
                    transports.mtc_number as transports_numero_mtc
                FROM transfers_guides
                LEFT JOIN movimientos_transito ON movimientos_transito.id_movt = transfers_guides.movement_id
                LEFT JOIN almacen as almacen_ini ON almacen_ini.id_alm = movimientos_transito.id_alm_ini
                LEFT JOIN almacen as almacen_des ON almacen_des.id_alm = movimientos_transito.id_alm_des
                LEFT JOIN companies as company_ini ON company_ini.id = almacen_ini.company_id
                LEFT JOIN companies as company_des ON company_des.id = almacen_des.company_id
                LEFT JOIN document_types as document_types_ini ON document_types_ini.id = company_ini.document_type_id
                LEFT JOIN document_types as document_types_des ON document_types_des.id = company_des.document_type_id
                LEFT JOIN ubigeo as u_alm_ini ON u_alm_ini.id_ubigeo = almacen_ini.distrito_alm
                LEFT JOIN ubigeo as u_alm_des ON u_alm_des.id_ubigeo = almacen_des.distrito_alm
                LEFT JOIN transports ON transports.transfer_guide_id = transfers_guides.id
            ';
            $conditions = '';

            if(count($filters) > 0){
                if(isset($filters['q'])){
                    $conditions .= 'CONCAT(transfers_guides.serie, "-", transfers_guides.number) LIKE "%'.$filters['q'].'%"';
                }

                if(isset($filters['date_from'])){
                    if(strlen($conditions) > 0) $conditions .= ' AND ';
                    $conditions .= 'transfers_guides.date_issue >= "'. $filters['date_from'].'"';
                }

                if(isset($filters['date_to'])){
                    if(strlen($conditions) > 0) $conditions .= ' AND ';
                    $conditions .= 'transfers_guides.date_issue <= "'. $filters['date_to'].'"';
                }

                $conditions = ' WHERE ' . $conditions;
            }

            $result = self::query($query.$conditions);


            // if($result){
            //     if(is_array($result)){
            //         if(count($result) > 0){
            //             $data = TransitMovementHelper::format($result);
            //         }
            //     }
            // }

            return $result;
        }
        catch(Exception $e){
            echo $e->getMessage();
        }
        return null;
    }

    public function findOneWithDetails($id){
        try{
            $data = null;
            $result = self::query(self::getQueryDefault()." 
                WHERE 
                transfers_guides.id = {$id}");

            if($result){
                if(is_array($result)){
                    if(count($result) > 0){
                        $data = TransferGuideHelper::format($result);
                        // $data = $result;
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
                    u_alm_ini.id_ubigeo as u_alm_ini_id_ubigeo,
                    u_alm_ini.nombre_ubigeo as u_alm_ini_nombre_ubigeo,
                    u_alm_ini.codigo_inei as u_alm_ini_codigo_inei,
                    u_alm_des.id_ubigeo as u_alm_des_id_ubigeo,
                    u_alm_des.nombre_ubigeo as u_alm_des_nombre_ubigeo,
                    u_alm_des.codigo_inei as u_alm_des_codigo_inei,
                    inventario.id_inv,
                    inventario.um_inv as inventario_um_inv,
                    inventario.cod_inv as inventario_cod_inv,
                    (
                        SELECT 
                            unidad_medida.sunat_code 
                        FROM unidad_medida 
                        WHERE CONVERT(unidad_medida.cod_um USING utf8) = CONVERT(inventario_um_inv USING utf8) LIMIT 1
                    ) as um_sunat_code,
                    transfers_guides.id as transfers_guides_id,
                    transfers_guides.serie as transfers_guides_serie,
                    transfers_guides.number as transfers_guides_number,
                    transfers_guides.date_issue as transfers_guides_date_issue,
                    transfers_guides.time_issue as transfers_guides_time_issue,
                    transfers_guides.observations as transfers_guides_observations,
                    transfers_guides.motive_code as transfers_guides_motive_code,
                    transfers_guides.motive_description as transfers_guides_motive_description,
                    transfers_guides.total_witght as transfers_guides_total_witght,
                    transfers_guides.unit_measure as transfers_guides_unit_measure,
                    transfers_guides.email_principal as transfers_guides_email_principal,
                    transfers_guides.email_secondary as transfers_guides_email_secondary,
                    transfers_guides.total_quantity as transfers_guides_total_quantity,
                    transfers_guides.transport_modality as transfers_guides_transport_modality,
                    transfers_guides.tci_response_code as tci_response_code,
                    transfers_guides.tci_response_type as tci_response_type,
                    transfers_guides.tci_response_description as tci_response_description,
                    transfers_guides.tci_response_date as tci_response_date,
                    transfer_guide_detail.additional_description as additional_description,
                    transports.id as transports_id,
                    transports.modality as transports_modalidad,
                    transports.start_date as transports_fecha_inicio,
                    transports.document_type_code as transports_tipo_documento,
                    transports.document as transports_documento,
                    transports.company_name as transports_razon_social,
                    transports.mtc_number as transports_numero_mtc,
                    transports.license as transports_license,
                    transports.name as transports_name,
                    transports.last_name as transports_last_name,
                    vehicles.id as vehicles_id,
                    vehicles.plate as vehicles_plate
                FROM transfers_guides
                INNER JOIN transfer_guide_detail ON transfer_guide_detail.transfer_guide_id = transfers_guides.id
                INNER JOIN movimientos_transito_detalle ON movimientos_transito_detalle.id_mtde = transfer_guide_detail.movement_detail_id
                INNER JOIN movimientos_transito ON movimientos_transito.id_movt = movimientos_transito_detalle.id_movt
                LEFT JOIN almacen as almacen_ini ON almacen_ini.id_alm = movimientos_transito.id_alm_ini
                LEFT JOIN almacen as almacen_des ON almacen_des.id_alm = movimientos_transito.id_alm_des
                LEFT JOIN companies as company_ini ON company_ini.id = almacen_ini.company_id
                LEFT JOIN companies as company_des ON company_des.id = almacen_des.company_id
                LEFT JOIN document_types as document_types_ini ON document_types_ini.id = company_ini.document_type_id
                LEFT JOIN document_types as document_types_des ON document_types_des.id = company_des.document_type_id
                LEFT JOIN ubigeo as u_alm_ini ON u_alm_ini.id_ubigeo = almacen_ini.distrito_alm
                LEFT JOIN ubigeo as u_alm_des ON u_alm_des.id_ubigeo = almacen_des.distrito_alm
                LEFT JOIN inventario ON inventario.id_inv = movimientos_transito_detalle.id_inv
                LEFT JOIN transports ON transports.transfer_guide_id = transfers_guides.id
                LEFT JOIN vehicles ON vehicles.transfer_guide_id = transfers_guides.id
        ';

        return $query;
    }
}