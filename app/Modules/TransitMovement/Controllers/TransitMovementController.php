<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);
$action = $_REQUEST["action"];

require_once __DIR__ . '/../../../../assets/util/Session.php';
require_once __DIR__ . '/../Repository/TransitMovementRepository.php';
$controller = new TransitMovementController();
call_user_func(array($controller,$action));


class TransitMovementController{
    private $transitMovementRepository;

    public function __construct()
    {
        $this->transitMovementRepository = new TransitMovementRepository();
    }

    public function index(){
        $response = [
            'data' => null,
            'success' => false,
            'message' => 'Error'
        ];
        try {
            $id = $_GET['id'];
            $datos = $this->transitMovementRepository->findWithDetails($id);
            $response['data'] = $datos ?? [];
            $response['success'] = true;
            $response['message'] = 'Información obtenida exitosamente.';
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
            echo $e->getMessage();
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function show(){
        $response = [
            'data' => null,
            'success' => false,
            'message' => 'Error'
        ];
        try {
            $id = (int)$_GET['id'];
            $datos = $this->transitMovementRepository->findOneWithDetails($id);
            if($datos){
                $response['data'] = $datos;
                $response['success'] = true;
                $response['message'] = 'Información obtenida exitosamente.';
            }
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }
}