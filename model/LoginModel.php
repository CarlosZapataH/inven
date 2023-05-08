<?php
require_once '../dao/LoginDAO.php';

class LoginModel{

    public function consulta_Logueo_xNroDocumento($email){
        try{ $dao = new LoginDAO();
            $userCtrl = $dao->consulta_Logueo_xNroDocumento($email);
            return $userCtrl;
        }catch(PDOException $e){
            throw $e;
        }
    }
}
