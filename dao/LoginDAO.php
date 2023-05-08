<?php
require_once '../ds/AccesoDB.php';

class LoginDAO{

    public function consulta_Logueo_xNroDocumento($ndoc) {
        try {
            $pdo = AccesoDB::getPDO();
            $query = "SELECT per.*, us.* FROM persona per INNER JOIN usuario us ON per.id_per = us.id_per WHERE per.ndoc_per = :ndoc LIMIT 1";
            $stm = $pdo->prepare($query);
            $stm->bindParam(":ndoc",$ndoc, PDO::PARAM_STR);
            $stm->execute();
            $detalle = $stm->fetch(PDO::FETCH_ASSOC);
            if(!$detalle){$detalle = null;}
            $stm = null;
            return $detalle;
        } catch (PDOException $e) {
            throw $e;
        }
    }

}
