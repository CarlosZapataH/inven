<?php
require_once __DIR__ . '/../../../Helpers/GlobalHelper.php';
require_once __DIR__ . '/../Requests/TransferBetweenCompanyRequest.php';
require_once __DIR__ . '/../Helpers/ValidateHelper.php';
require_once __DIR__ . '/../../../Models/TransferGuide.php';
require_once __DIR__ . '/../../TransitMovement/Repository/TransitMovementRepository.php';
require_once __DIR__ . '/../../../../config/Config.php';

class ValidationTransferGuide{
    private $config;
    private $data;
    private $response;
    private $guide;
    private $send;
    private $startStore;
    private $endStore;
    private $movements;
    private $details;
    private $transports;
    private $vehicles;
    private $provider;
    private $buyer;
    
    private $transitMovementRepository;

    public function __construct($data)
    {
        $this->response = GlobalHelper::getGlobalResponse();
        $this->config = new Config();
        $this->data = $data;
        $this->guide = [];
        $this->startStore = null;
        $this->endStore = null;
        $this->send = false;
        $this->transports = [];
        $this->vehicles = [];
        $this->provider = null;
        $this->buyer = null;
        $this->transitMovementRepository = new TransitMovementRepository();

        if(isset($this->data['send'])){
            if($this->data['send'] == 1 || $this->data['send'] == true || $this->data['send'] == 'true' || $this->data['send'] == '1'){
                $this->send = true;
            }
        }
    }

    public function validate(){
        $this->validateData();
        if(!$this->response['errors']){
            $this->response['success'] = true;
            $this->response['message'] = 'Validación exitosa.';
            $this->response['code'] = 200;
            $this->completeGuide();
            unset($this->guide['detail']);
        }
        $this->response['data'] = [
            'guide' => $this->guide,
            'startStore' => $this->startStore,
            'endStore' => $this->endStore,
            'send' => $this->send,
            'transports' => $this->transports,
            'vehicles' => $this->vehicles,
            'detail' => $this->details,
            'movements' => $this->movements,
            'provider' => $this->provider,
            'buyer' => $this->buyer
        ];
        return $this->response;
    }

    private function validateData(){
        $this->validateGuide();

        if(!$this->response['errors']){
            $this->validateEmailsStoreCompany();
        }

        if(!$this->response['errors']){
            $this->validateDetails();
        }

        if(!$this->response['errors']){
            $this->validateStores();
        }

        if(!$this->response['errors']){
            $this->validatedTransport();
        }

        if(!$this->response['errors']){
            $this->validateVehicle();
        }

        if(!$this->response['errors']){
            $this->validateProviver();
        }

        if(!$this->response['errors']){
            $this->validateBuyer();
        }
    }

    private function validateGuide(){
        $requireDescription = false;

        if(ValidateHelper::validateProperty($this->data, ['motive_code'])){
            if($this->data['motive_code'] == TransferGuide::OTHER){
                $requireDescription = true;
            }
        }

        $validator = new TransferBetweenCompanyRequest();
        if ($validator->validateGuide($this->data, $this->send, $requireDescription)) {
            $this->guide = $validator->getValidData();
        } 
        else {
            $this->addErrors($validator->getErrors());
        }
    }

    private function validateDetails(){
        if(count($this->data['detail']) == 0){
            $this->addErrors(['detail.min' => 'Los items de inventario deben ser mayor a 0']);
        }
        else{
            $this->details = [];
            foreach($this->data['detail'] as $row){
                $validator = new TransferBetweenCompanyRequest();
                if ($validator->validateDetail($row)) {
                    array_push($this->details, $validator->getValidData());
                }
                else{
                    $this->addErrors($validator->getErrors());
                    break;
                }
            }
        }
    }

    private function validateStores(){
        $ids = [];
        foreach($this->guide['detail'] as $detail){
            if(!in_array($detail['movement_id'], $ids)){
                array_push($ids, $detail['movement_id']);
            }
        }
        
        $avalialble = $this->guide['id']?false:true;
        $response = $this->transitMovementRepository->findWithDetails(implode(",", $ids), $avalialble);
        
        if($response['data']){
            if(count($response['data']) > 0){
                if(!$response['start_store']){
                    $this->addErrors(['start_store' => 'No se encontró almacén de salida']);
                }

                if(!$response['end_store']){
                    $this->addErrors(['end_store' => 'No se encontró almacén de llegada']);
                }

                if($this->guide['motive_code'] == TransferGuide::BETWEENCOMPANY){
                    if(count($response['start_store_id']) > 1){
                        $this->addErrors(['start_store.same' => 'Todos los elementos deben pertenecer al mismo almacén de partida']);
                    }
                    else if(count($response['end_store_id']) > 1){
                        $this->addErrors(['end_store.same' => 'Todos los elementos deben pertenecer al mismo almacén de llegada']);
                    } 
                }
            }
            else{
                $this->addErrors(['detail' => 'No se encontraron registros de inventario']);
            }
        }
        else{
            $this->addErrors(['detail' => 'No se encontraron registros de inventario']);
        }

        if(!$this->response['errors']){
            $this->movements = $response['data'];
            $this->startStore = $response['start_store'];
            $this->endStore = $response['end_store'];

            $this->validateSameCompany();
        }
    }

    private function validateSameCompany(){
        $startStoreCompanyId = null;
        $endStoreCompanyId = null;
        $this->startStore['update'] = false;
        $this->endStore['update'] = false;

        if(ValidateHelper::validateProperty($this->startStore, ['company.id'])){
            $startStoreCompanyId = $this->startStore['company']['id'];
        }
        else if(ValidateHelper::validateProperty($this->data, ['start_store.company_id'])){
            $startStoreCompanyId = $this->data['start_store']['company_id'];
            $this->startStore['update'] = true;
            $this->startStore['company_id'] = $startStoreCompanyId;
        }
        else{
            $this->addErrors(['start_store.company' => 'El id de empresa del almacen de salida es obligatorio']);
        }

        if(ValidateHelper::validateProperty($this->endStore, ['company.id'])){
            $endStoreCompanyId = $this->endStore['company']['id'];
        }
        else if(ValidateHelper::validateProperty($this->data, ['end_store.company_id'])){
            $endStoreCompanyId = $this->data['end_store']['company_id'];
            $this->endStore['update'] = true;
            $this->endStore['company_id'] = $endStoreCompanyId;
        }
        else{
            $this->addErrors(['end_store.company' => 'El id de empresa del almacen de llegada es obligatorio']);
        }

        if($startStoreCompanyId && $endStoreCompanyId){
            if($this->guide['motive_code'] == TransferGuide::BETWEENCOMPANY && $startStoreCompanyId != $endStoreCompanyId){
                $this->addErrors(['guide.company' => 'El remitente y destinatario deben ser el mismo.']);
            }
            else if($this->guide['motive_code'] == TransferGuide::DEVOLUTION && $startStoreCompanyId == $endStoreCompanyId){
                $this->addErrors(['guide.company' => 'El remitente y destinatario no deben ser el mismo.']);
            }

            if(!$this->startStore['establishment_id']){
                $this->addErrors(['guide.company' => 'El almacen de partida no cuenta con un establecimiento asignado.']);
            }
        }
    }

    private function validateEmailsStoreCompany(){
        if($this->send){
            if(!ValidateHelper::validateProperty($this->data, ['end_store.email_principal'])){
                $this->addErrors(['end_store.email_principal' => 'El correo electrónico primario del almacén de llegada es obligatorio.']);
            }
            if(!ValidateHelper::validateProperty($this->data, ['end_store.email_secondary'])){
                $this->addErrors(['end_store.email_secondary' => 'El correo electrónico secundario del almacén de llegada es obligatorio.']);
            }
        }
        
        $this->guide['email_principal'] = ValidateHelper::validateProperty($this->data, ['end_store.email_principal'])?$this->data['end_store']['email_principal']:null;
        $this->guide['email_secondary'] = ValidateHelper::validateProperty($this->data, ['end_store.email_secondary'])?$this->data['end_store']['email_secondary']:null;
    }

    private function validatedTransport(){
        $exist = false;

        if(!isset($this->data['transports'])){
            if($this->send){
                $this->addErrors(['transports' => 'El transporte es obligatorio']);
            }
        }
        else if(!is_array($this->data['transports'])){
            if($this->send){
                $this->addErrors(['transports' => 'El transporte debe ser una lista']);
            }
        }
        else if(count($this->data['transports']) == 0){
            if($this->send){
                $this->addErrors(['transports' => 'El transporte debe ser mayor a 0']);
            }
        }
        else{
            $exist = true;
        }
        
        if($exist){
            $modality = $this->data['transport_modality'];
                
            foreach($this->data['transports'] as $row){
                $validator = new TransferBetweenCompanyRequest();
                $status = null;
                if($modality == 1){
                    $status = $validator->validateStoreTransportPublic($row, $this->send);
                }
                else if($modality == 2){
                    $status = $validator->validateStoreTransportPrivate($row, $this->send);
                }

                if ($status) {
                    array_push($this->transports, $validator->getValidData());
                }
                else{
                    $this->addErrors($validator->getErrors());
                    break;
                }
            }
        }
    }

    private function validateVehicle(){
        $exist = false;
        if($this->data['transport_modality'] == 2){
            if(!isset($this->data['vehicles'])){
                if($this->send){
                    $this->addErrors(['vehicles' => 'El vehículo es obligatorio']);
                }
            }
            else if(!is_array($this->data['vehicles'])){
                if($this->send){
                    $this->addErrors(['vehicles' => 'El vehículo debe ser una lista']);
                }
            }
            else if(count($this->data['vehicles']) == 0){
                if($this->send){
                    $this->addErrors(['vehicles' => 'El vehículo debe ser mayor a 0']);
                }
            }
            else{
                $exist = true;
            }

            if($exist){
                foreach($this->data['vehicles'] as $row){
                    $validator = new TransferBetweenCompanyRequest();
                    $status = $validator->validateStoreVehicle($row, $this->send);
                    if ($status) {
                        array_push($this->vehicles, $validator->getValidData());
                    }
                    else{
                        $this->addErrors($validator->getErrors());
                        break;
                    }
                }
            }
        } 

    }

    private function completeGuide(){
        $this->guide['unit_measure'] = 'KGM';
        $this->guide['store_ini_id'] = $this->startStore['id'];
        $this->guide['store_des_id'] = $this->endStore['id'];
        
        if($this->guide['id']){
            $this->guide['updated_at'] = date("Y-m-d H:i:s");
        }
        else{
            $this->guide['created_at'] = date("Y-m-d H:i:s");
        }
    }

    private function addErrors($errors){
        $this->response['errors'] = $this->response['errors']?array_merge($this->response['errors'], $errors):$errors;
    }

    private function validateProviver(){
        if($this->data['motive_code'] == TransferGuide::OTHER){
            if(isset($this->data['provider'])){
                $validator = new TransferBetweenCompanyRequest();
                if ($validator->validateProvider($this->data['provider'], $this->send)) {
                    $this->provider = $validator->getValidData();
                } 
                else {
                    $this->addErrors($validator->getErrors());
                }
            }
        }
    }

    private function validateBuyer(){
        if($this->data['motive_code'] == TransferGuide::OTHER){
            if(isset($this->data['buyer'])){
                $validator = new TransferBetweenCompanyRequest();
                if ($validator->validateBuyer($this->data['buyer'], $this->send)) {
                    $this->buyer = $validator->getValidData();
                } 
                else {
                    $this->addErrors($validator->getErrors());
                }
            }
        }
    }
}