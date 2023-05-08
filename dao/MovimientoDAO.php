<?php
require_once '../ds/AccesoDB.php';

class MovimientoDAO{

    public function registrar_Movimiento_Item_lastID($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
            $query = "INSERT INTO movimientos(action_mov,id_alm_ini,und_mov,om_mov,id_alm_des,solicitado_mov,recibido_mov,autorizado_mov,observ_mov,documento_mov,motivo_mov,fecha_mov,nro_mov,id_us,fechareg_mov,entregado_mov,nrovale_mov,id_mov_ref,areaoperativa_mov)
                      VALUES (:accion,:idalmini,:unidad,:omantto,:idalmdes,:solicitado,:recibido,:autorizado,:observ,:documento,:motivo,:fechamov,:nromov,:idus,:fechareg,:entregado,:nrovale,:idmovref,:areaoperativa)";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":accion",$datos[0] );
            $stm->bindParam(":idalmini",$datos[1]);
            $stm->bindParam(":unidad",$datos[2]);
            $stm->bindParam(":omantto",$datos[3]);
            $stm->bindParam(":idalmdes",$datos[4], PDO::PARAM_INT);
            $stm->bindParam(":solicitado",$datos[5]);
            $stm->bindParam(":recibido",$datos[6]);
            $stm->bindParam(":autorizado",$datos[7]);
            $stm->bindParam(":observ",$datos[8]);
            $stm->bindParam(":documento",$datos[9]);
            $stm->bindParam(":motivo",$datos[10]);
            $stm->bindParam(":fechamov",$datos[11]);
            $stm->bindParam(":nromov",$datos[12]);
            $stm->bindParam(":idus",$datos[13], PDO::PARAM_INT);
            $stm->bindParam(":fechareg",$datos[14]);
            $stm->bindParam(":entregado",$datos[15]);
            $stm->bindParam(":nrovale",$datos[16]);
            $stm->bindParam(":idmovref",$datos[17], PDO::PARAM_INT);
            $stm->bindParam(":areaoperativa",$datos[18]);
            $stm->execute();
            $id = $pdo->lastInsertId();
            return $id;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_Movimiento_Item_Detalle($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "INSERT INTO movimientos_detalle(id_mov,id_inv,cod_mde,des_mde,nparte_mde,cant_mde,stock_mde,id_cla,um_mde,marca_mde,cactivo_mde,cinventario_mde,cmapel_mde,conu_mde)
                      VALUES (:idmov,:idinv,:cod,:des,:nparte,:cant,:stockm,:clasificacion,:umedida,:marca,:cactivo,:cinventario,:cmapel,:conu)";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idmov",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":idinv",$datos[1], PDO::PARAM_INT);
            $stm->bindParam(":cod",$datos[2]);
            $stm->bindParam(":des",$datos[3]);
            $stm->bindParam(":nparte",$datos[4]);
            $stm->bindParam(":cant",$datos[5]);
            $stm->bindParam(":stockm",$datos[6]);
            $stm->bindParam(":clasificacion",$datos[7], PDO::PARAM_INT);
            $stm->bindParam(":umedida",$datos[8]);
            $stm->bindParam(":marca",$datos[9]);
            $stm->bindParam(":cactivo",$datos[10]);
            $stm->bindParam(":cinventario",$datos[11]);
            $stm->bindParam(":cmapel",$datos[12]);
            $stm->bindParam(":conu",$datos[13]);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_MovimientoTransito_Item_lastID($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
            $query = "INSERT INTO movimientos_transito(action_mov,id_alm_ini,und_mov,om_mov,id_alm_des,solicitado_mov,recibido_mov,autorizado_mov,observ_mov,documento_mov,
                        motivo_mov,fecha_mov,nro_mov,id_us,fechareg_mov,entregado_mov,nrovale_mov,id_mov_ref,motivotransfer_mov,fechaguia_mov,
                        nroguia_mov,timellegada_mov,persona_mov,persona1_mov)
                      VALUES (:accion,:idalmini,:unidad,:omantto,:idalmdes,:solicitado,:recibido,:autorizado,:observ,:documento,:motivo,:fechamov,:nromov,
                              :idus,:fechareg,:entregado,:nrovale,:idmovref,:motivotransfer,:fechaguia,:nroguia,:timellegada,:persona1,:persona2)";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":accion",$datos[0], PDO::PARAM_STR);
            $stm->bindParam(":idalmini",$datos[1], PDO::PARAM_INT);
            $stm->bindParam(":unidad",$datos[2], PDO::PARAM_STR);
            $stm->bindParam(":omantto",$datos[3], PDO::PARAM_STR);
            $stm->bindParam(":idalmdes",$datos[4], PDO::PARAM_INT);
            $stm->bindParam(":solicitado",$datos[5], PDO::PARAM_STR);
            $stm->bindParam(":recibido",$datos[6], PDO::PARAM_STR);
            $stm->bindParam(":autorizado",$datos[7], PDO::PARAM_STR);
            $stm->bindParam(":observ",$datos[8], PDO::PARAM_STR);
            $stm->bindParam(":documento",$datos[9], PDO::PARAM_STR);
            $stm->bindParam(":motivo",$datos[10], PDO::PARAM_STR);
            $stm->bindParam(":fechamov",$datos[11], PDO::PARAM_STR);
            $stm->bindParam(":nromov",$datos[12], PDO::PARAM_STR);
            $stm->bindParam(":idus",$datos[13], PDO::PARAM_INT);
            $stm->bindParam(":fechareg",$datos[14], PDO::PARAM_STR);
            $stm->bindParam(":entregado",$datos[15]);
            $stm->bindParam(":nrovale",$datos[16]);
            $stm->bindParam(":idmovref",$datos[17],PDO::PARAM_INT);
            $stm->bindParam(":motivotransfer",$datos[18]);
            $stm->bindParam(":fechaguia",$datos[19]);
            $stm->bindParam(":nroguia",$datos[20]);
            $stm->bindParam(":timellegada",$datos[21],PDO::PARAM_INT);
            $stm->bindParam(":persona1",$datos[22]);
            $stm->bindParam(":persona2",$datos[23]);
            $stm->execute();
            $id = $pdo->lastInsertId();
            return $id;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_MovimientoTransito_Item_Detalle($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "INSERT INTO movimientos_transito_detalle(id_movt,id_inv,cod_mde,des_mde,nparte_mde,cant_mde,stock_mde,id_cla,fechaultcalibra_mde,freccalibra_mde,cactivo_mde,cinventario_mde,cmapel_mde,conu_mde)
                      VALUES (:idmov,:idinv,:cod,:des,:nparte,:cant,:stockm,:clasificacion,:fechaultcalibra,:freccalibra,:cactivo,:cinventario,:cmapel,:conu)";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idmov",$datos[0],PDO::PARAM_INT);
            $stm->bindParam(":idinv",$datos[1],PDO::PARAM_INT);
            $stm->bindParam(":cod",$datos[2]);
            $stm->bindParam(":des",$datos[3]);
            $stm->bindParam(":nparte",$datos[4]);
            $stm->bindParam(":cant",$datos[5]);
            $stm->bindParam(":stockm",$datos[6]);
            $stm->bindParam(":clasificacion",$datos[7], PDO::PARAM_INT);
            $stm->bindParam(":fechaultcalibra",$datos[8]);
            $stm->bindParam(":freccalibra",$datos[9], PDO::PARAM_INT);
            $stm->bindParam(":cactivo",$datos[10]);
            $stm->bindParam(":cinventario",$datos[11]);
            $stm->bindParam(":cmapel",$datos[12]);
            $stm->bindParam(":conu",$datos[13]);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function listar_Movimientos_xAlmacen($where){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM movimientos  WHERE ".$where." ORDER BY fecha_mov DESC";
            $stm = $pdo->prepare($query);
            $stm->execute();
            $lista = $stm->fetchAll(PDO::FETCH_ASSOC);
            if(!$lista){$lista = null;}
            $stm = null;
            return $lista;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function listar_Movimientos_xAlmacen_TRAExterno($where){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM movimientos_transito WHERE ".$where." ORDER BY fecha_mov DESC";
            $stm = $pdo->prepare($query);
            $stm->execute();
            $lista = $stm->fetchAll(PDO::FETCH_ASSOC);
            if(!$lista){$lista = null;}
            $stm = null;
            return $lista;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function detalle_Movimiento_xVale($datos){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM movimientos WHERE id_alm_ini = :idalm AND  nrovale_mov = :nrovale";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idalm",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":nrovale",$datos[1]);
            $stm->execute();
            $lista = $stm->fetch(PDO::FETCH_ASSOC);
            if(!$lista){$lista = null;}
            $stm = null;
            return $lista;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function detalle_Movimiento_xID($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM movimientos WHERE id_mov = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$id, PDO::PARAM_INT);
            $stm->execute();
            $detalle = $stm->fetch(PDO::FETCH_ASSOC);
            if(!$detalle){$detalle = null;}
            $stm = null;
            return $detalle;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function lista_MovimientoDetalle_xIdMovimiento($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM movimientos_detalle WHERE id_mov = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$id, PDO::PARAM_INT);
            $stm->execute();
            $lista = $stm->fetchAll(PDO::FETCH_ASSOC);
            if(!$lista){$lista = null;}
            $stm = null;
            return $lista;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function detalle_MovimientoTransito_xID($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM movimientos_transito WHERE id_movt = :id LIMIT 1";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$id, PDO::PARAM_INT);
            $stm->execute();
            $detalle = $stm->fetch(PDO::FETCH_ASSOC);
            if(!$detalle){$detalle = null;}
            $stm = null;
            return $detalle;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function lista_MovimientoTransitoDetalle_xIdMovimiento($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM movimientos_transito_detalle WHERE id_movt = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$id, PDO::PARAM_INT);
            $stm->execute();
            $lista = $stm->fetchAll(PDO::FETCH_ASSOC);
            if(!$lista){$lista = null;}
            $stm = null;
            return $lista;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function lista_MovimientoDetalle_xIdInventario($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM movimientos_detalle WHERE id_inv = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$id, PDO::PARAM_INT);
            $stm->execute();
            $lista = $stm->fetchAll(PDO::FETCH_ASSOC);
            if(!$lista){$lista = null;}
            $stm = null;
            return $lista;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function existe_MovimientoDetalle_xIdInventario($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT count(*) as nreg 
                      FROM movimientos mov INNER JOIN movimientos_detalle mvd ON mov.id_mov = mvd.id_mov
                      WHERE mvd.id_inv = :id AND mov.action_mov != 'IN' LIMIT 1";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$id, PDO::PARAM_INT);
            $stm->execute();
            $detalle = $stm->fetch(PDO::FETCH_ASSOC);
            if(!$detalle){$detalle = null;}
            $stm = null;
            return $detalle;
        } catch(PDOException $e){
            throw $e;
        }
    }


    public function lista_MovimientosPT_cmpRecibido_xIdInventario($idAlm,$idinv){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT mov.id_mov, mov.recibido_mov, mov.solicitado_mov, mov.action_mov 
                      FROM movimientos mov INNER JOIN movimientos_detalle mvd ON mov.id_mov = mvd.id_mov
                      WHERE mvd.id_inv = :idInv AND mov.id_alm_ini = :idAlm AND (mov.action_mov = 'PT' OR mov.action_mov = 'SO') 
                      ORDER BY mov.id_mov DESC LIMIT 1";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idAlm",$idAlm, PDO::PARAM_INT);
            $stm->bindParam(":idInv",$idinv, PDO::PARAM_INT);
            $stm->execute();
            $detalle = $stm->fetch(PDO::FETCH_ASSOC);
            if(!$detalle){$detalle = null;}
            $stm = null;
            return $detalle;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function lista_Inventario_status_Calibracion_xMovimientos($idAlm,$idinv){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT mov.*,mvd.*
                      FROM movimientos mov INNER JOIN movimientos_detalle mvd ON mov.id_mov = mvd.id_mov
                      WHERE mov.id_alm_ini = :idAlm AND (mov.id_alm_ini = mov.id_alm_des) AND (mov.action_mov = 'PT' OR mov.action_mov = 'SO' OR mov.action_mov = 'DV' OR mov.action_mov = 'DC') AND mvd.id_inv = :idInv  
                      ORDER BY mov.id_mov DESC LIMIT 1";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idAlm",$idAlm, PDO::PARAM_INT);
            $stm->bindParam(":idInv",$idinv, PDO::PARAM_INT);
            $stm->execute();
            $detalle = $stm->fetch(PDO::FETCH_ASSOC);
            if(!$detalle){$detalle = null;}
            $stm = null;
            return $detalle;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function lista_Inventario_status_Calibracion_xMovimientosTRANSITO($idAlm,$idinv){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT mov.*,mvd.*
                        FROM movimientos_transito mov INNER JOIN movimientos_transito_detalle mvd ON mov.id_movt = mvd.id_movt
                        WHERE mov.id_alm_ini = :idAlm AND mov.action_mov = 'TRA' AND mov.estd_transito ='T' AND  mvd.id_inv = :idInv  
                        ORDER BY mov.id_movt DESC LIMIT 1";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idAlm",$idAlm, PDO::PARAM_INT);
            $stm->bindParam(":idInv",$idinv, PDO::PARAM_INT);
            $stm->execute();
            $detalle = $stm->fetch(PDO::FETCH_ASSOC);
            if(!$detalle){$detalle = null;}
            $stm = null;
            return $detalle;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function lista_Inventario_status_Calibracion_xMovimientosTRANSFER($idAlm,$idinv){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT mov.*,mvd.*
                        FROM movimientos mov INNER JOIN movimientos_detalle mvd ON mov.id_mov = mvd.id_mov
                        WHERE mov.id_alm_ini = :idAlm AND mov.action_mov = 'TRA' AND  mvd.id_inv = :idInv  
                        ORDER BY mov.id_mov DESC LIMIT 1";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idAlm",$idAlm, PDO::PARAM_INT);
            $stm->bindParam(":idInv",$idinv, PDO::PARAM_INT);
            $stm->execute();
            $detalle = $stm->fetch(PDO::FETCH_ASSOC);
            if(!$detalle){$detalle = null;}
            $stm = null;
            return $detalle;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function delete_Movimiento_xID($id) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "DELETE FROM movimientos WHERE id_mov = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$id, PDO::PARAM_INT);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_Movimientos_xNumVale($id,$nvale){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT id_mov FROM movimientos where id_alm_ini = :id and nrovale_mov = :nrovale";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$id, PDO::PARAM_INT);
            $stm->bindParam(":nrovale",$nvale, PDO::PARAM_INT);
            $stm->execute();
            $lista = $stm->fetchAll(PDO::FETCH_ASSOC);
            if(!$lista){$lista = null;}
            $stm = null;
            return $lista;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function cantidad_devuelta_xIDInventario($datos){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT sum(movd.cant_mde) as devuelto FROM movimientos mov
                            INNER JOIN movimientos_detalle movd ON mov.id_mov = movd.id_mov
                       WHERE mov.id_mov_ref = :idMovPadre and id_inv = :idInventario";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idMovPadre",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":idInventario",$datos[1], PDO::PARAM_INT);
            $stm->execute();
            $detalle = $stm->fetch(PDO::FETCH_ASSOC);
            if(!$detalle){$detalle = null;}
            $stm = null;
            return $detalle;
        } catch(PDOException $e){
            throw $e;
        }
    }

}
