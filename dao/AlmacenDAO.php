<?php
require_once '../ds/AccesoDB.php';

class AlmacenDAO{

    public function lst_Almacenes_All_Activos(){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM almacen WHERE condicion_alm = 1";
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

    public function details_ultimo_carga_Almacen_xIDAlm($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT  cantfile_lgu as cantidad, DATE_FORMAT(fecha_lgu, '%w') as dia, 
            DATE_FORMAT(fecha_lgu, '%e') as dianumber, DATE_FORMAT(fecha_lgu, '%c') as mes, 
            DATE_FORMAT(fecha_lgu, '%Y') as anio, DATE_FORMAT(hora_lgu, '%h:%i %p') as hora 
            FROM log_almacen_carga WHERE id_alm = :id AND fecha_lgu = (SELECT max(fecha_lgu) FROM log_almacen_carga) LIMIT 1";
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

    public function lst_Almacenes_Asignados_xUsuario($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT alm.* FROM usuario_almacen  ua INNER JOIN almacen alm ON ua.id_alm = alm.id_alm WHERE ua.id_su = :id ORDER BY alm.des_alm ASC";
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

    public function detalle_Almacen_xID($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM almacen WHERE id_alm = :id";
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

    public function lista_almacenes_Activos_xServicio_menosAlmActual($idserv,$idalm){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM almacen WHERE id_serv = :idserv and id_alm <> :idalm ORDER BY titulo_alm ASC";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idserv",$idserv, PDO::PARAM_INT);
            $stm->bindParam(":idalm",$idalm, PDO::PARAM_INT);
            $stm->execute();
            $lista = $stm->fetchAll(PDO::FETCH_ASSOC);
            if(!$lista){$lista = null;}
            $stm = null;
            return $lista;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function detalle_correlativo_Almacen($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM almacen_correlativo WHERE id_alm = :id";
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

    public function actualizar_Correlativo_Almacen($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "UPDATE almacen_correlativo SET val_alc = :valor WHERE id_alc = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":valor",$datos[1], PDO::PARAM_INT);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function actualizar_Correlativo_NroVale_Autogenerado($id,$valor) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "UPDATE almacen SET numautogen_alm = :valor WHERE id_alm = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$id, PDO::PARAM_INT);
            $stm->bindParam(":valor",$valor, PDO::PARAM_INT);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function actualizar_Correlativo_NroDespacho($id,$valor) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "UPDATE almacen SET autogen_desp_alm = :valor WHERE id_alm = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$id, PDO::PARAM_INT);
            $stm->bindParam(":valor",$valor, PDO::PARAM_INT);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_log_upload_Almacen($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
            $pdo->beginTransaction();
            $query = "INSERT INTO log_almacen_carga (id_alm, fecha_lgu,hora_lgu,cantfile_lgu,id_us,creadopor_lgu)
                      VALUES (:idalm, :fecha,:hora,:cantfile,:idus,:creadopor)";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idalm",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":fecha",$datos[1], PDO::PARAM_STR);
            $stm->bindParam(":hora",$datos[2], PDO::PARAM_STR);
            $stm->bindParam(":cantfile",$datos[3], PDO::PARAM_STR);
            $stm->bindParam(":idus",$datos[4], PDO::PARAM_INT);
            $stm->bindParam(":creadopor",$datos[5], PDO::PARAM_STR);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lst_almacenes_All_xServicio($idserv){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM almacen WHERE id_serv = :idserv ORDER BY titulo_alm ASC";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idserv",$idserv, PDO::PARAM_INT);
            $stm->execute();
            $lista = $stm->fetchAll(PDO::FETCH_ASSOC);
            if(!$lista){$lista = null;}
            $stm = null;
            return $lista;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function lst_almacenes_Activos_All_xServicio($idserv){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM almacen WHERE id_serv = :idserv AND condicion_alm = 1 ORDER BY titulo_alm ASC";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idserv",$idserv, PDO::PARAM_INT);
            $stm->execute();
            $lista = $stm->fetchAll(PDO::FETCH_ASSOC);
            if(!$lista){$lista = null;}
            $stm = null;
            return $lista;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function update_Estado_Almacen($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
            $pdo->beginTransaction();
            $query = "UPDATE almacen SET condicion_alm = :estd WHERE id_alm = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":estd",$datos[1], PDO::PARAM_INT);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function delete_Almacen_xID($id) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "DELETE FROM almacen WHERE id_alm = :id";
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

    public function registrar_Almacen($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
            $query = "INSERT INTO almacen (id_serv,titulo_alm,des_alm,id_us,creadopor_alm,fechareg_alm,id_vale,
                                           autogen_alm,direccion_alm,departamento_alm,provincia_alm,distrito_alm,valecampo_alm)
                      VALUES (:idserv,:titulo,:des,:idus,:creadopor,:fecha,:idvale,:valeautogen,:direccion,:depa,:prov,:distri,:valecampo)";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idserv",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":titulo",$datos[1]);
            $stm->bindParam(":des",$datos[2]);
            $stm->bindParam(":idus",$datos[3], PDO::PARAM_INT);
            $stm->bindParam(":creadopor",$datos[4]);
            $stm->bindParam(":fecha",$datos[5]);
            $stm->bindParam(":idvale",$datos[6], PDO::PARAM_INT);
            $stm->bindParam(":valeautogen",$datos[7], PDO::PARAM_INT);
            $stm->bindParam(":direccion",$datos[8]);
            $stm->bindParam(":depa",$datos[9], PDO::PARAM_INT);
            $stm->bindParam(":prov",$datos[10], PDO::PARAM_INT);
            $stm->bindParam(":distri",$datos[11], PDO::PARAM_INT);
            $stm->bindParam(":valecampo",$datos[12], PDO::PARAM_INT);
            $stm->execute();
            $id = $pdo->lastInsertId();
            return $id;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_Almacen_Correlativo($id) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "INSERT INTO almacen_correlativo (id_alm) VALUES (:id)";
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

    public function update_Almacen($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "UPDATE almacen SET titulo_alm = :titulo, des_alm = :des, autogen_alm = :nautogen,
                             direccion_alm = :direccion, departamento_alm = :depa, provincia_alm = :prov,
                             distrito_alm = :distri, valecampo_alm = :valecampo  WHERE id_alm = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":titulo",$datos[1]);
            $stm->bindParam(":des",$datos[2]);
            $stm->bindParam(":nautogen",$datos[3], PDO::PARAM_INT);
            $stm->bindParam(":direccion",$datos[4]);
            $stm->bindParam(":depa",$datos[5], PDO::PARAM_INT);
            $stm->bindParam(":prov",$datos[6], PDO::PARAM_INT);
            $stm->bindParam(":distri",$datos[7], PDO::PARAM_INT);
            $stm->bindParam(":valecampo",$datos[8], PDO::PARAM_INT);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function registrar_Almacen_Backup_lastID($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
            $query = "INSERT INTO backup_almacen (id_alm,id_serv,anio_bai,mes_bai,fecha_bai,fechareg_bai) VALUES (:idalm,:idserv,:anio,:mes,:fecha,:fechareg)";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idalm",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":idserv",$datos[1], PDO::PARAM_INT);
            $stm->bindParam(":anio",$datos[2], PDO::PARAM_INT);
            $stm->bindParam(":mes",$datos[3], PDO::PARAM_INT);
            $stm->bindParam(":fecha",$datos[4], PDO::PARAM_STR);
            $stm->bindParam(":fechareg",$datos[5], PDO::PARAM_STR);
            $stm->execute();
            $id = $pdo->lastInsertId();
            return $id;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lst_Almacenes_All_Asignados_xUsuario($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT ua.id_ual, ua.condicion_ual, su.id_serv, alm.titulo_alm FROM servicio_usuario su 
            INNER JOIN usuario_almacen ua ON su.id_su = ua.id_su INNER JOIN almacen alm ON ua.id_alm = alm.id_alm 
            WHERE su.id_us = :id ORDER BY su.id_serv ASC, alm.des_alm ASC";
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

    public function delete_AlmacenUsuario_xID($id) {
        try {
            $pdo = AccesoDB::getPDO();
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "DELETE FROM usuario_almacen WHERE id_ual = :id";
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

    public function add_UsuarioAlmacen($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "INSERT INTO usuario_almacen (id_su,id_alm,fechareg_ual) VALUES (:idsu,:idalm,:fecha)";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idsu",$datos[0], PDO::PARAM_INT);
            $stm->bindParam(":idalm",$datos[1], PDO::PARAM_INT);
            $stm->bindParam(":fecha",$datos[2], PDO::PARAM_STR);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function busca_almacen_asignado($idsu,$idalm){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM usuario_almacen WHERE id_su = :idsu AND id_alm = :idalm LIMIT 1";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idsu",$idsu, PDO::PARAM_INT);
            $stm->bindParam(":idalm",$idalm, PDO::PARAM_INT);
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
