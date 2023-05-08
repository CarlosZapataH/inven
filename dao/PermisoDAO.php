<?php
require_once '../ds/AccesoDB.php';

class PermisoDAO{

    public $total_registros;

    public function obtener_permisos($id,$opcion){
        try{
            $pdo = AccesoDB::getPDO();
            $opcion_=$opcion;
            $query  =" SELECT mo.*,p.*";
            $query .=" FROM usuario u ";
            $query .="     INNER JOIN perfil pf ON (pf.id_perfil=u.id_perfil)";
			$query .="     INNER JOIN modulo_opcion mo";
            if(!is_null($opcion)){$query.=" ON (mo.opcion LIKE '%$opcion%')";}
            $query .=" INNER JOIN permiso p ON (p.id_perfil=pf.id_perfil 
				         AND p.id_modulo_opcion=mo.id_modulo_opcion)
				       WHERE u.id_us='$id' and mo.estado='A'";
            $query .=" ORDER BY mo.indiceh,mo.indicev";

            $stm = $pdo->prepare($query);
            $stm->execute();
            $permiso = $stm->fetchAll(PDO::FETCH_ASSOC);
            $stm = null;
            $this->total_registros;
            $opciones = [];
            foreach($permiso as $fila){
                $opcion=explode("/",$fila['opcion']);
                $n=count($opcion);
                if($n==1){
                    $opciones[$opcion[0]]['idmo'] = $fila['id_modulo_opcion'];
                    $opciones[$opcion[0]]['acceso'] = $fila['acceso'];
                    $opciones[$opcion[0]]['url'] = $fila['url'];
                    $opciones[$opcion[0]]['estilo'] = $fila['estilo'];
                    $opciones[$opcion[0]]['icon'] = $fila['icon'];
                    $opciones[$opcion[0]]['abreviado'] = $fila['abreviado'];
                    $opciones[$opcion[0]]['badge'] = $fila['badge'];
                    $opciones[$opcion[0]]['color'] = $fila['badge_color'];
                    $opciones[$opcion[0]]['raiz'] = "si";
                }
                elseif($n==2){
                    $opciones[$opcion[0]]['opciones'][$opcion[1]]['acceso']=$fila['acceso'];
                    if ($opcion[1]==$opcion_)
                        $_SESSION['permisos']=$opciones[$opcion[0]]['opciones'][$opcion[1]];
                        $opciones[$opcion[0]]['opciones'][$opcion[1]]['idmo']=$fila['id_modulo_opcion'];
                        $opciones[$opcion[0]]['opciones'][$opcion[1]]['url']=$fila['url'];
                        $opciones[$opcion[0]]['opciones'][$opcion[1]]['estilo']=$fila['estilo'];
                        $opciones[$opcion[0]]['opciones'][$opcion[1]]['icon']=$fila['icon'];
                        $opciones[$opcion[0]]['opciones'][$opcion[1]]['abreviado']=$fila['abreviado'];
                        $opciones[$opcion[0]]['opciones'][$opcion[1]]['badge']=$fila['badge'];
                        $opciones[$opcion[0]]['opciones'][$opcion[1]]['color']=$fila['badge_color'];
                }
                elseif($n==3){
                    $opciones[$opcion[0]]['opciones'][$opcion[1]]['opciones'][$opcion[2]]['idmo']=$fila['id_modulo_opcion'];
                    $opciones[$opcion[0]]['opciones'][$opcion[1]]['opciones'][$opcion[2]]['acceso']=$fila['acceso'];
                    $opciones[$opcion[0]]['opciones'][$opcion[1]]['opciones'][$opcion[2]]['url']=$fila['url'];
                    $opciones[$opcion[0]]['opciones'][$opcion[1]]['opciones'][$opcion[2]]['estilo']=$fila['estilo'];
                    $opciones[$opcion[0]]['opciones'][$opcion[1]]['opciones'][$opcion[2]]['icon']=$fila['icon'];
                    $opciones[$opcion[0]]['opciones'][$opcion[1]]['opciones'][$opcion[2]]['abreviado']=$fila['abreviado'];
                    $opciones[$opcion[0]]['opciones'][$opcion[1]]['opciones'][$opcion[2]]['badge']=$fila['badge'];
                    $opciones[$opcion[0]]['opciones'][$opcion[1]]['opciones'][$opcion[2]]['color']=$fila['badge_color'];
                }
            }
            return $opciones;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function listar_modulos($condicion,$order){
        try{$pdo = AccesoDB::getPDO();
            if(!$order==NULL){$ordena= "ORDER BY $order";}
            $query ="SELECT * 
				 FROM modulo_opcion $condicion 
				 $ordena";
            $stm = $pdo->prepare($query);
            $stm->execute();
            $lista = $stm->fetchAll(PDO::FETCH_ASSOC);
            $stm = null;
            return $lista;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function eliminarRegistros($tabla, $condicion) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "DELETE FROM $tabla $condicion";
            $stm = $pdo->prepare($query);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function listar_modulo_opcion($id_modulo_opcion,$condicion,$order){
        try{$pdo = AccesoDB::getPDO();
            $condi="";
            $ordena="";
            if(!$order==NULL){$ordena= "ORDER BY $order";}
            if(!empty($condicion)){$condi= $condicion;}
            $query ="SELECT * FROM modulo_opcion mo WHERE mo.padre=$id_modulo_opcion $condi $ordena";
            $stm = $pdo->prepare($query);
            $stm->execute();
            $lista = $stm->fetchAll(PDO::FETCH_ASSOC);
            $stm = null;
            return $lista;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function actualizar($tabla,$campos,$condicion) {
        try { $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            foreach ($campos as $campo=>$valor)
            { $campos_nuevo[]="$campo = '$valor'"; }
            $campos_nuevo=implode(" , ",$campos_nuevo);
            $query = "UPDATE $tabla SET $campos_nuevo WHERE id_{$tabla}=".$condicion;
            $stm = $pdo->prepare($query);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function listar_modulo_opciones($id_modulo_opcion,$condicion,$order){
        try{$pdo = AccesoDB::getPDO();
            if(!$order==NULL) $ordena= "ORDER BY $order";
            $conex=AccesoDB::getConnection();
            $query ="SELECT * 
				 FROM modulo_opcion mo 
				 WHERE id_modulo_opcion=$id_modulo_opcion $condicion 
				 $ordena";
            $stm = $pdo->prepare($query);
            $stm->execute();
            $lista = $stm->fetchAll();
            $stm = null;
            return $lista;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function lst_modulosHijos_xIdPadrea($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query ="SELECT * FROM modulo_opcion WHERE padre = :id";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$id,PDO::PARAM_INT);
            $stm->execute();
            $lista = $stm->fetchAll(PDO::FETCH_ASSOC);
            $stm = null;
            return $lista;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function lst_modulosHijos_xIdPadrea_sinhijoActual($idpadre,$hijoactual){
        try{
            $pdo = AccesoDB::getPDO();
            $query ="SELECT * FROM modulo_opcion WHERE padre = :id AND id_modulo_opcion <> :hijo";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":id",$idpadre,PDO::PARAM_INT);
            $stm->bindParam(":hijo",$hijoactual,PDO::PARAM_INT);
            $stm->execute();
            $lista = $stm->fetchAll(PDO::FETCH_ASSOC);
            $stm = null;
            return $lista;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function delete_Permiso_xID($id){
        try { $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, TRUE);
            $pdo->beginTransaction();
            $query ="DELETE FROM permiso WHERE id_permiso = :id";
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

    public function detalle_Padre_permiso($idperfil,$idpadre){
        try{$pdo = AccesoDB::getPDO();
            $query ="SELECT * FROM permiso WHERE id_perfil = :idperfil AND id_modulo_opcion = :idmodulo AND acceso = 0";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idperfil",$idperfil,PDO::PARAM_INT);
            $stm->bindParam(":idmodulo",$idpadre,PDO::PARAM_INT);
            $stm->execute();
            $detalle = $stm->fetch(PDO::FETCH_ASSOC);
            if(!$detalle){$detalle = null;}
            $stm = null;
            return $detalle;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function detalle_Permiso_xIDs($idperfil,$idpadre){
        try{$pdo = AccesoDB::getPDO();
            $query ="SELECT * FROM permiso WHERE id_perfil = :idperfil and id_modulo_opcion = :idmodulo";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idperfil",$idperfil,PDO::PARAM_INT);
            $stm->bindParam(":idmodulo",$idpadre,PDO::PARAM_INT);
            $stm->execute();
            $detalle = $stm->fetch(PDO::FETCH_ASSOC);
            if(!$detalle){$detalle = null;}
            $stm = null;
            return $detalle;
        } catch(PDOException $e){
            throw $e;
        }
    }

    public function detalle_moduloOpcion_xID($id){
        try{
            $pdo = AccesoDB::getPDO();
            $query ="SELECT * FROM modulo_opcion WHERE id_modulo_opcion = :id";
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

    public function registrar_permiso($datos) {
        try {
            $pdo = AccesoDB::getPDO();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
            $pdo->beginTransaction();
            $query = "INSERT INTO permiso (id_perfil,id_modulo_opcion,acceso) VALUES (:idperfil,:idmodulo,:valacceso)";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":idperfil",$datos[0],PDO::PARAM_INT);
            $stm->bindParam(":idmodulo",$datos[1],PDO::PARAM_INT);
            $stm->bindParam(":valacceso",$datos[2],PDO::PARAM_INT);
            $stm->execute();
            $pdo->commit();
            if(!$pdo) return false;
            return true;
        } catch (PDOException $e) {
            throw $e;
        }
    }
}