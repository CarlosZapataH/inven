<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);
$action = $_REQUEST["action"];

require_once __DIR__ . '/../../../../assets/util/Session.php';
$controller = new TransferGuideController();
call_user_func(array($controller,$action));


class TransferGuideController{
    // private $companyRepository;

    public function __construct()
    {
        // $this->companyRepository = new CompanyRepository();
    }

    public function show(){
        $response = [
            'data' => null,
            'success' => false,
            'message' => 'Error'
        ];
        try {
            $companyId = (int)$_GET['id'];
            $datos = $this->companyRepository->findBy('id', $companyId);
            if($datos){
                $response['data'] = $datos;
                $response['success'] = true;
                $response['message'] = 'Información obtenida exitosamente.';
            }
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
        echo json_encode($response);
    }
}