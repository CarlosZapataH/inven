<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);
$action = $_REQUEST["action"];

require_once __DIR__ . '/../../../../assets/util/Session.php';
require_once __DIR__ . '/../Requests/TransitMovementGuideRequest.php';
require_once __DIR__ . '/../../TransitMovement/Repository/TransitMovementRepository.php';
require_once __DIR__ . '/../Helpers/FormatHelper.php';
require_once __DIR__ . '/../Helpers/ValidateHelper.php';
require_once __DIR__ . '/../Helpers/XMLHelper.php';
require_once __DIR__ . '/../Services/TCIService.php';
$controller = new TransferGuideController();
call_user_func(array($controller,$action));


class TransferGuideController{
    // private $companyRepository;
    private $transitMovementRepository;

    public function __construct()
    {
        // $this->companyRepository = new CompanyRepository();
        $this->transitMovementRepository = new TransitMovementRepository();
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

    public function storeTransitMovementGuide(){
        $response = [
            'data' => null,
            'success' => false,
            'message' => 'Error',
            'errors' => null
        ];
        try {
            $postData = file_get_contents('php://input');
            $data = json_decode($postData, true);
            if (json_last_error() === JSON_ERROR_NONE){
                $id = (int)$_GET['id'];
                $movement = $this->transitMovementRepository->getTransitMovement($id);
                if($movement){
                    $rules = $this->getRulesTransitMovementGuide($movement);
                    $validated = $this->validatedData($data, $rules);
    
                    if($validated['success']){
                        // $dataValidated = $validated['data'];
                        // if(!ValidateHelper::validateProperty($movement, ['transfer_guide_id'])){
                        // }
                        $tciService = new TCIService();
                        $tciResponse = $tciService->registerGRR20(FormatHelper::parseStoreTransitMovementGuide($movement, $validated['data']));
                        $response['data'] = $tciResponse['data'];
                        
                        if($tciResponse['success']){
                            $response['success'] = true;
                            $response['message'] = $tciResponse['message'];
                        }
                        else{
                            $response['errors'] = [$tciResponse['message']];
                        }
                    }
                    else{
                        $response['success'] = false;
                        $response['errors'] = $validated['errors'];
                    }
                }
                else{
                    $response['success'] = false;
                    $response['errors'] = 'Registro no encontrado';
                }
            } 
        } 
        catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function validatedData($data, $rules){
        $result = [
            'success' => false,
            'errors' => null,
            'data' => null
        ];
        $validator = new TransitMovementGuideRequest();

        if ($validator->validateStore($data, $rules)) {
            $result['data'] = $validator->getValidData();
            $result['success'] = true;
        } 
        else {
            $result['errors'] = $validator->getErrors();
        }

        return $result;
    }

    public function validatedDetail($data){
        $result = [
            'success' => false,
            'errors' => null,
            'data' => null
        ];
        foreach($data as $row){
            $validator = new TransitMovementGuideRequest();
            $status = $validator->validateStoreDetail($row);
            if ($status) {
                if(!$result['data']){
                    $result['data'] = [];
                }
                array_push($result['data'], $validator->getValidData());
                $result['success'] = true;
            } 
            else {
                $result['success'] = false;
                $result['errors'] = $validator->getErrors();
                break;
            }
        }

        return $result;
    }

    private function getRulesTransitMovementGuide($movement){
        $rules = [];
        // ent_RemitenteGRR
        if(!ValidateHelper::validateProperty($movement, ['almacen_partida.company.document'])){ 
            $rules['almacen_partida.document'] = [['required']];
        }
        if(!ValidateHelper::validateProperty($movement, ['almacen_partida.company.name'])){
            $rules['almacen_partida.name'] = [['required']];
        }
        if(!ValidateHelper::validateProperty($movement, ['almacen_partida.company.commercial_name'])){
            $rules['almacen_partida.commercial_name'] = [['required']];
        }

        // // ent_PuntoPartidaGRR
        if(!ValidateHelper::validateProperty($movement, ['almacen_partida.district.code'])){
            $rules['almacen_partida.ubigeo'] = [['required']];
        }
        if(!ValidateHelper::validateProperty($movement, ['almacen_partida.direccion_alm'])){
            $rules['almacen_partida.address'] = [['required']];
        }

        // ent_DestinatarioGRR
        if(!ValidateHelper::validateProperty($movement, ['almacen_destino.company.document_type_code'])){
            $rules['almacen_destino.document_type_code'] = [['required']];
        }
        if(!ValidateHelper::validateProperty($movement, ['almacen_destino.company.document'])){
            $rules['almacen_destino.document'] = [['required']];
        }
        if(!ValidateHelper::validateProperty($movement, ['almacen_destino.company.name'])){
            $rules['almacen_destino.name'] = [['required']];
        }
        if(!ValidateHelper::validateProperty($movement, ['almacen_destino.email_principal'])){
            $rules['almacen_destino.email_principal'] = [['required'], ['email']];
        }
        if(!ValidateHelper::validateProperty($movement, ['almacen_destino.email_secondary'])){
            $rules['almacen_destino.email_secondary'] = [['required'], ['email']];
        }

        // // ent_PuntoLlegadaGRR
        if(!ValidateHelper::validateProperty($movement, ['almacen_destino.district.code'])){
            $rules['almacen_destino.ubigeo'] = [['required']];
        }
        if(!ValidateHelper::validateProperty($movement, ['almacen_destino.direccion_alm'])){
            $rules['almacen_destino.address'] = [['required']];
        }

        // ent_General
        if(!ValidateHelper::validateProperty($movement, ['fecha_emision'])){
            $rules['fecha_emision'] = [['required']];
        }
        if(!ValidateHelper::validateProperty($movement, ['serie'])){
            $rules['serie'] = [['required']];
        }
        if(!ValidateHelper::validateProperty($movement, ['numero'])){
            $rules['numero'] = [['required']];
        }
        if(!ValidateHelper::validateProperty($movement, ['observacion'])){
            $rules['observacion'] = [['required']];
        }
        if(!ValidateHelper::validateProperty($movement, ['hora_emision'])){
            $rules['hora_emision'] = [['required']];
        }
        if(!ValidateHelper::validateProperty($movement, ['peso'])){
            $rules['peso'] = [['required']];
        }

        // ent_Transpor
        if(!ValidateHelper::validateProperty($movement, ['transporte.modalidad'])){
            $rules['transporte.modalidad'] = [['required']];
        }
        if(!ValidateHelper::validateProperty($movement, ['transporte.fecha_inicio'])){
            $rules['transporte.fecha_inicio'] = [['required']];
        }
        if(!ValidateHelper::validateProperty($movement, ['transporte.tipo_documento'])){
            $rules['transporte.tipo_documento'] = [['required']];
        }
        if(!ValidateHelper::validateProperty($movement, ['transporte.documento'])){
            $rules['transporte.documento'] = [['required']];
        }
        if(!ValidateHelper::validateProperty($movement, ['transporte.razon_social'])){
            $rules['transporte.razon_social'] = [['required']];
        }
        if(!ValidateHelper::validateProperty($movement, ['transporte.numero_mtc'])){
            $rules['transporte.numero_mtc'] = [['required']];
        }

        return $rules;
    }
}