<?php
require_once '../ds/AccesoDB.php';

class InventarioDAO{

    public function listar_Inventario_xIDAlmacen_All($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM inventario WHERE id_alm = :id AND cant_inv > 0 AND condicion_inv = 1";
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

    public function nRegistros_Inventario_xAlmacen_All($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT count(*) as ncount FROM inventario WHERE id_alm = :id AND cant_inv > 0 AND condicion_inv = 1 LIMIT 1";
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

    public function lst_ubicacion_Inventario_xIdAlm($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT ubic_inv FROM inventario WHERE id_alm = :id AND condicion_inv = 1 GROUP BY ubic_inv ORDER BY ubic_inv ASC";
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

    public function busca_existencia_Item_xDatos($datos){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM inventario WHERE id_alm = :id AND cod_inv = :codMaterial AND nroparte_inv = :nroParte AND cactivo_inv = :codActivo AND cant_inv > 0 AND condicion_inv = 1 LIMIT 1";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":codMaterial",$datos[1]);
            $stm->bindParam(":nroParte",$datos[2]);
            $stm->bindParam(":codActivo",$datos[3]);
            $stm->execute();
            $detalle = $stm->fetch(PDO::FETCH_ASSOC);
            if(!$detalle){$detalle = null;}
            $stm = null;
            return $detalle;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function busca_existencia_Item_xCodMaterial($datos){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM inventario WHERE id_alm = :id AND cod_inv = :codMaterial AND condicion_inv = 1 LIMIT 1";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":codMaterial",$datos[1]);
            $stm->execute();
            $detalle = $stm->fetch(PDO::FETCH_ASSOC);
            if(!$detalle){$detalle = null;}
            $stm = null;
            return $detalle;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function busca_existencia_codMaterial_xItem($datos){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM inventario WHERE id_alm = :idAlmacen AND id_inv != :idInventario AND cod_inv = :codMaterial";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idAlmacen",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":idInventario",$datos[1], PDO::PARAM_INT);
            $stm->bindParam(":codMaterial",$datos[2]);
            $stm->execute();
            $lista = $stm->fetchAll(PDO::FETCH_ASSOC);
            if(!$lista){$lista = null;}
            $stm = null;
            return $lista;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function registrar_Item($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "INSERT INTO inventario(id_alm,cod_inv,cant_inv,des_inv,um_inv,nroparte_inv,marca_inv,observ_inv,id_us,fechareg_inv,ordencompra_inv,id_cla,costo_act_inv,frec_depre_act_inv,val_depre_mensual_inv,
                       cactivo_inv,cinventario_inv,cmapel_inv,conu_inv)
                      VALUES (:idalm,:cod,:cant,:des,:um,:nroparte,:marca,:observ,:idus,:freg,:ocomp,:clasification,:costo,:frecuencia,:valmensual,:cactivo,:cinventario,:cmapel,:conu)";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idalm",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":cod",$datos[1]);
            $stm->bindParam(":cant",$datos[2]);
            $stm->bindParam(":des",$datos[3]);
            $stm->bindParam(":um",$datos[4]);
            $stm->bindParam(":nroparte",$datos[5]);
            $stm->bindParam(":marca",$datos[6]);
            $stm->bindParam(":observ",$datos[7]);
            $stm->bindParam(":idus",$datos[8], PDO::PARAM_INT);
            $stm->bindParam(":freg",$datos[9]);
            $stm->bindParam(":ocomp",$datos[10]);
            $stm->bindParam(":clasification",$datos[11], PDO::PARAM_INT);
            $stm->bindParam(":costo",$datos[12], PDO::PARAM_INT);
            $stm->bindParam(":frecuencia",$datos[13], PDO::PARAM_INT);
            $stm->bindParam(":valmensual",$datos[14], PDO::PARAM_INT);
            $stm->bindParam(":cactivo",$datos[15]);
            $stm->bindParam(":cinventario",$datos[16]);
            $stm->bindParam(":cmapel",$datos[17]);
            $stm->bindParam(":conu",$datos[18]);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_Item_calibracion($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "INSERT INTO inventario(id_alm,cod_inv,cant_inv,des_inv,um_inv,nroparte_inv,marca_inv,observ_inv,id_us,fechareg_inv,ordencompra_inv,id_cla,
                       fechaultcalibra_inv, freccalibra_inv,costo_act_inv,frec_depre_act_inv,val_depre_mensual_inv,cactivo_inv,cinventario_inv,cmapel_inv,conu_inv)
                      VALUES (:idalm,:cod,:cant,:des,:um,:nroparte,:marca,:observ,:idus,:freg,:ocomp,:clasification,:fechaultcalibra,:freccalibra,
                              :costo,:frecuencia,:valmensual,:cactivo,:cinventario,:cmapel,:conu)";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idalm",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":cod",$datos[1]);
            $stm->bindParam(":cant",$datos[2]);
            $stm->bindParam(":des",$datos[3]);
            $stm->bindParam(":um",$datos[4]);
            $stm->bindParam(":nroparte",$datos[5]);
            $stm->bindParam(":marca",$datos[6]);
            $stm->bindParam(":observ",$datos[7]);
            $stm->bindParam(":idus",$datos[8], PDO::PARAM_INT);
            $stm->bindParam(":freg",$datos[9]);
            $stm->bindParam(":ocomp",$datos[10]);
            $stm->bindParam(":clasification",$datos[11], PDO::PARAM_INT);
            $stm->bindParam(":costo",$datos[12], PDO::PARAM_INT);
            $stm->bindParam(":frecuencia",$datos[13], PDO::PARAM_INT);
            $stm->bindParam(":valmensual",$datos[14], PDO::PARAM_INT);
            $stm->bindParam(":cactivo",$datos[15]);
            $stm->bindParam(":cinventario",$datos[16]);
            $stm->bindParam(":cmapel",$datos[17]);
            $stm->bindParam(":conu",$datos[18]);
            $stm->bindParam(":fechaultcalibra",$datos[19]);
            $stm->bindParam(":freccalibra",$datos[20], PDO::PARAM_INT);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_Item_lastID($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
            $query = "INSERT INTO inventario(id_alm,cod_inv,cant_inv,des_inv,um_inv,nroparte_inv,
                                             marca_inv,observ_inv,id_us,fechareg_inv,fecharecep_inv,
                                             id_cla,nguia_inv,fechaultcalibra_inv,freccalibra_inv,fechadepre_inv,costo_act_inv,frec_depre_act_inv,
                                             val_depre_mensual_inv,cactivo_inv,cinventario_inv,cmapel_inv,conu_inv)
                      VALUES (:idalm,:codigoMaterial,:cantidad,:desItem,:undMedida,:nroParte,:marca,:observ,
                              :idus,:fregsis,:fechaRecepcion,:IdClasificacion,:nroGuia,:fechaUltcalibra,:frecCalibracion,
                              :fechaDepreciacion,:costoActivo,:frecDepreciacion,:valDepreciacion,:cActivo,:cInventario,:cMapel,:cONU)";
            $stm = $pdo->prepare($query);
            $stm->bindParam("idalm",$datos[0], PDO::PARAM_INT);
            $stm->bindParam("codigoMaterial",$datos[1]);
            $stm->bindParam("cantidad",$datos[2]);
            $stm->bindParam("desItem",$datos[3]);
            $stm->bindParam("undMedida",$datos[4]);
            $stm->bindParam("nroParte",$datos[5]);
            $stm->bindParam("marca",$datos[6]);
            $stm->bindParam("observ",$datos[7]);
            $stm->bindParam("idus",$datos[8], PDO::PARAM_INT);
            $stm->bindParam("fregsis",$datos[9]);
            $stm->bindParam("fechaRecepcion",$datos[10]);
            $stm->bindParam("IdClasificacion",$datos[11], PDO::PARAM_INT);
            $stm->bindParam("nroGuia",$datos[12]);
            $stm->bindParam("fechaUltcalibra",$datos[13]);
            $stm->bindParam("frecCalibracion",$datos[14]);
            $stm->bindParam("fechaDepreciacion",$datos[15]);
            $stm->bindParam("costoActivo",$datos[16]);
            $stm->bindParam("frecDepreciacion",$datos[17], PDO::PARAM_INT);
            $stm->bindParam("valDepreciacion",$datos[18]);
            $stm->bindParam("cActivo",$datos[19]);
            $stm->bindParam("cInventario",$datos[20]);
            $stm->bindParam("cMapel",$datos[21]);
            $stm->bindParam("cONU",$datos[22]);
            $stm->execute();
            $id = $pdo->lastInsertId();
            return $id;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function detalle_Item_xID($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM inventario WHERE id_inv = :id";
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

    public function update_Item($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "UPDATE inventario SET 
                      id_cla = :idClasificacion,cod_inv = :cod, cant_inv = :cant, des_inv = :des, um_inv = :um, ubic_inv = :ubic,
                      nroparte_inv = :nroparte,fechadepre_inv = :fechadepre, costo_act_inv = :costoactivo, frec_depre_act_inv = :frecdepre, 
                      val_depre_mensual_inv = :valmensual, nguia_inv = :nroguia,ordencompra_inv = :ocompra,marca_inv = :marca,
                      cactivo_inv = :cactivo,cinventario_inv = :cinvent,cmapel_inv = :cmapel,conu_inv = :conu,
                      fechaultcalibra_inv = :fechaultcalibra, freccalibra_inv = :frecCalibra,fecharecep_inv = :fecharecep,observ_inv = :observ
                      WHERE id_inv = :idinv";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idinv",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":idClasificacion",$datos[1],PDO::PARAM_INT);
            $stm->bindParam(":cod",$datos[2]);
            $stm->bindParam(":cant",$datos[3]);
            $stm->bindParam(":des",$datos[4]);
            $stm->bindParam(":um",$datos[5]);
            $stm->bindParam(":ubic",$datos[6]);
            $stm->bindParam(":nroparte",$datos[7]);
            $stm->bindParam(":fechadepre",$datos[8]);
            $stm->bindParam(":costoactivo",$datos[9]);
            $stm->bindParam(":frecdepre",$datos[10],PDO::PARAM_INT);
            $stm->bindParam(":valmensual",$datos[11]);
            $stm->bindParam(":nroguia",$datos[12]);
            $stm->bindParam(":ocompra",$datos[13]);
            $stm->bindParam(":marca",$datos[14]);
            $stm->bindParam(":cactivo",$datos[15]);
            $stm->bindParam(":cinvent",$datos[16]);
            $stm->bindParam(":cmapel",$datos[17]);
            $stm->bindParam(":conu",$datos[18]);
            $stm->bindParam(":fechaultcalibra",$datos[19]);
            $stm->bindParam(":frecCalibra",$datos[20]);
            $stm->bindParam(":fecharecep",$datos[21]);
            $stm->bindParam(":observ",$datos[22]);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_log_Actualizacion_Inventario($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
            $pdo->beginTransaction();
            $query = "INSERT INTO log_change_inventario (id_inv,campo_lcv,vanterior_lcv,vchange_lcv,persona_lcv,id_us,fechareg_lav)
                      VALUES (:idinv,:campo,:vanterior,:vchange,:persona,:idus,:fechareg)";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idinv",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":campo",$datos[1], PDO::PARAM_STR                                                                                                                                                          );
            $stm->bindParam(":vanterior",$datos[2], PDO::PARAM_STR);
            $stm->bindParam(":vchange",$datos[3], PDO::PARAM_STR);
            $stm->bindParam(":persona",$datos[4], PDO::PARAM_STR);
            $stm->bindParam(":idus",$datos[5], PDO::PARAM_INT);
            $stm->bindParam(":fechareg",$datos[6], PDO::PARAM_STR);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_log_delete_Inventario($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
            $pdo->beginTransaction();
            $query = "INSERT INTO log_delete_inventario (id_inv,id_alm,cod_ldi,des_ldi,um_ldi,om_ldi,id_us,persona_ldi,fechareg_ldi)
                      VALUES (:idinv,:idalm,:cod,:des,:umedida,:omantto,:idus,:persona,:fechareg)";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idinv",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":idalm",$datos[1], PDO::PARAM_INT);
            $stm->bindParam(":cod",$datos[2], PDO::PARAM_STR);
            $stm->bindParam(":des",$datos[3], PDO::PARAM_STR);
            $stm->bindParam(":umedida",$datos[4], PDO::PARAM_STR);
            $stm->bindParam(":omantto",$datos[5], PDO::PARAM_STR);
            $stm->bindParam(":idus",$datos[6], PDO::PARAM_INT);
            $stm->bindParam(":persona",$datos[7], PDO::PARAM_STR);
            $stm->bindParam(":fechareg",$datos[8], PDO::PARAM_STR);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function delete_Inventario_xID($id) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "DELETE FROM inventario WHERE id_inv = :id";
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

    public function update_Condicion_Item($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "UPDATE inventario SET condicion_inv = :condic WHERE id_inv = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":condic",$datos[1], PDO::PARAM_INT);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_inventario_xIdAlmacen($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM inventario WHERE id_alm = :id  AND cant_inv > 0 AND condicion_inv = 1 ORDER BY cod_inv, des_inv ASC";
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

    public function actualizar_Stock_Item($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "UPDATE inventario SET cant_inv = :cant WHERE id_inv = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":cant",$datos[1]);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function listar_Inventario_xAlmacen_Rpte($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM inventario WHERE cant_inv > 0  AND id_alm = :id AND condicion_inv = 1";
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

    public function cantidad_Repuesto_Inventario_xAlmacen_OM($id,$om){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT sum(cant_inv) as cantidad FROM inventario where id_alm = :$id AND om_inv = :$om AND condicion_inv = 1 AND cant_inv > 0";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$id, PDO::PARAM_INT);
            $stm->bindParam(":om",$om);
            $stm->execute();
            $detalle = $stm->fetch(PDO::FETCH_ASSOC);
            if(!$detalle){$detalle = null;}
            $stm = null;
            return $detalle;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function listar_Inventario_xCorte_Rpte($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM inventario_bk WHERE  cant_inv > 0  AND id_bai = :id";
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

    public function cantidad_Repuesto_InventarioBK_xAlmacen_OM($id,$om){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT sum(ibk.cant_inv) as cantidad FROM backup_almacen ba INNER JOIN inventario_bk ibk ON ba.id_bai = ibk.id_bai WHERE ba.id_alm = :id AND ibk.om_inv = :om AND ibk.cant_inv > 0";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$id, PDO::PARAM_INT);
            $stm->bindParam(":om",$om);
            $stm->execute();
            $detalle = $stm->fetch(PDO::FETCH_ASSOC);
            if(!$detalle){$detalle = null;}
            $stm = null;
            return $detalle;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function listar_cortes_Inventario_backup_xAlmacen($datos){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM backup_almacen WHERE id_alm = :idalm AND anio_bai = :periodo AND mes_bai = :mes ORDER BY fecha_bai DESC";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idalm",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":periodo",$datos[1], PDO::PARAM_INT);
            $stm->bindParam(":mes",$datos[2], PDO::PARAM_INT);
            $stm->execute();
            $lista = $stm->fetchAll(PDO::FETCH_ASSOC);
            if(!$lista){$lista = null;}
            $stm = null;
            return $lista;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function registrar_inventario_backup($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
            $pdo->beginTransaction();
            $query = "INSERT INTO inventario_bk(id_bai,und_inv,cod_inv,cant_inv,des_inv,um_inv,ubic_inv,nroparte_inv,reserva_inv,om_inv,fechapedido_inv,fecharec_inv,marca_inv,
                                                cunit_inv,total_inv,fechains_inv,mecanico_inv,observ_inv,id_us,fechareg_inv,ordencompra_inv,numerovale_inv,fecharecep_inv,itempedido_inv)
                      VALUES (:idbai,:und,:cod,:cant,:des,:um,:ubic,:nroparte,:reserva,:omantto,:fpedido,:frec,:marca,:cunit,:total,:fins,:mec,:observ,:idus,:freg,:ocomp,
                              ,:numerovale,:fecharecep,:itempedido)";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idbai",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":und",$datos[1], PDO::PARAM_STR);
            $stm->bindParam(":cod",$datos[2], PDO::PARAM_STR);
            $stm->bindParam(":cant",$datos[3], PDO::PARAM_STR);
            $stm->bindParam(":des",$datos[4], PDO::PARAM_STR);
            $stm->bindParam(":um",$datos[5], PDO::PARAM_STR);
            $stm->bindParam(":ubic",$datos[6], PDO::PARAM_STR);
            $stm->bindParam(":nroparte",$datos[7], PDO::PARAM_STR);
            $stm->bindParam(":reserva",$datos[8], PDO::PARAM_STR);
            $stm->bindParam(":omantto",$datos[9], PDO::PARAM_STR);
            $stm->bindParam(":fpedido",$datos[10], PDO::PARAM_STR);
            $stm->bindParam(":frec",$datos[11], PDO::PARAM_STR);
            $stm->bindParam(":marca",$datos[12], PDO::PARAM_STR);
            $stm->bindParam(":cunit",$datos[13], PDO::PARAM_STR);
            $stm->bindParam(":total",$datos[14], PDO::PARAM_STR);
            $stm->bindParam(":fins",$datos[15], PDO::PARAM_STR);
            $stm->bindParam(":mec",$datos[16], PDO::PARAM_STR);
            $stm->bindParam(":observ",$datos[17], PDO::PARAM_STR);
            $stm->bindParam(":idus",$datos[18], PDO::PARAM_INT);
            $stm->bindParam(":freg",$datos[19], PDO::PARAM_STR);
            $stm->bindParam(":ocomp",$datos[20]);
            $stm->bindParam(":numerovale",$datos[21]);
            $stm->bindParam(":fecharecep",$datos[22]);
            $stm->bindParam(":itempedido",$datos[23], PDO::PARAM_INT);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_inventario_xIdMovimiento($id){
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

    public function lista_Clasificacion_Activos_All(){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM clasificacion WHERE condicion_cla = 1 ORDER BY des_cla ASC";
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

    public function detalle_Clasificacion_xID($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM clasificacion WHERE id_cla = :id";
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

    public function lista_Transitos_Ingresos_Activos_xAlmacen($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM movimientos_transito WHERE estd_transito = 'T' AND id_alm_des = :id";
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

    public function lista_Transitos_Salida_Activos_xAlmacen($de,$a){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM movimientos_transito WHERE id_alm_ini = :almacenInicio AND id_alm_des = :almacenFin ORDER BY estd_transito DESC";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":almacenInicio",$de, PDO::PARAM_INT);
            $stm->bindParam(":almacenFin",$a, PDO::PARAM_INT);
            $stm->execute();
            $lista = $stm->fetchAll(PDO::FETCH_ASSOC);
            if(!$lista){$lista = null;}
            $stm = null;
            return $lista;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function detalle_Transferencia_Transito_xID($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM movimientos_transito WHERE id_movt = :id";
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

    public function lista_Transitos_Detalle_xAlmacen($id){
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

    public function actualizar_Transferencia_Transito_Estado_xID($datos){
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "UPDATE movimientos_transito SET estd_transito = :estado, fechaingreso_mov = :fechaIng, nroguiallegada_mov = :nroguiaIng WHERE id_movt = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":estado",$datos[1]);
            $stm->bindParam(":fechaIng",$datos[2]);
            $stm->bindParam(":nroguiaIng",$datos[3]);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_inventarios_xCalibrar_xIdAlmacen($id){
        try{
            $pdo = AccesoDB::getPDO();/*AND cant_inv > 0*/
            $query = "SELECT * 
                FROM inventario 
                WHERE id_alm = :id AND fechaultcalibra_inv is not null AND freccalibra_inv > 0  AND condicion_inv = 1";
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

    public function registrar_newFecha_Calibracion($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "INSERT INTO inventario_calibracion(id_inv,cod_ca,des_ca,tipo_ca,nguia_ca,fecha_ant_ca,fecha_new_ca,fechareg_ca,file_ca,id_us,name_ca,id_alm,des_alm)
                      VALUES (:idinv,:codigo,:descrip,:tipocla,:nroguia,:fechaAntCal,:fechaActCal,:fechaRegCal,:fileName,:idus,:persona,:idalm,:desAlm)";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idinv",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":codigo",$datos[1]);
            $stm->bindParam(":descrip",$datos[2]);
            $stm->bindParam(":tipocla",$datos[3]);
            $stm->bindParam(":nroguia",$datos[4]);
            $stm->bindParam(":fechaAntCal",$datos[5]);
            $stm->bindParam(":fechaActCal",$datos[6]);
            $stm->bindParam(":fechaRegCal",$datos[7]);
            $stm->bindParam(":fileName",$datos[8]);
            $stm->bindParam(":idus",$datos[9], PDO::PARAM_INT);
            $stm->bindParam(":persona",$datos[10]);
            $stm->bindParam(":idalm",$datos[11], PDO::PARAM_INT);
            $stm->bindParam(":desAlm",$datos[12]);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function actualizar_fechaCalibracion_Inventario_xID($datos){
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "UPDATE inventario SET fechaultcalibra_inv = :fechaActCal WHERE id_inv = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":fechaActCal",$datos[1]);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_Calibraciones_xIdInventario($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM inventario_calibracion WHERE id_inv = :id ORDER BY fecha_new_ca DESC ";
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

    public function registrar_Inventario_Baja_xID($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "INSERT INTO inventario_baja(id_inv,cod_inb,des_inb,tipo_inb,textbaja_inb,fechareg_inb,file_inb,id_us,persona_us,id_alm,des_alm)

                      VALUES (:idinv,:codigo,:descrip,:tipocla,:textbaja,:fechaRegCal,:fileName,:idus,:persona,:idalm,:desAlm)";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idinv",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":codigo",$datos[1]);
            $stm->bindParam(":descrip",$datos[2]);
            $stm->bindParam(":tipocla",$datos[3]);
            $stm->bindParam(":textbaja",$datos[4]);
            $stm->bindParam(":fechaRegCal",$datos[5]);
            $stm->bindParam(":fileName",$datos[6]);
            $stm->bindParam(":idus",$datos[7], PDO::PARAM_INT);
            $stm->bindParam(":persona",$datos[8]);
            $stm->bindParam(":idalm",$datos[9], PDO::PARAM_INT);
            $stm->bindParam(":desAlm",$datos[10]);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function actualizar_Estado_Inventario_xID($datos){
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "UPDATE inventario SET condicion_inv = :estado WHERE id_inv = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":estado",$datos[1], PDO::PARAM_INT);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_Items_xBaja_xIdAlmacen($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM inventario_baja WHERE id_alm = :id";
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

    public function lista_Depreciacion_Activo_xIdAlmacen($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM inventario WHERE id_alm = :id AND id_cla = 1 AND condicion_inv = 1";
            /*$query = "SELECT * FROM inventario WHERE id_alm = :id AND id_cla = 1 AND cant_inv > 0 AND condicion_inv = 1";*/
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

    public function lista_Depreciacion_Activo_xAll(){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM inventario WHERE id_cla = 1 AND cant_inv > 0 AND condicion_inv = 1";
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

    public function actualizar_Depreciacion_xIdInventario($datos){
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "UPDATE inventario SET fechadepre_inv = :fechaDepre, costo_act_inv = :costo, frec_depre_act_inv = :frecuencia WHERE id_inv = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":fechaDepre",$datos[1]);
            $stm->bindParam(":costo",$datos[2]);
            $stm->bindParam(":frecuencia",$datos[3], PDO::PARAM_INT);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function update_Estado_Item_Transito($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "UPDATE movimientos_transito_detalle SET transito_mde = :transito, recepcion_mde = :recepcion, estado_mde = :estado WHERE id_mtde = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":transito",$datos[1], PDO::PARAM_INT);
            $stm->bindParam(":recepcion",$datos[2], PDO::PARAM_INT);
            $stm->bindParam(":estado",$datos[3]);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function update_Inventario_Reversa($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "UPDATE inventario SET cant_inv = :cantidad, condicion_inv = :estado WHERE id_inv = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":cantidad",$datos[1]);
            $stm->bindParam(":estado",$datos[2],PDO::PARAM_INT);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function listar_Inventario_Detail_Count($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "
                SELECT COUNT(*) as 'total' FROM inventario
                WHERE id_alm = :id AND cant_inv > 0 AND condicion_inv = 1;
            ";
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

    public function listar_Inventario_Detail_All($id, $offset, $itemsPerPage, $search = null){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "
                SELECT inventario.*, clasificacion.*, (
                    SELECT count(*)
                FROM movimientos mov INNER JOIN movimientos_detalle mvd ON mov.id_mov = mvd.id_mov
                WHERE mvd.id_inv = inventario.id_inv AND mov.action_mov != 'IN' LIMIT 1
                ) AS 'ex_mov_item' FROM inventario 
                LEFT JOIN clasificacion ON clasificacion.id_cla = inventario.id_cla
                WHERE id_alm = :id AND cant_inv > 0 AND condicion_inv = 1 ";

            if($search){
                $query .= "
                    AND (inventario.cod_inv LIKE '%$search%' OR inventario.des_inv LIKE '%$search%')
                ";
            }

            $query .= "LIMIT :offset, :limit";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$id, PDO::PARAM_INT);
            $stm->bindParam(":offset",$offset, PDO::PARAM_INT);
            $stm->bindParam(":limit",$itemsPerPage, PDO::PARAM_INT);

            $stm->execute();
            $lista = $stm->fetchAll(PDO::FETCH_ASSOC);
            if(!$lista){$lista = null;}
            $stm = null;
            return $lista;
        } catch(PDOException $e){
            throw $e;
        }
    }
}
