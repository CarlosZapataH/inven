<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);
$action = $_REQUEST["action"];

require_once __DIR__ . '/../../../../assets/util/Session.php';
require_once __DIR__ . '/../../DocumentType/Repository/DocumentTypeRepository.php';
require_once __DIR__ . '/../Helpers/ModalityHelper.php';
require_once __DIR__ . '/../Helpers/MotiveHelper.php';

$controller = new UtilController();
call_user_func(array($controller,$action));


class UtilController{
    private $documentTypeRepository;

    public function __construct()
    {
        $this->documentTypeRepository = new DocumentTypeRepository();
    }

    public function index(){
        try {
            $documentTypes = $this->documentTypeRepository->find();
            header('Content-Type: application/json');
            echo json_encode([
                'data' => [
                    'documentTypes' => $documentTypes,
                    'modalities' => ModalityHelper::getAll(),
                    'motives' => MotiveHelper::getAll(),
                ],
                'success' => true
            ]);
        } catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }
    }

}