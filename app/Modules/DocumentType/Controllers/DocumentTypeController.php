<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);
$action = $_REQUEST["action"];

require_once __DIR__ . '/../../../../assets/util/Session.php';
require_once '../Repository/DocumentTypeRepository.php';
require_once '../Contract/IDocumentType.php';
$controller = new DocumentTypeController();
call_user_func(array($controller,$action));


class DocumentTypeController{
    private $documentTypeRepository;

    public function __construct()
    {
        $this->documentTypeRepository = new DocumentTypeRepository();
    }

    public function index(){
        try {
            $datos = $this->documentTypeRepository->find();
            echo json_encode([
                'data' => $datos,
                'success' => true
            ]);
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

    public function show(){
        $response = [
            'data' => null,
            'success' => false,
            'message' => 'Error'
        ];
        try {
            $id = (int)$_GET['id'];
            $datos = $this->documentTypeRepository->findBy('id', $id);
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


    // HTML
    public function loadFormCreate(){
        
    }
}