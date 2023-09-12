<?php
require_once '../ds/AccesoDB.php';

class PerfilDAO{

    public function lst_Perfil_All(){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM perfil WHERE id_perfil <> 1 ORDER BY des_perfil ";
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

    public function registrar_Perfil($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "INSERT INTO perfil(titulo_perfil,des_perfil,nuevo_perfil,editar_perfil,eliminar_perfil,visualizar_perfil,reporte_perfil,importar_perfil,activasusp_perfil,transferir_perfil,retirar_perfil,devolver_perfil, create_guide, edit_guide, revert_guide, show_guide)  
                      VALUES (:titulo,:des,:op1,:op2,:op3,:op4,:op5,:op6,:op7,:op8,:op9,:op10,:op11,:op12,:op13,:op14)";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":titulo",$datos[0], PDO::PARAM_STR);
            $stm->bindParam(":des",$datos[1], PDO::PARAM_STR);
            $stm->bindParam(":op1",$datos[2], PDO::PARAM_INT);
            $stm->bindParam(":op2",$datos[3], PDO::PARAM_INT);
            $stm->bindParam(":op3",$datos[4], PDO::PARAM_INT);
            $stm->bindParam(":op4",$datos[5], PDO::PARAM_INT);
            $stm->bindParam(":op5",$datos[6], PDO::PARAM_INT);
            $stm->bindParam(":op6",$datos[7], PDO::PARAM_INT);
            $stm->bindParam(":op7",$datos[8], PDO::PARAM_INT);
            $stm->bindParam(":op8",$datos[9], PDO::PARAM_INT);
            $stm->bindParam(":op9",$datos[10], PDO::PARAM_INT);
            $stm->bindParam(":op10",$datos[11], PDO::PARAM_INT);
            $stm->bindParam(":op11",$datos[12], PDO::PARAM_INT);
            $stm->bindParam(":op12",$datos[13], PDO::PARAM_INT);
            $stm->bindParam(":op13",$datos[14], PDO::PARAM_INT);
            $stm->bindParam(":op14",$datos[15], PDO::PARAM_INT);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function update_Estado_Perfil($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "UPDATE perfil SET condicion_perfil = :estd WHERE id_perfil = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$datos[0],PDO::PARAM_INT);
            $stm->bindParam(":estd",$datos[1],PDO::PARAM_INT);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function delete_Perfil_xID($id){
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "DELETE FROM perfil WHERE id_perfil = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$id,PDO::PARAM_INT);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function detalle_Perfil_xID($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM perfil WHERE id_perfil = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$id,PDO::PARAM_INT);
            $stm->execute();
            $detalle = $stm->fetch(PDO::FETCH_ASSOC);
            if(!$detalle){$detalle = null;}
            $stm = null;
            return $detalle;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function update_Perfil($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "UPDATE perfil SET titulo_perfil = :titulo,des_perfil = :des,nuevo_perfil = :op1,editar_perfil = :op2,eliminar_perfil = :op3,visualizar_perfil = :op4,
                      reporte_perfil = :op5,importar_perfil = :op6,activasusp_perfil = :op7,transferir_perfil = :op8,retirar_perfil = :op9,devolver_perfil = :op10, create_guide = :op11, edit_guide = :op12, revert_guide = :op13, show_guide = :op14 WHERE id_perfil = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":titulo",$datos[0], PDO::PARAM_STR);
            $stm->bindParam(":des",$datos[1], PDO::PARAM_STR);
            $stm->bindParam(":op1",$datos[2], PDO::PARAM_INT);
            $stm->bindParam(":op2",$datos[3], PDO::PARAM_INT);
            $stm->bindParam(":op3",$datos[4], PDO::PARAM_INT);
            $stm->bindParam(":op4",$datos[5], PDO::PARAM_INT);
            $stm->bindParam(":op5",$datos[6], PDO::PARAM_INT);
            $stm->bindParam(":op6",$datos[7], PDO::PARAM_INT);
            $stm->bindParam(":op7",$datos[8], PDO::PARAM_INT);
            $stm->bindParam(":op8",$datos[9], PDO::PARAM_INT);
            $stm->bindParam(":op9",$datos[10], PDO::PARAM_INT);
            $stm->bindParam(":op10",$datos[11], PDO::PARAM_INT);
            $stm->bindParam(":op11",$datos[12], PDO::PARAM_INT);
            $stm->bindParam(":op12",$datos[13], PDO::PARAM_INT);
            $stm->bindParam(":op13",$datos[14], PDO::PARAM_INT);
            $stm->bindParam(":op14",$datos[15], PDO::PARAM_INT);
            $stm->bindParam(":id",$datos[16],PDO::PARAM_INT);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function lst_Perfil_Activos_All(){
        try{
            $pdo = AccesoDB::getPDO();
            $query = "SELECT * FROM perfil WHERE id_perfil <> 1 AND condicion_perfil = 1 ORDER BY des_perfil ";
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