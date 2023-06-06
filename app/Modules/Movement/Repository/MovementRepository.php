<?php
require_once __DIR__ . '/../../Common/Repositories/CommonRepository.php';
require_once __DIR__ . '/../../../Models/Movement.php';
require_once __DIR__ . '/../Helpers/MovementHelper.php';
require_once __DIR__ . '/../Contract/IMovement.php';

class MovementRepository extends CommonRepository implements IMovement {
    public function __construct() {
        parent::__construct(Movement::class);
    }

    public function getMovement($id){
        try{
            $data = null;
            $result = self::query('
                SELECT 
                    movimientos.id_mov,
                    movimientos.action_mov,
                    movimientos.id_alm_ini,
                    movimientos.id_alm_des, 
                    movimientos.observ_mov,
                    movimientos.nro_mov,
                    movimientos_detalle.des_mde,
                    movimientos_detalle.um_mde,
                    movimientos_detalle.cant_mde,
                    almacen_ini.titulo_alm as almacen_ini_titulo_alm,
                    almacen_ini.direccion_alm as almacen_ini_direccion_alm,
                    almacen_des.titulo_alm as almacen_des_titulo_alm,
                    almacen_des.direccion_alm as almacen_des_direccion_alm,
                    u_alm_ini.id_ubigeo as u_alm_ini_id_ubigeo,
                    u_alm_ini.nombre_ubigeo as u_alm_ini_nombre_ubigeo,
                    u_alm_ini.codigo_inei as u_alm_ini_codigo_inei,
                    u_alm_des.id_ubigeo as u_alm_des_id_ubigeo,
                    u_alm_des.nombre_ubigeo as u_alm_des_nombre_ubigeo,
                    u_alm_des.codigo_inei as u_alm_des_codigo_inei
                FROM movimientos
                INNER JOIN movimientos_detalle ON movimientos_detalle.id_mov = movimientos.id_mov
                INNER JOIN almacen as almacen_ini ON almacen_ini.id_alm = movimientos.id_alm_ini
                INNER JOIN almacen as almacen_des ON almacen_des.id_alm = movimientos.id_alm_des
                INNER JOIN ubigeo as u_alm_ini ON u_alm_ini.id_ubigeo = almacen_ini.distrito_alm
                INNER JOIN ubigeo as u_alm_des ON u_alm_des.id_ubigeo = almacen_des.distrito_alm
                WHERE 
                    movimientos.id_mov = '.$id.'
            ');

            if($result){
                if(is_array($result)){
                    if(count($result) > 0){
                        $data = MovementHelper::format($result);
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