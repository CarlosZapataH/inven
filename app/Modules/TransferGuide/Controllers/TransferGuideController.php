<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);
$action = $_REQUEST["action"];

require_once __DIR__ . '/../../../../assets/util/Session.php';
require_once __DIR__ . '/../Repository/TransferGuideRepository.php';
require_once __DIR__ . '/../Requests/TransitMovementGuideRequest.php';
require_once __DIR__ . '/../../TransitMovement/Repository/TransitMovementRepository.php';
require_once __DIR__ . '/../Helpers/FormatHelper.php';
require_once __DIR__ . '/../Helpers/ValidateHelper.php';
require_once __DIR__ . '/../Helpers/XMLHelper.php';
require_once __DIR__ . '/../Services/TCIService.php';
require_once __DIR__ . '/../../../Helpers/GlobalHelper.php';
$controller = new TransferGuideController();
call_user_func(array($controller,$action));


class TransferGuideController{
    private $transferGuideRepository;
    private $transitMovementRepository;

    public function __construct()
    {
        $this->transferGuideRepository = new TransferGuideRepository();
        $this->transitMovementRepository = new TransitMovementRepository();
    }

    public function index(){
        $response = [
            'data' => null,
            'success' => false,
            'message' => 'Error'
        ];
        try {
            $filters = GlobalHelper::getUrlData();
            unset($filters['action']);
            $data = $this->transferGuideRepository->findWithPaginate($filters);
            $response['success'] = true;
            $response['data'] = $data;
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
        header('Content-Type: application/json');
        $response = GlobalHelper::getGlobalResponse();
        try {
            $data = GlobalHelper::getPostData();
            if (json_last_error() === JSON_ERROR_NONE){
                $id = (int)$_GET['id'];
                $movement = $this->transitMovementRepository->getTransitMovement($id);
                if($movement){
                    $rules = $this->getRulesTransitMovementGuide($movement);
                    $validated = $this->validatedData($data, $rules);
 
                    if($validated['success']){
                        $transferGuideId = null;

                        if(!ValidateHelper::validateProperty($movement, ['transfer_guide_id'])){
                            $validated['data']['created_at'] = date("Y-m-d H:i:s");
                            $transferGuideId = $this->transferGuideRepository->store($this->groupStoreTransitMovementGuide($movement, $validated['data']));
                        }
                        else{
                            $validated['data']['updated_at'] = date("Y-m-d H:i:s");
                            $this->transferGuideRepository->update($movement['transfer_guide_id'], $this->groupStoreTransitMovementGuide($movement, $validated['data']));
                        }

                        $send = false;
                        if(isset($data['send'])){
                            if($data['send'] == 1){
                                $send = true;
                            }
                        }

                        if($send){
                            $sendResponse = $this->sendTransitMovementGuide($id, $validated['data']);
                            $response['data'] = $sendResponse['data'];
                            $response['message'] = $sendResponse['message'];
                            if($sendResponse['success']){
                                $response['code'] = 200;
                                $response['success'] = true;
                            }
                            else{
                                $response['errors'] = $sendResponse['errors'];
                            }
                        }
                        else{
                            $response['code'] = 200;
                            $response['success'] = true;
                            $response['message'] = 'Información '. ($transferGuideId?'registrada':'actualizada') . ' exitosamente.';
                            $response['data'] = [
                                'transfer_guide_id' => ($transferGuideId?$transferGuideId:$movement['transfer_guide_id'])
                            ];
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
            echo $e->getMessage();
        }

        http_response_code($response['code']);
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
        $existGuide = ValidateHelper::validateProperty($movement, ['transfer_guide_id']);

        // $rules['send'] = [['required']];

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
        if(!ValidateHelper::validateProperty($movement, ['almacen_destino.email_principal']) || $existGuide){
            $rules['almacen_destino.email_principal'] = [['required'], ['email']];
        }
        if(!ValidateHelper::validateProperty($movement, ['almacen_destino.email_secondary']) || $existGuide){
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
        if(!ValidateHelper::validateProperty($movement, ['fecha_emision']) || $existGuide){
            $rules['fecha_emision'] = [['required']];
        }
        if(!ValidateHelper::validateProperty($movement, ['serie']) || $existGuide){
            $rules['serie'] = [['required']];
        }
        if(!ValidateHelper::validateProperty($movement, ['numero']) || $existGuide){
            $rules['numero'] = [['required']];
        }
        if(!ValidateHelper::validateProperty($movement, ['observacion']) || $existGuide){
            $rules['observacion'] = [['required']];
        }
        if(!ValidateHelper::validateProperty($movement, ['hora_emision']) || $existGuide){
            $rules['hora_emision'] = [['required']];
        }
        if(!ValidateHelper::validateProperty($movement, ['peso']) || $existGuide){
            $rules['peso'] = [['required']];
        }
        if(!ValidateHelper::validateProperty($movement, ['cantidad']) || $existGuide){
            $rules['cantidad'] = [['required']];
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

    private function groupStoreTransitMovementGuide($movement, $data){
        $groupData = [
            'serie' => $data['serie'],
            'number' => $data['numero'],
            'date_issue' => $data['fecha_emision'],
            'time_issue' => $data['hora_emision'],
            'observations' => $data['observacion'],
            'motive_code' => '04',
            'motive_description' => 'Traslado entre establecimientos de la misma empresa',
            'total_witght' => $data['peso'],
            'unit_measure' => 'KGM',
            'total_quantity' => $data['cantidad'],
            'email_principal' => $data['almacen_destino']['email_principal'],
            'email_secondary' => $data['almacen_destino']['email_secondary'],
            'movement_id' => $movement['id_movt']
        ];

        if(isset($data['created_at'])){
            $groupData['created_at'] = $data['created_at'];
        }

        if(isset($data['updated_at'])){
            $groupData['updated_at'] = $data['updated_at'];
        }

        return $groupData;
    }

    private function sendTransitMovementGuide($id, $data = []){
        $response = GlobalHelper::getGlobalResponse();
        $movement = $this->transitMovementRepository->getTransitMovement($id);
        $tciService = new TCIService();
        $tciResponse = $tciService->registerGRR20(FormatHelper::parseStoreTransitMovementGuide($movement, $data));
        $response['data'] = $tciResponse['data'];
        $response['message'] = $tciResponse['message'];
        // echo json_encode(FormatHelper::parseStoreTransitMovementGuide($movement, $data));
        $this->transferGuideRepository->update($movement['transfer_guide_id'], [
            'flag_sent' => true,
            'sent_attempts' => $movement['sent_attempts'] + 1,
            'tci_send' => $tciResponse['content_send'],
            'tci_response' => $tciResponse['original']
        ]);

        if($tciResponse['success']){
            $response['success'] = true;
        }
        else{
            $response['errors'] = [$tciResponse['message']];
        }

        return $response;
    }
}