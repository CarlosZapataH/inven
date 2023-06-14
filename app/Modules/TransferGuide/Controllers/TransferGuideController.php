<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);
$action = $_REQUEST["action"];

require_once __DIR__ . '/../../../../assets/util/Session.php';
require_once __DIR__ . '/../Repository/TransferGuideRepository.php';
require_once __DIR__ . '/../Requests/TransitMovementGuideRequest.php';
require_once __DIR__ . '/../../TransitMovement/Repository/TransitMovementRepository.php';
require_once __DIR__ . '/../../Transport/Repository/TransportRepository.php';
require_once __DIR__ . '/../../Vehicle/Repository/VehicleRepository.php';
require_once __DIR__ . '/../../Company/Repository/CompanyRepository.php';
require_once __DIR__ . '/../../Store/Repository/StoreRepository.php';
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
    private $transportRepository;
    private $vehicleRepository;
    private $companyRepository;
    private $storeRepository;

    public function __construct()
    {
        $this->transferGuideRepository = new TransferGuideRepository();
        $this->transitMovementRepository = new TransitMovementRepository();
        $this->transportRepository = new TransportRepository();
        $this->vehicleRepository = new VehicleRepository();
        $this->companyRepository = new CompanyRepository();
        $this->storeRepository = new StoreRepository();
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
                            $this->storeTransport($data['modalidad_transporte'], $validated['data'], $transferGuideId);
                            $this->storeVehicle($validated['data'], $transferGuideId);
                        }
                        else{
                            $validated['data']['updated_at'] = date("Y-m-d H:i:s");
                            $this->transferGuideRepository->update($movement['transfer_guide_id'], $this->groupStoreTransitMovementGuide($movement, $validated['data']));
                            $this->storeTransport($data['modalidad_transporte'], $validated['data'], $movement['transfer_guide_id'], true);
                            $this->storeVehicle($validated['data'], $movement['transfer_guide_id'], true);
                        }

                        $this->updateRelations($movement, $validated['data']);

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
                                $this->transferGuideRepository->update(($transferGuideId?$transferGuideId:$movement['transfer_guide_id']), [
                                    'hash_code' => $response['data']['at_CodigoHash'],
                                    'xml_name' => $response['data']['at_NombreXML']
                                ]);
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
            echo json_encode($e->getMessage());
        }

        http_response_code($response['code']);
        echo json_encode($response);
    }

    public function queryStatusTCI(){
        header('Content-Type: application/json');
        $response = GlobalHelper::getGlobalResponse();
        try {
            $data = GlobalHelper::getPostData();
            if (json_last_error() === JSON_ERROR_NONE){
                $tciService = new TCIService();
                $tciResponse = $tciService->queryStatusGRR20([
                    'ent_ConsultarEstado' => [
                        'at_NumeroDocumentoIdentidad' => '20357259976',
                        'at_CantidadConsultar' => 10
                    ]
                ]);
                
                $response['data'] = $tciResponse['data'];
                $response['message'] = $tciResponse['message'];

                if($tciResponse['success']){
                    $response['data'] = $this->formatQueryStatus($response['data']);
                    $response['code'] = 200;
                    $response['success'] = true;
                }
                else{
                    $response['errors'] = [$tciResponse['message']];
                }
            } 
        } 
        catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
            echo json_encode($e->getMessage());
        }

        http_response_code($response['code']);
        echo json_encode($response);
    }

    public function queryStatusSUNAT(){
        header('Content-Type: application/json');
        $response = GlobalHelper::getGlobalResponse();
        try {
            $data = GlobalHelper::getPostData();
            if (json_last_error() === JSON_ERROR_NONE){
                $tciService = new TCIService();
                $tciResponse = $tciService->queryStatusSUNAT([
                    'ent_ConsultarRespuesta' => [
                        'at_NumeroDocumentoIdentidad' => '20357259976',
                        'at_CantidadConsultar' => 50
                    ]
                ]);
                
                $response['data'] = $tciResponse['data'];
                $response['message'] = $tciResponse['message'];

                if($tciResponse['success']){
                    $response['data'] = $response['data'];
                    $response['code'] = 200;
                    $response['success'] = true;
                }
                else{
                    $response['errors'] = [$tciResponse['message']];
                }
            } 
        } 
        catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
            echo json_encode($e->getMessage());
        }

        http_response_code($response['code']);
        echo json_encode($response);
    }

    public function queryXML(){
        header('Content-Type: application/json');
        $response = GlobalHelper::getGlobalResponse();
        try {
            $data = GlobalHelper::getPostData();
            if (json_last_error() === JSON_ERROR_NONE){
                $tciService = new TCIService();
                $tciResponse = $tciService->queryXML([
                    'ent_ConsultarXML' => [
                        'at_NumeroDocumentoIdentidad' => '20357259976',
                        'ent_ComprobanteConsultarXML' => [
                            'at_Serie' => 'T001',
                            'at_Numero' => '115',
                            'at_NumeroRespuesta' => 10
                        ]
                    ]
                ]);
                
                $response['data'] = $tciResponse['data'];
                $response['message'] = $tciResponse['message'];

                if($tciResponse['success']){
                    $response['data'] = $this->formatQueryStatus($response['data']);
                    $response['code'] = 200;
                    $response['success'] = true;
                }
                else{
                    $response['errors'] = [$tciResponse['message']];
                }
            } 
        } 
        catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
            echo json_encode($e->getMessage());
        }

        http_response_code($response['code']);
        echo json_encode($response);
    }

    // PROCESS

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
        if(!$result['errors']){
            $modality = $data['modalidad_transporte'];
            $result['data']['transports'] = [];
            if(isset($data['transports'])){
                if(is_array($data['transports'])){
                    foreach($data['transports'] as $rowTransport){
                        $validatorTs = new TransitMovementGuideRequest();
                        if($modality == 1){
                            $resultTransport = $validatorTs->validatedTransportPublic($rowTransport);
                        }
                        else{
                            $resultTransport = $validatorTs->validatedTransportPrivate($rowTransport);
                        }
                        if($resultTransport){
                            array_push($result['data']['transports'], $validatorTs->getValidData());
                        }
                        else{
                            $result['errors'] = $validatorTs->getErrors();
                            $result['success'] = false;
                            break;
                        }
                    }
                }
            }
            else{
                $result['errors'] = [
                    "transports" => ["El transporte es obligatorio."]
                ];
            }
        }
        

        if(!$result['errors']){
            $modality = $data['modalidad_transporte'];
            $result['data']['vehicles']  = [];
            if($modality == 2){
                if(isset($data['vehicles'])){
                    foreach($data['vehicles'] as $rowVehicle){
                        $validatorV = new TransitMovementGuideRequest();

                        $resultVehicle = $validatorV->validatedVehicle($rowVehicle);
                      
                        if($resultVehicle){
                            array_push($result['data']['vehicles'], $validatorV->getValidData());
                        }
                        else{
                            $result['errors'] = $validatorV->getErrors();
                            $result['success'] = false;
                            break;
                        }
                    }
                }
                else{
                    $result['errors'] = [
                        "vehicles" => ["Los vehículos son obligatorios."]
                    ];
                }
            }
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

    public function validatedTransportPublic($data){
        $result = [
            'success' => false,
            'errors' => null,
            'data' => null
        ];
        foreach($data as $row){
            $validator = new TransitMovementGuideRequest();
            $status = $validator->validateStoreTransportPublic($row);
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

    public function validatedTransportPrivate($data){
        $result = [
            'success' => false,
            'errors' => null,
            'data' => null
        ];
        foreach($data as $row){
            $validator = new TransitMovementGuideRequest();
            $status = $validator->validateStoreTransportPrivate($row);
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

    public function validatedVehicle($data){
        $result = [
            'success' => false,
            'errors' => null,
            'data' => null
        ];
        foreach($data as $row){
            $validator = new TransitMovementGuideRequest();
            $status = $validator->validateStoreVehicle($row);
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
        if(!ValidateHelper::validateProperty($movement, ['almacen_partida.company.id'])){
            $rules['almacen_partida.document_type_id'] = [['required']];
        }
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
        if(!ValidateHelper::validateProperty($movement, ['almacen_destino.company.id'])){
            $rules['almacen_destino.document_type_id'] = [['required']];
        }
        if(!ValidateHelper::validateProperty($movement, ['almacen_destino.district.code'])){
            $rules['almacen_destino.ubigeo'] = [['required']];
        }
        if(!ValidateHelper::validateProperty($movement, ['almacen_destino.direccion_alm'])){
            $rules['almacen_destino.address'] = [['required']];
        }

        // ent_General
        if(!ValidateHelper::validateProperty($movement, ['modalidad_transporte']) || $existGuide){
            $rules['modalidad_transporte'] = [['required']];
        }
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

        if(!ValidateHelper::validateProperty($movement, ['modalidad_transporte']) || $existGuide){
            $rules['modalidad_transporte'] = [['required']];
        }

        // ent_Transpor
       /*  if(!ValidateHelper::validateProperty($movement, ['transporte.modalidad'])){
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
        } */

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
            'movement_id' => $movement['id_movt'],
            'transport_modality' => $data['modalidad_transporte']
        ];

        if(isset($data['created_at'])){
            $groupData['created_at'] = $data['created_at'];
        }

        if(isset($data['updated_at'])){
            $groupData['updated_at'] = $data['updated_at'];
        }

        return $groupData;
    }

    private function groupStoreTransitMovementGuideTransport($data, $transferGuideId){
        $groupData = ['modality' => $modality, 'transfer_guide_id' => $transferGuideId];
        
        if($modality == 1){
            if($data['start_date']){
                $groupData['start_date'] = $data['start_date'];
            }
    
            if($data['document_type']){
                $groupData['document_type'] = $data['document_type'];
            }
    
            if($data['document']){
                $groupData['document'] = $data['transporte']['document'];
            }
    
            if($data['company_name']){
                $groupData['company_name'] = $data['transporte']['company_name'];
            }
    
            if($data['mtc_number']){
                $groupData['mtc_number'] = $data['transporte']['mtc_number'];
            }
        }

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
        // echo json_encode(FormatHelper::parseStoreTransitMovementGuide($movement, $data));
        $tciResponse = $tciService->registerGRR20(FormatHelper::parseStoreTransitMovementGuide($movement, $data));
        $response['data'] = $tciResponse['data'];
        $response['message'] = $tciResponse['message'];
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

    private function storeTransport($modality, $data, $transferGuideId, $update = false){
        if($transferGuideId){
            if($update){
                $this->transportRepository->deleteBy('transfer_guide_id', $transferGuideId);
            }

            if(count($data['transports'])){
                foreach($data['transports'] as $transport){
                    $transport['modality'] = $modality;
                    $transport['transfer_guide_id'] = $transferGuideId;
                    $transport['created_at'] = date("Y-m-d H:i:s");
                    $this->transportRepository->store($transport);
                }
            }
        }
    }

    private function storeVehicle($data, $transferGuideId, $update = false){
        if($transferGuideId && isset($data['vehicles'])){
            if($update){
                $this->vehicleRepository->deleteBy('transfer_guide_id', $transferGuideId);
            }
            
            if(count($data['vehicles'])){
                foreach($data['vehicles'] as $vehicle){
                    $vehicle['transfer_guide_id'] = $transferGuideId;
                    $vehicle['created_at'] = date("Y-m-d H:i:s");
                    $this->vehicleRepository->store($vehicle);
                }
            }
        }
    }
    
    private function updateRelations($movement, $data){
        
        $this->completeStoreIni($movement, $data);
        $this->completeStoreDes($movement, $data);
    }

    private function completeStoreIni($movement, $data){
        if(!ValidateHelper::validateProperty($movement, ['almacen_partida.company.id'])){
            $validateCompanyIni = $this->companyRepository->validateCompany($data['almacen_partida']['document_type_id'], $data['almacen_partida']['document']);
            $companyIniId = null;

            if(!$validateCompanyIni){
                $companyIni = [
                    'name' => $data['almacen_partida']['name'],
                    'commercial_name' => $data['almacen_partida']['commercial_name'],
                    'document' => $data['almacen_partida']['document'],
                    'document_type_id' => $data['almacen_partida']['document_type_id']
                ];
                $companyIniId = $this->companyRepository->store($companyIni);
            }
            else{
                if(!ValidateHelper::validateProperty($validateCompanyIni, ['commercial_name'])){
                    $this->companyRepository->update($validateCompanyIni['id'], [ 'commercial_name' => $data['almacen_partida']['commercial_name']]);
                }

                $companyIniId = $validateCompanyIni['id'];
            }

            if($companyIniId){
                $this->storeRepository->updateBy('id_alm', $movement['almacen_partida']['id'], [ 'company_id' => $companyIniId]);
            }
        }
    }

    private function completeStoreDes($movement, $data){
        if(!ValidateHelper::validateProperty($movement, ['almacen_destino.company.id'])){
            $validateCompanyDes = $this->companyRepository->validateCompany($data['almacen_destino']['document_type_id'], $data['almacen_destino']['document']);
            $companyDesId = null;

            if(!$validateCompanyDes){
                $companyDes = [
                    'name' => $data['almacen_destino']['name'],
                    'commercial_name' => $data['almacen_destino']['commercial_name'],
                    'document' => $data['almacen_destino']['document'],
                    'document_type_id' => $data['almacen_destino']['document_type_id']
                ];
                $companyDesId = $this->companyRepository->store($companyDes);
            }
            else{
                if(!ValidateHelper::validateProperty($validateCompanyDes, ['commercial_name'])){
                    $this->companyRepository->update($validateCompanyDes['id'], [ 'commercial_name' => $data['almacen_destino']['commercial_name']]);
                }

                $companyDesId = $validateCompanyDes['id'];
            }

            if($companyDesId){
                $this->storeRepository->updateBy('id_alm', $movement['almacen_destino']['id'], [ 'company_id' => $companyDesId]);
            }
        }
    }

    private function formatQueryStatus($data){
        $documents = [];

        if(isset($data['l_ResultadoEstadoComprobante'])){
            if(isset($data['l_ResultadoEstadoComprobante']['en_ResultadoEstadoComprobanteGR'])){
                foreach($data['l_ResultadoEstadoComprobante']['en_ResultadoEstadoComprobanteGR'] as $row){
                    $item = [
                        'serie' => $row['at_Serie'],
                        'number' => $row['at_Numero'],
                        'status_granted' => false,
                        'status_granted_date' => null,
                        'status_read' => false,
                        'status_read_date' => null
                    ];

                    if(isset($row['ent_EstadoOtorgado'])){
                        if(isset($row['ent_EstadoOtorgado']['at_FechaOtorgado'])){
                            if(!empty($row['ent_EstadoOtorgado']['at_FechaOtorgado'])){
                                $item['status_granted'] = true;
                                $item['status_granted_date'] = $row['ent_EstadoOtorgado']['at_FechaOtorgado'];
                            }
                        }
                    }

                    if(isset($row['ent_EstadoLeido'])){
                        if(isset($row['ent_EstadoLeido']['at_FechaLeido'])){
                            if(!empty($row['ent_EstadoLeido']['at_FechaLeido'])){
                                $item['status_read'] = true;
                                $item['status_read_date'] = $row['ent_EstadoLeido']['at_FechaLeido'];
                            }
                        }
                    }

                    array_push($documents, $item);
                }
            }

        }

        return $documents;
    }
}