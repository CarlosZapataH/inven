<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);
$action = $_REQUEST["action"];

require_once __DIR__ . '/../../../../assets/util/Session.php';
require_once __DIR__ . '/../Repository/TransferGuideRepository.php';
require_once __DIR__ . '/../Requests/TransitMovementGuideRequest.php';
require_once __DIR__ . '/../Requests/TransferBetweenCompanyRequest.php';
require_once __DIR__ . '/../../TransitMovement/Repository/TransitMovementRepository.php';
require_once __DIR__ . '/../../Transport/Repository/TransportRepository.php';
require_once __DIR__ . '/../../Vehicle/Repository/VehicleRepository.php';
require_once __DIR__ . '/../../TransferGuideDetail/Repository/TransferGuideDetailRepository.php';
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
    private $transferGuideDetailRepository;

    private $data;
    private $movements;
    private $startStore;
    private $endStore;

    private $dataGuide;
    private $dataStartCompany;
    private $dataEndCompany;
    private $detailsData;

    public function __construct()
    {
        $this->transferGuideRepository = new TransferGuideRepository();
        $this->transitMovementRepository = new TransitMovementRepository();
        $this->transportRepository = new TransportRepository();
        $this->vehicleRepository = new VehicleRepository();
        $this->companyRepository = new CompanyRepository();
        $this->storeRepository = new StoreRepository();
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
            echo $e->getMessage();
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

    public function storeTransferBetweenSameCompany(){
        header('Content-Type: application/json');
        $response = GlobalHelper::getGlobalResponse();
        // try {
            $this->data = GlobalHelper::getPostData();
            if (json_last_error() === JSON_ERROR_NONE){
                $errors = $this->validateMovementsBetweenCompany();
                if(count($errors) == 0){ 
                    $this->completeGuide();
                    if(!ValidateHelper::validateProperty($this->data, ['id'])){
                        $transferGuideId = $this->transferGuideRepository->store($this->dataGuide);
                        if($transferGuideId){
                            $this->storeDetails($this->data['detail'], $transferGuideId);
                            $this->storeTransport($this->data['transports'], $transferGuideId);
                            $this->storeVehicle($this->data['vehicles'], $transferGuideId);
                        }
                    }
                    else{
                        $this->transferGuideRepository->update($this->dataGuide);
                        $this->storeDetails($this->data['detail'], $this->data['id']);
                        $this->storeTransport($this->data['transports'], $this->data['id'], true);
                        $this->storeVehicle($this->data['vehicles'], $this->data['id'], true);
                    }

                    $this->updateRelations();

                    $send = false;
                    if(isset($this->data['send'])){
                        if($this->data['send'] == 1){
                            $send = true;
                        }
                    }

                    if($send){
                        $sendResponse = $this->sendTransitMovementGuide($transferGuideId?$transferGuideId:$this->data['id']);
                        $response['data'] = $sendResponse['data'];
                        $response['message'] = $sendResponse['message'];
                        if($sendResponse['success']){
                            $this->transferGuideRepository->update(($transferGuideId?$transferGuideId:$this->data['id']), [
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
                            'transfer_guide_id' => ($transferGuideId?$transferGuideId:$this->data['id'])
                        ];
                    }
                }
                else{
                    $response['success'] = false;
                    $response['errors'] = $errors;
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

    public function storeTransitMovementGuide2(){
        header('Content-Type: application/json');
        $response = GlobalHelper::getGlobalResponse();
        try {
            $data = GlobalHelper::getPostData();
            if (json_last_error() === JSON_ERROR_NONE){
                $id = (int)$_GET['id'];
                $movement = $this->transitMovementRepository->findOneWithDetails($id);
                
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

    public function validateMovementsBetweenCompany(){
        $this->movements = [];
        $this->dataGuide = null;
        $errors = [];
        $ids = [];
        if(isset($this->data['detail'])){
            if(is_array($this->data['detail'])){
                foreach($this->data['detail'] as $detail){
                    if(!in_array($detail['movement_id'], $ids)){
                        array_push($ids, $detail['movement_id']);
                    }
                }
            }
        }

        if(count($ids) > 0){
            $response = $this->transitMovementRepository->findWithDetails(implode(",", $ids));
            if(count($response['data']) > 0){
                if(count($response['start_store_id']) > 1){
                    array_push($errors, 'Todos los elementos deben pertenecer al mismo almacén de partida');
                }
                else if(count($response['end_store_id']) > 1){
                    array_push($errors, 'Todos los elementos deben pertenecer al mismo almacén de llegada');
                }
            }
            else{
                array_push($errors, 'No se encontraron registros de inventario');
            }
        }

        if(count($errors) == 0){
            $this->movements = $response['data'];
            $this->startStore = $response['start_store'];
            $this->endStore = $response['end_store'];

            $errors = $this->validateBetweenCompany();
        }
        return $errors;
    }

    public function validateBetweenCompany(){
        $errors = [];

        // GRUIDE
        $validateGuide = $this->validateGuideBetweenCompany();

        if($validateGuide['errors']){
            $errors = array_merge($errors, $validateGuide['errors']);
        }
        else{
            $this->dataGuide = $validateGuide['data'];
        }

        // EMAILS STORE
        $validateEmailsStoreCompany = $this->validateEmailsStoreCompany();

        if($validateEmailsStoreCompany['errors']){
            $errors = array_merge($errors, $validateEmailsStoreCompany['errors']);
        }

        // START STORE COMPANY
        $validateStartStoreCompany = $this->validateStartStoreCompany();

        if($validateStartStoreCompany['errors']){
            $errors = array_merge($errors, $validateStartStoreCompany['errors']);
        }
        else{
            $this->dataStartCompany = $validateStartStoreCompany['data'];
        }

        // END STORE COMPANY
        $validateEndStoreCompany = $this->validateEndStoreCompany();

        if($validateEndStoreCompany['errors']){
            $errors = array_merge($errors, $validateEndStoreCompany['errors']);
        }
        else{
            $this->dataEndCompany = $validateEndStoreCompany['data'];
        }

        // DETAILS
        $validateDetails = $this->validateDetails();

        if($validateDetails['errors']){
            $errors = array_merge($errors, $validateDetails['errors']);
        }

        // TRANSPORT
        $validateTransport = $this->validatedTransport();

        if($validateTransport['errors']){
            $errors = array_merge($errors, $validateTransport['errors']);
        }

        // VEHICLE
        $validateVehicle = $this->validateVehicle();

        if($validateVehicle['errors']){
            $errors = array_merge($errors, $validateVehicle['errors']);
        }

        // VEHICLE
        /* $validateExistStartCompany = $this->validateExistStartCompany();

        if($validateExistStartCompany['errors']){
            $errors = array_merge($errors, $validateExistStartCompany['errors']);
        }

        $validateExistEndCompany = $this->validateExistEndCompany();

        if($validateExistEndCompany['errors']){
            $errors = array_merge($errors, $validateExistEndCompany['errors']);
        } */

        return $errors;
    }

    public function validateEmailsStoreCompany(){
        $result = [
            'success' => false,
            'errors' => null,
            'data' => null
        ];
            
        if(!ValidateHelper::validateProperty($this->data, ['end_store'])){
            $result['errors'] = ['La informacion de la empresa del almacén de llegada es obligatoria.'];
        }
        else{
            if(!ValidateHelper::validateProperty($this->data['end_store'], ['email_principal'])){
                $result['errors'] = ['El correo electrónico del almacén de llegada es obligatorio.'];
            }

            if(!ValidateHelper::validateProperty($this->data['end_store'], ['email_secondary'])){
                $message  = 'El correo electrónico del almacén de llegada es obligatorio.';
                if($result['errors']){
                    array_push($result['errors'], $message);
                }
                else{
                    $result['errors'] = [$message];
                }
            }
        }

        return $result;
    }

    public function validateGuideBetweenCompany(){
        $result = [
            'success' => false,
            'errors' => null,
            'data' => null
        ];
        $validator = new TransferBetweenCompanyRequest();

        if ($validator->validateGuide($this->data)) {
            $result['data'] = $validator->getValidData();
            $result['success'] = true;
        } 
        else {
            $result['errors'] = $validator->getErrors();
        }

        return $result;
    }

    public function validateStartStoreCompany(){
        $result = [
            'success' => false,
            'errors' => null,
            'data' => null
        ];

        if(!$this->startStore){
            if(ValidateHelper::validateProperty($this->data, ['start_store.company_id'])){
                $result['errors'] = ['La informacion de la empresa del almacén de partida es obligatoria.'];
            }
        }

        return $result;
    }

    public function validateEndStoreCompany(){
        $result = [
            'success' => false,
            'errors' => null,
            'data' => null
        ];

        if(!$this->endStore){
            if(!ValidateHelper::validateProperty($this->data, ['end_store.company_id'])){
                $result['errors'] = ['La informacion de la empresa del almacén de llegada es obligatoria.'];
            }
        }

        return $result;
    }

    private function getRulesCompany(){
        $rules = [];

        // ent_RemitenteGRR
        if(!$this->startStore){
            $rules = [
                'name' => [['required']],
                'commercial_name' => [['required']],
                'document_type_id' => [['required']],
                'document' => [['required']]
            ];
        }
        else{
            if(!ValidateHelper::validateProperty($this->startStore, ['company.name'])){ 
                $rules['name'] = [['required']];
            }
            if(!ValidateHelper::validateProperty($this->startStore, ['company.commercial_name'])){
                $rules['commercial_name'] = [['required']];
            }
            if(!ValidateHelper::validateProperty($this->startStore, ['company.document_type_id'])){
                $rules['document_type_id'] = [['required']];
            }
            if(!ValidateHelper::validateProperty($this->startStore, ['company.document'])){
                $rules['document'] = [['required']];
            }
        }
        
        return $rules;
    }

    private function isCompleteStartCompany(){
        $completed = true;

        if(!$this->startStore){
            $completed = false;
        }
        else{
            if(
                !ValidateHelper::validateProperty($this->startStore, ['company.name']) || 
                !ValidateHelper::validateProperty($this->startStore, ['company.commercial_name']) || 
                !ValidateHelper::validateProperty($this->startStore, ['company.document_type_id']) || 
                !ValidateHelper::validateProperty($this->startStore, ['company.document'])
            ){
                $completed = false;
            }
        }

        return $completed;

    }

    private function isCompleteEndCompany(){
        $completed = true;

        if(!$this->endStore){
            $completed = false;
        }
        else{
            if(
                !ValidateHelper::validateProperty($this->endStore, ['company.name']) || 
                !ValidateHelper::validateProperty($this->endStore, ['company.commercial_name']) || 
                !ValidateHelper::validateProperty($this->endStore, ['company.document_type_id']) || 
                !ValidateHelper::validateProperty($this->endStore, ['company.document'])
            ){
                $completed = false;
            }
        }

        return $completed;

    }

    private function validateDetails(){
        $result = [
            'success' => false,
            'errors' => null,
            'data' => null
        ];

        if(!isset($this->data['detail'])){
            $result['errors'] = ['Los items de inventario son obligatorios'];
        }
        else if(!is_array($this->data['detail'])){
            $result['errors'] = ['Los items de inventario deben ser una lista'];
        }
        else if(count($this->data['detail']) == 0){
            $result['errors'] = ['Los items de inventario deben ser mayor a 0'];
        }
        else{
            foreach($this->data['detail'] as $row){
                $validator = new TransferBetweenCompanyRequest();
                $status = $validator->validateDetail($row);
                if (!$status) {
                    $result['success'] = false;
                    $result['errors'] = $validator->getErrors();
                    break;
                }
            }
        }

        return $result;
    }

    private function validatedTransport(){
        $result = [
            'success' => false,
            'errors' => null,
            'data' => null
        ];

        if(!isset($this->data['transports'])){
            $result['errors'] = ['El transporte es obligatorio'];
        }
        else if(!is_array($this->data['transports'])){
            $result['errors'] = ['El transporte debe ser una lista'];
        }
        else if(count($this->data['transports']) == 0){
            $result['errors'] = ['El transporte debe ser mayor a 0'];
        }
        else{
            $modality = null;
            if(isset($this->data['transport_modality'])){
                $modality = $this->data['transport_modality'];
            }
            
            if($modality){
                foreach($this->data['transports'] as $row){
                    $validator = new TransferBetweenCompanyRequest();
                    $status = null;
                    if($modality == 1){
                        $status = $validator->validateStoreTransportPublic($row);
                    }
                    else if($modality == 2){
                        $status = $validator->validateStoreTransportPrivate($row);
                    }

                    if (!$status) {
                        $result['success'] = false;
                        $result['errors'] = $validator->getErrors();
                        break;
                    }
                }
            }
        }

        return $result;
    }

    public function validateVehicle(){
        $result = [
            'success' => false,
            'errors' => null,
            'data' => null
        ];

        if(isset($this->data['transport_modality'])){
           if($this->data['transport_modality'] == 2){
               if(!isset($this->data['vehicles'])){
                   $result['errors'] = ['El vehículo es obligatorio'];
               }
               else if(!is_array($this->data['detail'])){
                   $result['errors'] = ['El vehículo debe ser una lista'];
               }
               else if(count($this->data['vehicles']) == 0){
                   $result['errors'] = ['El vehículo debe ser mayor a 0'];
               }
               else{
                   foreach($this->data['vehicles'] as $row){
                       $validator = new TransferBetweenCompanyRequest();
                       $status = $validator->validateStoreVehicle($row);
                       if (!$status) {
                           $result['success'] = false;
                           $result['errors'] = $validator->getErrors();
                           break;
                       }
                   }
               }
           } 
        }

        return $result;
    }


    public function validateExistEndCompany(){

    }

    private function completeGuide(){
        $this->dataGuide['observations'] = isset($this->data['observations'])?$this->data['observations']:null;
        $this->dataGuide['motive_code'] = '04';
        $this->dataGuide['motive_description'] = 'Traslado entre establecimientos de la misma empresa';
        $this->dataGuide['unit_measure'] = 'KGM';
        $this->dataGuide['email_principal'] = $this->data['end_store']['email_principal'];
        $this->dataGuide['email_secondary'] = $this->data['end_store']['email_secondary'];
        if(isset($this->data['id'])){
            $this->dataGuide['updated_at'] = $data['updated_at'];
        }
        else{
            $this->dataGuide['created_at'] = date("Y-m-d H:i:s");
        }
    }

    private function storeTransport($data, $transferGuideId, $update = false){
        if($update){
            $this->transportRepository->deleteBy('transfer_guide_id', $transferGuideId);
        }

        foreach($data as $transport){
            $transport['modality'] = $this->data['transport_modality'];
            $transport['transfer_guide_id'] = $transferGuideId;
            $transport['created_at'] = date("Y-m-d H:i:s");
            $this->transportRepository->store($transport);
        }
    }

    private function storeVehicle($data, $transferGuideId, $update = false){
        if($update){
            $this->vehicleRepository->deleteBy('transfer_guide_id', $transferGuideId);
        }
        
        foreach($data as $vehicle){
            $vehicle['transfer_guide_id'] = $transferGuideId;
            $vehicle['created_at'] = date("Y-m-d H:i:s");
            $this->vehicleRepository->store($vehicle);
        }
    }

    private function storeDetails($data, $transferGuideId){
        $this->transferGuideDetailRepository->deleteBy('transfer_guide_id', $transferGuideId);
        
        foreach($data as $detail){
            $item = [
                'transfer_guide_id' => $transferGuideId,
                'movement_id' => $detail['movement_id'],
                'movement_detail_id' => $detail['movement_detail_id'],
                'inventory_id' => $detail['inventory_id'],
                'additional_description' => isset($detail['additional_description'])?$detail['additional_description']:null,
                'unit_measure_sunat' => $detail['unit_measure_sunat'],
            ];
            $this->transferGuideDetailRepository->store($item);
        }
    }

    private function updateRelations(){
        $this->completeStoreIni();
        $this->completeStoreDes();
    }

    private function completeStoreIni(){
        if(!ValidateHelper::validateProperty($this->startStore, ['company.id'])){
            $this->storeRepository->updateBy('id_alm', $this->data['start_store']['id'], [ 'company_id' => $this->data['start_store']['company_id']]);
        }
    }

    private function completeStoreDes(){
        if(!ValidateHelper::validateProperty($this->endStore, ['company.id'])){
            $this->storeRepository->updateBy('id_alm', $this->data['end_store']['id'], [ 'company_id' => $this->data['end_store']['company_id']]);
        }
    }

    private function sendTransitMovementGuide($id){
        $response = GlobalHelper::getGlobalResponse();
        $transferGuide = $this->transferGuideRepository->findOneWithDetails($id);
        $tciService = new TCIService();
        // echo json_encode(FormatHelper::parseStoreTransitMovementGuide($transferGuide));
        $tciResponse = $tciService->registerGRR20(FormatHelper::parseStoreTransitMovementGuide($transferGuide));
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


















    private function validatedTransportPrivate($data){
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
                    echo json_encode($tciService);
                    $tciResponse = $tciService->registerGRR20(FormatHelper::parseStoreDevolutionGuide($movement, $data));
                    $response['data'] = $tciResponse['data'];
                    $response['message'] = $tciResponse['message'];
                }
                // END TEST
            } 
        } 
        catch (PDOException $e) {
            Session::setAttribute("error", $e->getMessage());
            echo json_encode($e->getMessage());
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
                    echo json_encode($tciService);
                    $tciResponse = $tciService->registerGRR20(FormatHelper::parseStoreOthersGuide($movement, $data));
                    $response['data'] = $tciResponse['data'];
                    $response['message'] = $tciResponse['message'];
                }
                // END TEST
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