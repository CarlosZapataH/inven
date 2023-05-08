<?php 
session_start();
require_once '../assets/util/Session.php';
require_once '../controller/ControlSesion.php';
require_once '../model/PermisoModel.php';
$objpermiso= new PermisoModel();
?>
    <li>
        <a href="sistema.php">
            <i class="ti-home"></i>
            <span>Inicio</span>
        </a>
    </li>
<?php
$per=$objpermiso->obtener_permisos($user['id_us'],$opcion=NULL);
$objpermiso->menus($per);

?>
<li>
    <a class="cursor-pointer " id="btnGenerarToken">
        <i class="ti-key"></i>
        Generar Token
    </a>
</li>
