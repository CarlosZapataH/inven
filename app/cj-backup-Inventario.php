<?php
date_default_timezone_set("America/Lima");
setlocale(LC_TIME, 'es_PE.UTF-8');
require_once '../model/AlmacenModel.php';
require_once '../model/InventarioModel.php';
try {
    $obj_alm = new AlmacenModel();
    $lstAlmacren = $obj_alm->lst_Almacenes_All_Activos();
    if (is_array($lstAlmacren)) {
        foreach ($lstAlmacren as $almacen) {
            $datesBkp[0] = $almacen['id_alm'];
            $datesBkp[1] = $almacen['id_serv'];
            $datesBkp[2] = date("Y");
            $datesBkp[3] = date("m");
            $datesBkp[4] = date("Y-m-d");
            $datesBkp[5] = date("Y-m-d H:i:s");
            $insertIDBK = $obj_alm->registrar_Almacen_Backup_lastID($datesBkp);
            if ((int)$insertIDBK > 0) {
                $obj_inv = new InventarioModel();
                $lstInventario = $obj_inv->lista_inventario_xIdAlmacen($almacen['id_alm']);
                if (!is_null($lstInventario)) {
                    foreach ($lstInventario as $inventario) {
                        $datesINV[0] = $insertIDBK;
                        $datesINV[1] = $inventario['und_inv'];
                        $datesINV[2] = $inventario['cod_inv'];
                        $datesINV[3] = $inventario['cant_inv'];
                        $datesINV[4] = $inventario['des_inv'];
                        $datesINV[5] = $inventario['um_inv'];
                        $datesINV[6] = $inventario['ubic_inv'];
                        $datesINV[7] = $inventario['nroparte_inv'];
                        $datesINV[8] = $inventario['reserva_inv'];
                        $datesINV[9] = $inventario['om_inv'];
                        $datesINV[10] = $inventario['fechapedido_inv'];
                        $datesINV[11] = $inventario['fecharec_inv'];
                        $datesINV[12] = $inventario['marca_inv'];
                        $datesINV[13] = $inventario['cunit_inv'];
                        $datesINV[14] = $inventario['total_inv'];
                        $datesINV[15] = $inventario['fechains_inv'];
                        $datesINV[16] = $inventario['mecanico_inv'];
                        $datesINV[17] = $inventario['observ_inv'];
                        $datesINV[18] = $inventario['id_us'];
                        $datesINV[19] = $inventario['fechareg_inv'];
                        $datesINV[20] = $inventario['ordencompra_inv'];
                        $datesINV[21] = $inventario['numerovale_inv'];
                        $datesINV[22] = $inventario['fecharecep_inv'];
                        $datesINV[23] = $inventario['itempedido_inv'];
                        $obj_inv->registrar_inventario_backup($datesINV);
                    }
                }
            }

        }
    }
}
catch (PDOException $e) {
    throw $e;
}


