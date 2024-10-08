<?php
require_once __DIR__ . '/../../../Helpers/GlobalHelper.php';
require_once __DIR__ . '/../Requests/TransferBetweenCompanyRequest.php';
require_once __DIR__ . '/../Helpers/ValidateHelper.php';
require_once __DIR__ . '/../../../Models/TransferGuide.php';
require_once __DIR__ . '/../../TransitMovement/Repository/TransitMovementRepository.php';
require_once __DIR__ . '/../../../../config/Config.php';
require_once __DIR__ . '/../../Ubigeo/Repository/UbigeoRepository.php';
require_once __DIR__ . '/../../Util/Helpers/IndicatorServiceHelper.php';
require_once __DIR__ . '/../../../Helpers/LoadEnv.php';

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
    private $ubigeoRepository;

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
        $this->ubigeoRepository = new UbigeoRepository();

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

        if(!$this->response['errors']){
            $this->validateCompleteStores();
        }

        if(!$this->response['errors']){
            $this->validateNewCompany();
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
            /* if(!ValidateHelper::validateProperty($this->data, ['end_store.email_principal'])){
                $this->addErrors(['end_store.email_principal' => 'El correo electrónico primario del almacén de llegada es obligatorio.']);
            } */
            /* if(!ValidateHelper::validateProperty($this->data, ['end_store.email_secondary'])){
                $this->addErrors(['end_store.email_secondary' => 'El correo electrónico secundario del almacén de llegada es obligatorio.']);
            } */
        }
        
        $this->guide['email_principal'] = $_ENV['TRANSFER_GUIDE_PRINCIPAL_EMAIL'];
        // $this->guide['email_principal'] = ValidateHelper::validateProperty($this->data, ['end_store.email_principal'])?$this->data['end_store']['email_principal']:null;
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

        $requiredMTC = false;
        if($this->guide['indicator_service'] && $this->guide['indicator_service'] != ''){
            $indicators = IndicatorServiceHelper::getAll();
            foreach($indicators as $indicator){
                if($indicator['code'] == $this->guide['indicator_service']){
                    $requiredMTC = $indicator['required_mtc'];
                }
            }
        }
        
        if($exist){
            $modality = $this->data['transport_modality'];
                
            foreach($this->data['transports'] as $row){
                $validator = new TransferBetweenCompanyRequest();
                $status = null;
                if($modality == 1){
                    $status = $validator->validateStoreTransportPublic($row, $this->send, $requiredMTC);
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

    private function validateCompleteStores(){
        // if($this->send){
            $this->isAlternative();
           
            if(!ValidateHelper::validateProperty($this->startStore, ['address']) && !ValidateHelper::validateProperty($this->data['start_store'], ['address'])){
                $this->addErrors(['start_store.address' => 'La dirección del almacén de salida es obligatoria.']);
            }
            if(!ValidateHelper::validateProperty($this->startStore, ['district.id']) && !ValidateHelper::validateProperty($this->data['start_store'], ['district_id'])){
                $this->addErrors(['start_store.address' => 'El distrito del almacén de salida es obligatorio.']);
            }

            if(
                (!ValidateHelper::validateProperty($this->endStore, ['address']) && !ValidateHelper::validateProperty($this->data['end_store'], ['address'])) || 
                (!ValidateHelper::validateProperty($this->data['end_store'], ['address']) && $this->data['end_store']['alternative_address'])
            ){
                $this->addErrors(['end_store.address' => 'La dirección del almacén de llegada es obligatoria.']);
            }
            if(
                (!ValidateHelper::validateProperty($this->endStore, ['district.id']) && !ValidateHelper::validateProperty($this->data['end_store'], ['district_id'])) || 
                (!ValidateHelper::validateProperty($this->data['end_store'], ['district_id']) && $this->data['end_store']['alternative_address'])
            ){
                $this->addErrors(['end_store.address' => 'El distrito del almacén de llegada es obligatorio.']);
            }
        // }
            
        if(!$this->response['errors']){
            if($this->data['end_store']['alternative_address']){
                $ubigeoFind = $this->ubigeoRepository->getDistrict($this->data['end_store']['district_id']);
                if($ubigeoFind){
                    $this->data['end_store']['district_id'] = $ubigeoFind['id_ubigeo'];
                }
            }


            if(!ValidateHelper::validateProperty($this->startStore, ['address'])){
                $this->startStore['update'] = true;
                $this->startStore['update_address'] = $this->data['start_store']['address'];
            }
            if(!ValidateHelper::validateProperty($this->startStore, ['district.id'])){
                $this->startStore['update'] = true;
                $this->startStore['update_district_id'] = $this->data['start_store']['district_id'];
            }

            if($this->data['end_store']['alternative_address']){
                $this->guide['store_des_address'] = $this->data['end_store']['address'];
                $this->guide['store_des_district_id'] = $this->data['end_store']['district_id'];
            }
            else{
                if(!ValidateHelper::validateProperty($this->endStore, ['address'])){
                    $this->endStore['update'] = true;
                    $this->endStore['update_address'] = $this->data['end_store']['address'];
                    $this->guide['store_des_address'] = $this->data['end_store']['address'];
                }
                else{
                    $this->guide['store_des_address'] = $this->endStore['address'];
                }

                if(!ValidateHelper::validateProperty($this->endStore, ['district.id'])){
                    $this->endStore['update'] = true;
                    $this->endStore['update_district_id'] = $this->data['end_store']['district_id'];
                    $this->guide['store_des_district_id'] = $this->data['end_store']['district_id'];
                }
                else{
                    $this->guide['store_des_district_id'] = $this->endStore['district']['id'];
                }
            }
        }
    }

    private function isAlternative(){
        $result = false;
        if(isset($this->data['end_store']['alternative_address'])){
            if($this->data['end_store']['alternative_address'] == true || $this->data['end_store']['alternative_address'] == 'true' || $this->data['end_store']['alternative_address'] == 1 || $this->data['end_store']['alternative_address'] == '1'){
                $result = true;
            }
        }

        $this->data['end_store']['alternative_address'] = $result;
        $this->guide['alternative_address'] = $result;

    }

    private function validateNewCompany(){
        $newCompany = false;
        if(isset($this->guide['flag_new_company'])){
            if($this->guide['flag_new_company'] == true || $this->guide['flag_new_company'] == 'true' || $this->guide['flag_new_company'] == 1 || $this->guide['flag_new_company'] == '1'){
                $newCompany = true;
            }
        }

        $this->guide['flag_new_company'] = $newCompany;

        if($newCompany){
            if(!ValidateHelper::validateProperty($this->guide, ['new_document_type_id'])){
                $this->addErrors(['guide.new_document_type_id' => 'El documento de identidad del nuevo remitente es obligatorio.']);
            }
            if(!ValidateHelper::validateProperty($this->guide, ['new_document'])){
                $this->addErrors(['guide.new_document' => 'El documento de identidad del nuevo remitente es obligatorio.']);
            }
            if(!ValidateHelper::validateProperty($this->guide, ['new_company_name'])){
                $this->addErrors(['guide.new_company_name' => 'La razón social del nuevo remitente es obligatoria.']);
            }
        }
        else{
            $this->guide['new_document_type_id'] = null ;
            $this->guide['new_document'] = null ;
            $this->guide['new_company_name'] = null ;
        }
    }
}