<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);
$action = $_REQUEST["action"];

require_once __DIR__ . '/../../../../assets/util/Session.php';
require_once '../Repository/UbigeoRepository.php';
require_once '../Contract/IUbigeo.php';
$controller = new UbigeoController();
call_user_func(array($controller,$action));


class UbigeoController{
    private $ubigeoTypeRepository;

    public function __construct()
    {
        $this->ubigeoTypeRepository = new UbigeoRepository();
    }

    public function getDepartments(){
        header('Content-Type: application/json');
        $response = [
            'data' => null,
            'success' => false,
            'message' => 'Error'
        ];

        try {
            $datos = $this->ubigeoTypeRepository->getDepartments();
            $response['data'] = $datos;
            $response['success'] = true;
            $response['message'] = 'Información obtenida exitosamente.';
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }

        echo json_encode($response);
    }

    public function getProvinces(){
        header('Content-Type: application/json');
        $response = [
            'data' => null,
            'success' => false,
            'message' => 'Error'
        ];

        try {
            if($_GET['department_id']){
                $id = (int)$_GET['department_id'];
                $datos = $this->ubigeoTypeRepository->getProvinces($id);
                $response['data'] = $datos;
                $response['success'] = true;
                $response['message'] = 'Información obtenida exitosamente.';
            }
            else{
                $response['message'] = 'El department_id es obligatorio.';
            }
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }

        echo json_encode($response);
    }

    public function getDistricts(){
        header('Content-Type: application/json');
        $response = [
            'data' => null,
            'success' => false,
            'message' => 'Error'
        ];

        try {
            if($_GET['province_id']){
                $id = (int)$_GET['province_id'];
                $datos = $this->ubigeoTypeRepository->getDistricts($id);
                $response['data'] = $datos;
                $response['success'] = true;
                $response['message'] = 'Información obtenida exitosamente.';
            }
            else{
                $response['message'] = 'El province_id es obligatorio.';
            }
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }

        echo json_encode($response);
    }
}