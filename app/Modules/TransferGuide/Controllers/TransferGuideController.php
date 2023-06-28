<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);
$action = $_REQUEST["action"];

require_once __DIR__ . '/../../../../assets/util/Session.php';
require_once __DIR__ . '/../Repository/TransferGuideRepository.php';
require_once __DIR__ . '/../Requests/TransitMovementGuideRequest.php';
require_once __DIR__ . '/../Helpers/TransferGuideHelper.php';
require_once __DIR__ . '/../Requests/TransferBetweenCompanyRequest.php';
require_once __DIR__ . '/../../TransitMovement/Repository/TransitMovementRepository.php';
require_once __DIR__ . '/../../Transport/Repository/TransportRepository.php';
require_once __DIR__ . '/../../Vehicle/Repository/VehicleRepository.php';
require_once __DIR__ . '/../../TransferGuideDetail/Repository/TransferGuideDetailRepository.php';
require_once __DIR__ . '/../../Company/Repository/CompanyRepository.php';
require_once __DIR__ . '/../../Store/Repository/StoreRepository.php';
require_once __DIR__ . '/../../Provider/Repository/ProviderRepository.php';
require_once __DIR__ . '/../../Buyer/Repository/BuyerRepository.php';
require_once __DIR__ . '/../Helpers/FormatHelper.php';
require_once __DIR__ . '/../Helpers/ValidateHelper.php';
require_once __DIR__ . '/../Helpers/XMLHelper.php';
require_once __DIR__ . '/../Services/TCIService.php';
require_once __DIR__ . '/../../../Helpers/GlobalHelper.php';
require_once __DIR__ . '/../Validation/ValidationTransferGuide.php';
require_once __DIR__ . '/../../../Models/TransferGuide.php';
require_once __DIR__ . '/../../TransferGuideHistory/Repository/TransferGuideHistoryRepository.php';
$controller = new TransferGuideController();
call_user_func(array($controller,$action));


class TransferGuideController{
    private $transferGuideRepository;
    private $transitMovementRepository;
    private $transportRepository;
    private $vehicleRepository;
    private $companyRepository;
    private $providerRepository;
    private $buyerRepository;
    private $storeRepository;
    private $transferGuideDetailRepository;
    private $validationTransferGuide;

    
    private $data;
    private $id;
    private $newCode;

    public function __construct()
    {
        $this->transferGuideRepository = new TransferGuideRepository();
        $this->transitMovementRepository = new TransitMovementRepository();
        $this->transportRepository = new TransportRepository();
        $this->vehicleRepository = new VehicleRepository();
        $this->companyRepository = new CompanyRepository();
        $this->storeRepository = new StoreRepository();
        $this->providerRepository = new ProviderRepository();
        $this->buyerRepository = new BuyerRepository();
        $this->transferGuideDetailRepository = new TransferGuideDetailRepository();
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
        }

        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function show(){
        header('Content-Type: application/json');
        $response = [
            'data' => null,
            'success' => false,
            'message' => 'Error'
        ];
        try {
            $id = (int)$_GET['id'];
            $datos = $this->transferGuideRepository->findOneWithDetails($id);
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
        // try {
            $data = GlobalHelper::getPostData();
            if (json_last_error() === JSON_ERROR_NONE){
                $this->validationTransferGuide = new ValidationTransferGuide($data);
                $this->data = $this->validationTransferGuide->validate();
                if(!$this->data['errors']){
                    $this->data = $this->data['data'];
                    $this->storeData();
                    $this->updateRelations();
                    
                    if($this->data['send']){
                        $sendResponse = $this->sendTransitMovementGuide($this->id, $this->data['startStore']['establishment_id']);
                        $response['data'] = $sendResponse['data'];
                        $response['message'] = $sendResponse['message'];
                        if($sendResponse['success']){
                            $this->transferGuideRepository->update($this->id, [
                                'hash_code' => $response['data']['at_CodigoHash'],
                                'xml_name' => $response['data']['at_NombreXML']
                            ]);
                            $response['code'] = 200;
                            $response['success'] = true;
                            $response['data']['id'] = $this->id;
                        }
                        else{
                            $response['errors'] = $sendResponse['errors'];
                        }
                    }
                    else{
                        $response['code'] = 200;
                        $response['success'] = true;
                        $response['message'] = 'Información '. ($this->id?'registrada':'actualizada') . ' exitosamente.';
                        $response['data'] = [
                            'transfer_guide_id' => ($this->id)
                        ];
                    }
                }
                else{
                    $response['success'] = false;
                    $response['errors'] = $this->data['errors'];
                }
            } 
        // } 
        // catch (PDOException $e) {
        //     Session::setAttribute("error", $e->getMessage());
        //     echo json_encode($e->getMessage());
        // }

        http_response_code($response['code']);
        echo json_encode($response);
        
    }

    public function download(){
        header('Content-Type: application/json');
        
        $response = GlobalHelper::getGlobalResponse();
        // try {
            $data = GlobalHelper::getPostData();
            if (json_last_error() === JSON_ERROR_NONE){
                if(!ValidateHelper::validateProperty($data, ['id'])){
                    $response['errors'] = ['id' => 'El id es obligatorio'];
                }
                else{
                    $this->data = $this->transferGuideRepository->findOneWithDetails($data['id']);
                    if(!$this->data){
                        $response['errors'] = ['id' => 'Registro no encontrado'];
                    }
                    else if(!$this->data['flag_sent']){
                        $response['errors'] = ['sent' => 'El registro aún no ha sido enviado'];
                    }
                    else{
                        $queryResponse = $this->downloadTransferGuide($this->data);
                        $response['data'] = $queryResponse['data'];
                        $response['message'] = $queryResponse['message'];
                        if(!$queryResponse['success']){
                            $response['errors'] = $queryResponse['errors'];
                        }
                    }
                }

                if(!$this->data['errors']){
                    $response['code'] = 200;
                    $response['success'] = true;
                    $response['message'] = 'Información exitosamente.';
                    if(ValidateHelper::validateProperty($response['data'], ['ent_Resultado.at_ArchivoRI'])){
                        $response['data'] = [
                            'file' => $response['data']['ent_Resultado']['at_ArchivoRI']
                            ?"data:application/pdf;base64,{$response['data']['ent_Resultado']['at_ArchivoRI']}"
                            :null
                        ];
                    }
                }
            } 
        // } 
        // catch (PDOException $e) {
        //     Session::setAttribute("error", $e->getMessage());
        //     echo json_encode($e->getMessage());
        // }

        http_response_code($response['code']);
        echo json_encode($response);
        
    }

    public function queryOne(){
        header('Content-Type: application/json');
        
        $response = GlobalHelper::getGlobalResponse();
        // try {
            $data = GlobalHelper::getPostData();
            if (json_last_error() === JSON_ERROR_NONE){
                if(!ValidateHelper::validateProperty($data, ['id'])){
                    $response['errors'] = ['id' => 'El id es obligatorio'];
                }
                else{
                    $this->data = $this->transferGuideRepository->findOneWithDetails($data['id']);
                    if(!$this->data){
                        $response['errors'] = ['id' => 'Registro no encontrado'];
                    }
                    else if(!$this->data['flag_sent']){
                        $response['errors'] = ['sent' => 'El registro aún no ha sido enviado'];
                    }
                    else{
                        $queryResponse = $this->queryOneGRR($this->data);
                        $response['data'] = $queryResponse['data'];
                        $response['message'] = $queryResponse['message'];
                        if(!$queryResponse['success']){
                            $response['errors'] = $queryResponse['errors'];
                        }
                    }
                }

                if(!$this->data['errors']){
                    $response['code'] = 200;
                    $response['success'] = true;
                    $response['message'] = 'Información actualizada exitosamente.';
                    $result = FormatHelper::parseResponseTci($this->data, $response['data']);
                    if($result){
                        $response['data'] = $result;
                        
                        $this->transferGuideRepository->update($this->data['id'], [
                            'tci_response_code' => $result['code_response'],
                            'tci_response_type' => $result['type_response'],
                            'tci_response_description' => $result['description'],
                            'tci_response_date' => $result['date'],
                            'tci_confirm_status_response' => json_encode($result)
                        ]);

                        $transferGuideHistoryRepository = new TransferGuideHistoryRepository();
                        $transferGuideHistoryRepository->store([
                            'status' => $result['type_response'],
                            'code' => $result['code_response'],
                            'description' => $result['description'],
                            'date' => $result['date'],
                            'transfer_guide_id' => $this->data['id'],
                            'tci_confirm_status_response' => json_encode($result),
                            'created_at' => date("Y-m-d H:i:s")
                        ]);
                        
                        $tciServiceConfirm = new TCIService();
                        $tciServiceConfirm->confirmResponseSUNAT([
                            'ent_ConfirmarRespuesta' => [
                                'at_NumeroDocumentoIdentidad' => $this->data['start_store']['company']['document'],
                                'l_Comprobante' => [
                                    'en_ComprobanteConfirmarRespuesta' => [
                                        'at_Serie' => $result['serie'],
                                        'at_Numero' => $result['number'],
                                        'at_CodigoRespuesta' => $result['code_response']
                                    ]
                                ]
                            ]
                        ]);
                    }
                }
            } 
        // } 
        // catch (PDOException $e) {
        //     Session::setAttribute("error", $e->getMessage());
        //     echo json_encode($e->getMessage());
        // }

        http_response_code($response['code']);
        echo json_encode($response);
        
    }

    private function storeData(){
        if($this->data['guide']['id']){
            $this->transferGuideRepository->update($this->data['guide']['id'], $this->data['guide']);
            $this->id = $this->data['guide']['id'];
        }
        else{
            $this->id = $this->transferGuideRepository->store($this->data['guide']);
        }

        if($this->id){
            $this->storeDetails();
            $this->storeTransport();

            if($this->data['guide']['transport_modality'] == 2){
                $this->storeVehicle();
            }

            if($this->data['guide']['motive_code'] == TransferGuide::OTHER){
                $this->storeProvider();
                $this->storeBuyer();
            }
        }
    }

    private function storeDetails(){
        if($this->data['guide']['id']){
            $this->transferGuideDetailRepository->deleteBy('transfer_guide_id', $this->id);
        }
        
        for($i = 0; $i < count($this->data['detail']); $i++){
            $this->data['detail'][$i]['transfer_guide_id'] = $this->id;
            $this->data['detail'][$i]['updated_at'] = date("Y-m-d H:i:s");
            $this->transferGuideDetailRepository->store($this->data['detail'][$i]);
        }
    }

    private function storeTransport(){
        if($this->data['guide']['id']){
            $this->transportRepository->deleteBy('transfer_guide_id', $this->id);
        }

        for($i = 0; $i < count($this->data['transports']); $i++){
            $this->data['transports'][$i]['modality'] = $this->data['guide']['transport_modality'];
            $this->data['transports'][$i]['transfer_guide_id'] = $this->id;
            $this->data['transports'][$i]['created_at'] = date("Y-m-d H:i:s");
            $this->transportRepository->store($this->data['transports'][$i]);
        }
    }

    private function storeVehicle(){
        if($this->data['guide']['id']){
            $this->vehicleRepository->deleteBy('transfer_guide_id', $this->id);
        }

        for($i = 0; $i < count($this->data['vehicles']); $i++){
            $this->data['vehicles'][$i]['transfer_guide_id'] = $this->id;
            $this->data['vehicles'][$i]['created_at'] = date("Y-m-d H:i:s");
            $this->vehicleRepository->store($this->data['vehicles'][$i]);
        }
    }

    private function updateRelations(){
        $this->updateAvailableMovement();
        $this->completeStoreIni();
        $this->completeStoreDes();
    }

    private function updateAvailableMovement(){
        $movementsId = [];
        foreach($this->data['movements'] as $movement){
            if(!in_array($movement['id'], $movementsId)){
                array_push($movementsId, $movement['id']);
            }
        }
        $this->transitMovementRepository->updateAvailable(implode(",", $movementsId), 0);
    }

    private function completeStoreIni(){
        if($this->data['startStore']['update']){
            $this->storeRepository->updateBy('id_alm', $this->data['startStore']['id'], [ 'company_id' => $this->data['startStore']['company_id']]);
        }
    }

    private function completeStoreDes(){
        if($this->data['endStore']['update']){
            $this->storeRepository->updateBy('id_alm', $this->data['endStore']['id'], [ 'company_id' => $this->data['endStore']['company_id']]);
        }
    }

    private function storeProvider(){
        if($this->data['guide']['id']){
            $this->providerRepository->deleteBy('transfer_guide_id', $this->id);
        }

        if($this->data['provider']){
            $this->data['provider']['transfer_guide_id'] = $this->id;
            $this->providerRepository->store($this->data['provider']);
        }
    }

    private function storeBuyer(){
        if($this->data['guide']['id']){
            $this->buyerRepository->deleteBy('transfer_guide_id', $this->id);
        }

        if($this->data['buyer']){
            $this->data['buyer']['transfer_guide_id'] = $this->id;
            $this->buyerRepository->store($this->data['buyer']);
        }
    }

    private function sendTransitMovementGuide($id, $establishmentId){
        $response = GlobalHelper::getGlobalResponse();
        $this->newCode = null;
        $transferGuide = $this->transferGuideRepository->findOneWithDetails($id);

        if($transferGuide){
            if(!$transferGuide['serie']){
                $this->newCode = TransferGuideHelper::generateSerialNumber($establishmentId);
            }
    
            if(!$transferGuide['serie'] && !$this->newCode){
                $response['errors'] = ['guide' => 'No fue posible generar el número de serie de la guia.'];
            }
            else{
                $tciService = new TCIService();
                // echo json_encode(FormatHelper::parseStoreTransitMovementGuide($transferGuide));
                if(!$transferGuide['serie']){
                    $transferGuide['serie'] = $this->newCode['serie'];
                    $transferGuide['number'] = $this->newCode['number'];
                    $transferGuide['date_issue'] = date("Y-m-d");
                    $transferGuide['time_issue'] = date("H:i:s");
                }
                
                $tciResponse = $tciService->registerGRR20(FormatHelper::parseStoreTransitMovementGuide($transferGuide));
                $response['data'] = $tciResponse['data'];
                $response['message'] = $tciResponse['message'];
        
                $this->transferGuideRepository->update($transferGuide['id'], [
                    'flag_sent' => true,
                    'sent_attempts' => $movement['sent_attempts'] + 1,
                    'tci_send' => $tciResponse['content_send'],
                    'tci_response' => $tciResponse['original'],
                    'date_issue' => $transferGuide['date_issue'],
                    'time_issue' => $transferGuide['time_issue'],
                    'serie' => $transferGuide['serie'],
                    'number' => (int)$transferGuide['number']
                ]);
        
                if($tciResponse['success']){
                    $response['success'] = true;
                }
                else{
                    $response['errors'] = [$tciResponse['message']];
                }
            }
        }
        else{
            $response['errors'] = ['guide' => 'No se encontró guia'];
        }

        return $response;
    }

    private function downloadTransferGuide($data){
        $response = GlobalHelper::getGlobalResponse();
        $tciService = new TCIService();

        $tciResponse = $tciService->queryPdf(FormatHelper::parseDownloadPDF($data));
        $response['data'] = $tciResponse['data'];
        $response['message'] = $tciResponse['message'];
        
        if($tciResponse['success']){
            $response['success'] = true;
        }
        else{
            $response['errors'] = [$tciResponse['message']];
        }

        return $response;
    }

    private function queryOneGRR($data){
        $response = GlobalHelper::getGlobalResponse();
        $tciService = new TCIService();

        $tciResponse = $tciService->queryOneGRR(FormatHelper::parseQueryOneGRR($data));
        $response['data'] = $tciResponse['data'];
        $response['message'] = $tciResponse['message'];
        
        if($tciResponse['success']){
            $response['success'] = true;
        }
        else{
            $response['errors'] = [$tciResponse['message']];
        }

        return $response;
    }

    

    

    

    

    


















    

    public function storeDevolutionGuide(){
        header('Content-Type: application/json');
        $response = GlobalHelper::getGlobalResponse();
        try {
            $data = GlobalHelper::getPostData();
            if (json_last_error() === JSON_ERROR_NONE){
                // TEST
                $movement = [
                    'detalle' => [
                        [
                            'cant_mde' => '1.00',
                            'um_sunat_code' => 'SET',
                            'des_mde' => 'TORQUIMETRO',
                            'cod_inv' => '92932'
                        ],
                        [
                            'cant_mde' => '1.00',
                            'um_sunat_code' => 'SET',
                            'des_mde' => 'TELUROMETRO',
                            'cod_inv' => 'C0042'
                        ],
                        [
                            'cant_mde' => '1.00',
                            'um_sunat_code' => 'SET',
                            'des_mde' => 'RUGOSIMETRO',
                            'cod_inv' => 'C0182'
                        ]
                    ]
                ];
                if($data['send']){
                    $tciService = new TCIService();
                    $tciResponse = $tciService->registerGRR20(FormatHelper::parseStoreDevolutionGuide($movement, $data));
                    $response['data'] = $tciResponse['data'];
                    $response['message'] = $tciResponse['message'];
                }
                // END TEST
            } 
        } 
        catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
        }

        http_response_code($response['code']);
        echo json_encode($response);
    }

    public function storeOthersGuide(){
        header('Content-Type: application/json');
        $response = GlobalHelper::getGlobalResponse();
        try {
            $data = GlobalHelper::getPostData();
            if (json_last_error() === JSON_ERROR_NONE){
                // TEST
                $movement = [
                    'detalle' => [
                        [
                            'cant_mde' => '1.00',
                            'um_sunat_code' => 'SET',
                            'des_mde' => 'TORQUIMETRO',
                            'cod_inv' => '92932'
                        ],
                        [
                            'cant_mde' => '1.00',
                            'um_sunat_code' => 'SET',
                            'des_mde' => 'TELUROMETRO',
                            'cod_inv' => 'C0042'
                        ],
                        [
                            'cant_mde' => '1.00',
                            'um_sunat_code' => 'SET',
                            'des_mde' => 'RUGOSIMETRO',
                            'cod_inv' => 'C0182'
                        ]
                    ]
                ];
                if($data['send']){
                    $tciService = new TCIService();
                    $tciResponse = $tciService->registerGRR20(FormatHelper::parseStoreOthersGuide($movement, $data));
                    $response['data'] = $tciResponse['data'];
                    $response['message'] = $tciResponse['message'];
                }
                // END TEST
            } 
        } 
        catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
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
                        'at_CantidadConsultar' => 300
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
        }

        http_response_code($response['code']);
        echo json_encode($response);
    }

    public function confirmResponseSUNAT(){
        header('Content-Type: application/json');
        $response = GlobalHelper::getGlobalResponse();
        try {
            $data = GlobalHelper::getPostData();
            if (json_last_error() === JSON_ERROR_NONE){
                $tciService = new TCIService();
                $tciResponse = $tciService->confirmResponseSUNAT([
                    'ent_ConfirmarRespuesta' => [
                        'at_NumeroDocumentoIdentidad' => '20357259976',
                        'l_Comprobante' => [
                            'en_ComprobanteConfirmarRespuesta' => [
                                'at_Serie' => 'T001',
                                'at_Numero' => '2',
                                'at_CodigoRespuesta' => '1'
                            ]
                        ]
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
                            'at_Numero' => '116',
                            'at_NumeroRespuesta' => 1
                        ]
                    ]
                ]);
                
                $response['data'] = $tciResponse['data'];
                $response['message'] = $tciResponse['message'];

                if($tciResponse['success']){
                    $response['data'] = $this->formatQueryXML($response['data']);
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
            $modality = $data['transport_modality'];
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
            $modality = $data['transport_modality'];
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

    private function formatQueryXML($data){
        $dataResponse = [];

        if(isset($data['ent_ResultadoXML'])){
            if(isset($data['ent_ResultadoXML']['at_XML'])){
                $binaryData = base64_decode($data['ent_ResultadoXML']['at_XML']);
                $xml = simplexml_load_string($binaryData);
                $dataResponse['xml'] = $xml->asXML();
            }

        }

        return $dataResponse;
    }
}