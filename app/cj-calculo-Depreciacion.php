<?php
date_default_timezone_set("America/Lima");
setlocale(LC_TIME, 'es_PE.UTF-8');
require_once '../model/InventarioModel.php';
require_once '../model/FuncionesModel.php';

try {
    $obj_fn = new FuncionesModel();
    $obj_inv = new InventarioModel();
    $lstActivos = $obj_inv->lista_Depreciacion_Activo_xAll();

    $fechActual = date("Y-m-d");

    if (!is_null($lstActivos)) {

        foreach ($lstActivos as $activo) {

            if(!is_null($activo['fechadepre_inv']) && trim($activo['fechadepre_inv']) != "0000-00-00" && (int)$activo['costo_act_inv'] > 0 && (float)$activo['frec_depre_act_inv']> 0 && (int)$activo['val_depre_mensual_inv'] > 0) {

                $fechaProximaDepreciacion = $obj_fn->sumar_meses_fecha($activo['fechadepre_inv'], 1);

                if(strtotime($fechActual) == strtotime($fechaProximaDepreciacion)){
                    $newCostoActivo = (float)$activo['costo_act_inv'] - (float)$activo['val_depre_mensual_inv'];
                    $newFrecDepre = (int)$activo['frec_depre_act_inv'] - 1;

                    $datesUpdate[0] = $activo['id_inv'];
                    $datesUpdate[1] = $fechaProximaDepreciacion;
                    $datesUpdate[2] = $newCostoActivo;
                    $datesUpdate[3] = $newFrecDepre;
                    $obj_inv->actualizar_Depreciacion_xIdInventario($datesUpdate);
                }
            }
        }
    }
}
catch (PDOException $e) {
    throw $e;
}


