<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);
$action = $_REQUEST["action"];

require_once __DIR__ . '/../../../../assets/util/Session.php';
require_once '../Repository/CompanyRepository.php';
require_once '../Contract/ICompany.php';
require_once __DIR__ . '/../../../Helpers/GlobalHelper.php';
$controller = new CompanyController();
call_user_func(array($controller,$action));


class CompanyController{
    private $companyRepository;

    public function __construct()
    {
        $this->companyRepository = new CompanyRepository();
    }

    public function index(){
        try {
            $datos = $this->companyRepository->find();
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

    public function store(){
        header('Content-Type: application/json');
        $response = GlobalHelper::getGlobalResponse();
        try {
            $data = GlobalHelper::getPostData();
            $id = $this->companyRepository->store($data);
            if($id){
                $response['data'] = ['id' => $id];
                $response['success'] = true;
                $response['message'] = 'Información registrada exitosamente.';
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