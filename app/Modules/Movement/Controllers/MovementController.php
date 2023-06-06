<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);
$action = $_REQUEST["action"];

require_once __DIR__ . '/../../../../assets/util/Session.php';
require_once __DIR__ . '/../Repository/MovementRepository.php';
$controller = new MovementController();
call_user_func(array($controller,$action));


class MovementController{
    private $movementRepository;

    public function __construct()
    {
        $this->movementRepository = new MovementRepository();
    }

    public function getMovement(){
        $response = [
            'data' => null,
            'success' => false,
            'message' => 'Error'
        ];
        try {
            $id = (int)$_GET['id'];
            $datos = $this->movementRepository->getMovement($id);
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