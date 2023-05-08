<?php
require_once '../ds/AccesoDB.php';

class MaterialDAO{

    public function buscar_Material_xCodigo($id,$cod){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM material WHERE id_alm = :id AND cod_mat = :codigo AND condicion_mat = 1 LIMIT 1";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$id, PDO::PARAM_INT);
            $stm->bindParam(":codigo",$cod);
            $stm->execute();
            $detalle = $stm->fetch(PDO::FETCH_ASSOC);
            if(!$detalle){$detalle = null;}
            $stm = null;
            return $detalle;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function registrar_Despacho_lastID($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
            $query = "INSERT INTO despacho (id_serv,desserv_des,id_alm,desalm_des,id_col,solicitadopor_des,puesto_des,ndoc_des,fecha_des,hora_des,
                                            codtransac_des,codigo_des,fecharegsis_desp,id_us,creadopor_des,tipovalidacion_des,codigoadmin_des,statusvalidacion_des,
                                            fechavalidacion_des,estado_des,ndoccreadopor_des,tipodespacho_des)
                      VALUES (:idserv,:desServ,:idAlm,:desAlm,:idColab,:nameColab,:puestoColab,:docColab,:fechaTrans,:horaTrans,:codTrans,:CodOpera,:fechaReg,:idUsuario,:nameUsuario,
                              :tipovalida,:codvalida,:statusValida,:timeValida,:estado,:ndoccreadopor,:tipodespacho)";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idserv",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":desServ",$datos[1]);
            $stm->bindParam(":idAlm",$datos[2], PDO::PARAM_INT);
            $stm->bindParam(":desAlm",$datos[3]);
            $stm->bindParam(":idColab",$datos[4], PDO::PARAM_INT);
            $stm->bindParam(":nameColab",$datos[5]);
            $stm->bindParam(":puestoColab",$datos[6]);
            $stm->bindParam(":docColab",$datos[7]);
            $stm->bindParam(":fechaTrans",$datos[8]);
            $stm->bindParam(":horaTrans",$datos[9]);
            $stm->bindParam(":codTrans",$datos[10]);
            $stm->bindParam(":CodOpera",$datos[11], PDO::PARAM_INT);
            $stm->bindParam(":fechaReg",$datos[12]);
            $stm->bindParam(":idUsuario",$datos[13], PDO::PARAM_INT);
            $stm->bindParam(":nameUsuario",$datos[14]);
            $stm->bindParam(":tipovalida",$datos[15]);
            $stm->bindParam(":codvalida",$datos[16]);
            $stm->bindParam(":statusValida",$datos[17], PDO::PARAM_INT);
            $stm->bindParam(":timeValida",$datos[18]);
            $stm->bindParam(":estado",$datos[19]);
            $stm->bindParam(":ndoccreadopor",$datos[20]);
            $stm->bindParam(":tipodespacho",$datos[21]);
            $stm->execute();
            $id = $pdo->lastInsertId();
            return $id;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function detalle_Despachos_xNumOperacion($id,$cod){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM despacho WHERE id_alm = :id AND codigo_des = :codigo LIMIT 1";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$id, PDO::PARAM_INT);
            $stm->bindParam(":codigo",$cod,PDO::PARAM_INT);
            $stm->execute();
            $detalle = $stm->fetch(PDO::FETCH_ASSOC);
            if(!$detalle){$detalle = null;}
            $stm = null;
            return $detalle;
        } catch(PDOException $e){
            throw $e;
        }
    }


    public function registrar_Despacho_Detalle($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "INSERT INTO detalle_despacho(id_des,coddes_ded,id_mat,clasificacion_ded,codigo_ded,descripcion_ded,um_ded,cantidad_ded,area_ded,fecha_entrega_ded,hora_entrega_ded,
                                  periodo_renov_est_ded,fecha_renov_est_ded,id_col,ndoc_ded,persona_ded,id_alm,desalm_ded,id_serv,desserv_ded,fechahora_ded,creadopor_ded)
                      VALUES (:idDespacho,:codDespacho,:idMaterial,:clasificacion,:cod,:des,:um,:cant,:area,:fecha,:hora,:frecuencia,:fechaEstimada,:idColaborador,:ndocColab,:nameColab,
                              :idAlmacen,:desAlmacen,:idServicio,:desServicio,:fechaHoraReg,:credopor)";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idDespacho",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":codDespacho",$datos[1]);
            $stm->bindParam(":idMaterial",$datos[2], PDO::PARAM_INT);
            $stm->bindParam(":clasificacion",$datos[3]);
            $stm->bindParam(":cod",$datos[4]);
            $stm->bindParam(":des",$datos[5]);
            $stm->bindParam(":um",$datos[6]);
            $stm->bindParam(":cant",$datos[7], PDO::PARAM_INT);
            $stm->bindParam(":area",$datos[8]);
            $stm->bindParam(":fecha",$datos[9]);
            $stm->bindParam(":hora",$datos[10]);
            $stm->bindParam(":frecuencia",$datos[11], PDO::PARAM_INT);
            $stm->bindParam(":fechaEstimada",$datos[12]);
            $stm->bindParam(":idColaborador",$datos[13], PDO::PARAM_INT);
            $stm->bindParam(":ndocColab",$datos[14]);
            $stm->bindParam(":nameColab",$datos[15]);
            $stm->bindParam(":idAlmacen",$datos[16], PDO::PARAM_INT);
            $stm->bindParam(":desAlmacen",$datos[17]);
            $stm->bindParam(":idServicio",$datos[18], PDO::PARAM_INT);
            $stm->bindParam(":desServicio",$datos[19]);
            $stm->bindParam(":fechaHoraReg",$datos[20]);
            $stm->bindParam(":credopor",$datos[21]);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_Despachos_xColaborador($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT id_des, desserv_des as servicio, desalm_des as almacen, solicitadopor_des as colaborador,
                            ndoc_des as ndoc, date_format(fecha_des,'%d/%m/%Y') as fecha,date_format(hora_des,'%H:%i') as hora,
                            codigo_des as codigo, tipovalidacion_des as validacion, estado_des as estado,date_format(fecha_des,'%d%m%Y') as fechajun,condicion_des
                      FROM despacho
                      WHERE id_col = :id
                      ORDER BY desserv_des, desalm_des, fecha_des, hora_des ASC";
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

    public function lista_Despachos_Rango_xColaborador($datos){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT id_des, desserv_des as servicio, desalm_des as almacen, solicitadopor_des as colaborador,
                            ndoc_des as ndoc, date_format(fecha_des,'%d/%m/%Y') as fecha,date_format(hora_des,'%H:%i') as hora,
                            codigo_des as codigo, tipovalidacion_des as validacion, estado_des as estado,date_format(fecha_des,'%d%m%Y') as fechajun,condicion_des
                      FROM despacho
                      WHERE id_col = :id AND fecha_des BETWEEN :FDesde AND :FHasta
                      ORDER BY desserv_des, desalm_des, fecha_des, hora_des ASC";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":FDesde",$datos[1]);
            $stm->bindParam(":FHasta",$datos[2]);
            $stm->execute();
            $lista = $stm->fetchAll(PDO::FETCH_ASSOC);
            if(!$lista){$lista = null;}
            $stm = null;
            return $lista;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function lista_Consumos_Rango_xAlmacen($datos){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT persona_ded as colaborador,area_ded as area,creadopor_ded as creadopor,coddes_ded as codigodes, desserv_ded as servicio, 
                             desalm_ded as almacen,codigo_ded as codigo,descripcion_ded as descripcion, clasificacion_ded as clasificacion,
                             um_ded as unidadm,cantidad_ded as cantidad, date_format(fecha_entrega_ded,'%d/%m/%Y') as fechaentrega,
                             date_format(hora_entrega_ded,'%H:%i') as horaentrega
                      FROM detalle_despacho
                      WHERE id_alm = :id and fecha_entrega_ded BETWEEN :FDesde AND :FHasta AND condicion_ded = 1
                      ORDER BY id_alm, persona_ded, fecha_entrega_ded, hora_entrega_ded ASC";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":FDesde",$datos[1]);
            $stm->bindParam(":FHasta",$datos[2]);
            $stm->execute();
            $lista = $stm->fetchAll(PDO::FETCH_ASSOC);
            if(!$lista){$lista = null;}
            $stm = null;
            return $lista;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function detalle_Despacho_xID($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM despacho WHERE id_des = :id";
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

    public function lista_Materiales_xIdDespacho($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT codigo_ded as codigo,descripcion_ded as descripcion,area_ded as area,
                             um_ded as unidadm,cantidad_ded as cantidad, date_format(fecha_entrega_ded,'%d/%m/%Y') as fechaentrega,
                             date_format(hora_entrega_ded,'%H:%i') as horaentrega, periodo_renov_est_ded as periodo 
                      FROM detalle_despacho
                      WHERE id_des = :id AND condicion_ded = 1";
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

    public function lista_Despachos_Detalle_Historial_xColaborador($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT coddes_ded as codigodes, desserv_ded as servicio, desalm_ded as almacen,codigo_ded as codigo,descripcion_ded as descripcion,
                             um_ded as unidadm,cantidad_ded as cantidad, date_format(fecha_entrega_ded,'%d/%m/%Y') as fechaentrega,date_format(hora_entrega_ded,'%H:%i') as horaentrega
                      FROM detalle_despacho
                      WHERE id_col = :id AND condicion_ded = 1
                      ORDER BY fecha_entrega_ded, id_des DESC
                      LIMIT 60";
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

    public function lista_Despachos_Detalle_xColaborador($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT coddes_ded as codigodes, desserv_ded as servicio, desalm_ded as almacen,codigo_ded as codigo,descripcion_ded as descripcion,area_ded as area,
                             um_ded as unidadm,cantidad_ded as cantidad, date_format(fecha_entrega_ded,'%d/%m/%Y') as fechaentrega,date_format(hora_entrega_ded,'%H:%i') as horaentrega,
                             periodo_renov_est_ded as periodo
                      FROM detalle_despacho
                      WHERE id_col = :id AND condicion_ded = 1
                      ORDER BY fecha_entrega_ded, id_des ASC";
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

    public function lista_Despachos_Detalle_Rango_xColaborador($datos){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT coddes_ded as codigodes, desserv_ded as servicio, desalm_ded as almacen,codigo_ded as codigo,descripcion_ded as descripcion,descripcion,area_ded as area,
                             um_ded as unidadm,cantidad_ded as cantidad, date_format(fecha_entrega_ded,'%d/%m/%Y') as fechaentrega,date_format(hora_entrega_ded,'%H:%i') as horaentrega,
                             periodo_renov_est_ded as periodo       
                      FROM detalle_despacho
                      WHERE id_col = :id and fecha_entrega_ded BETWEEN :FDesde AND :FHasta AND condicion_ded = 1
                      ORDER BY fecha_entrega_ded, id_des DESC";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":FDesde",$datos[1]);
            $stm->bindParam(":FHasta",$datos[2]);
            $stm->execute();
            $lista = $stm->fetchAll(PDO::FETCH_ASSOC);
            if(!$lista){$lista = null;}
            $stm = null;
            return $lista;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function anula_Despacho_xID($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "UPDATE despacho SET iduseranula_des = :idUsuario,useranula_des = :nameUsuario,fechanula_des = :fecha,motivoanula_des = :motivo,estado_des = :estado, condicion_des = :condicion WHERE id_des = :idDespacho";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idDespacho",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":idUsuario",$datos[1], PDO::PARAM_INT);
            $stm->bindParam(":nameUsuario",$datos[2]);
            $stm->bindParam(":fecha",$datos[3]);
            $stm->bindParam(":motivo",$datos[4]);
            $stm->bindParam(":estado",$datos[5]);
            $stm->bindParam(":condicion",$datos[6], PDO::PARAM_INT);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function anula_Despacho_Detalle_xIDDespacho($id) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "UPDATE detalle_despacho SET condicion_ded = 0 WHERE id_des = :idDespacho";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idDespacho",$id, PDO::PARAM_INT);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_Material_xAlmacen($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM material WHERE id_alm = :id";
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

    public function update_Material_Estado_xID($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "UPDATE material SET  condicion_mat = :condicion WHERE id_mat = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":condicion",$datos[1], PDO::PARAM_INT);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function detalle_Material_xID($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM material WHERE id_mat = :id";
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

    public function actualizar_actionDelete_Material_xID($id,$opc) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "UPDATE material SET actiondel_mat = :actiondel WHERE id_mat = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$id, PDO::PARAM_INT);
            $stm->bindParam(":actiondel",$opc, PDO::PARAM_INT);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function eliminar_Material_xID($id) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "DELETE FROM material WHERE id_mat = :id";
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

    public function busca_existencia_codMaterial_xAlmacen($datos){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM material WHERE id_alm = :idAlmacen AND id_mat != :idMateriaAnterior AND cod_mat = :codMaterialNuevo";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idAlmacen",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":idMateriaAnterior",$datos[1], PDO::PARAM_INT);
            $stm->bindParam(":codMaterialNuevo",$datos[2]);
            $stm->execute();
            $lista = $stm->fetchAll(PDO::FETCH_ASSOC);
            if(!$lista){$lista = null;}
            $stm = null;
            return $lista;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function update_Material_xID($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "UPDATE material SET clasificacion_mat = :clasifica,cod_mat = :codigo,des_mat = :descripcion,um_mat = :umedida,
                                          renova_mat = :renova,frecrenova_mat = :frecuencia, ubica_mat = :ubica WHERE id_mat = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":clasifica",$datos[1]);
            $stm->bindParam(":codigo",$datos[2]);
            $stm->bindParam(":descripcion",$datos[3]);
            $stm->bindParam(":umedida",$datos[4]);
            $stm->bindParam(":renova",$datos[5], PDO::PARAM_INT);
            $stm->bindParam(":frecuencia",$datos[6], PDO::PARAM_INT);
            $stm->bindParam(":ubica",$datos[7]);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function busca_codMaterial_xAlmacen($datos){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM material WHERE id_alm = :idAlmacen AND cod_mat = :codMaterial AND condicion_mat = 1";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idAlmacen",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":codMaterial",$datos[1]);
            $stm->execute();
            $lista = $stm->fetchAll(PDO::FETCH_ASSOC);
            if(!$lista){$lista = null;}
            $stm = null;
            return $lista;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function registrar_Material($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "INSERT INTO material(id_alm,clasificacion_mat,cod_mat,des_mat,um_mat,renova_mat,frecrenova_mat)
                      VALUES (:idAlmacen,:clasificacion,:codigo,:descripcion,:unidadMed,:renueva,:frecuencia)";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idAlmacen",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":clasificacion",$datos[1]);
            $stm->bindParam(":codigo",$datos[2]);
            $stm->bindParam(":descripcion",$datos[3]);
            $stm->bindParam(":unidadMed",$datos[4]);
            $stm->bindParam(":renueva",$datos[5], PDO::PARAM_INT);
            $stm->bindParam(":frecuencia",$datos[6], PDO::PARAM_INT);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lista_Material_xID_All($where){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT cod_mat as codigo, des_mat as descrip, um_mat as um FROM material WHERE ".$where;
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
}
