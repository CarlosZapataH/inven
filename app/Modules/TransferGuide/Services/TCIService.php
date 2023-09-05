<?php
require_once __DIR__ . '/../Helpers/XMLHelper.php';
require_once __DIR__ . '/../Helpers/HttpHelper.php';
require_once __DIR__ . '/../../../Helpers/LoadEnv.php';

class TCIService{
    private $baseUrl;
    private $namespace = 'http://tci.net.pe/WS_eCica/GuiaRemisionRemitente/';
    
    private $baseUrlReversion;
    private $namespaceReversion = 'http://tci.net.pe/WS_eCica/Reversiones/';

    private $httpHelper;
    
    public function __construct() {
        $this->httpHelper = new HttpHelper();
        $this->baseUrl = $_ENV['TCI_URL_EMISION'];
        $this->baseUrlReversion = $_ENV['TCI_URL_REVERSION'];
    }

    /*
    |--------------------------------------------------------------------------
    | ConsultarEstadoGRR
    |--------------------------------------------------------------------------
    */
    public function queryStatusGRR20($data){
        return self::sendAction('ConsultarEstadoGRR', [
            'ConsultarEstadoGRR' => $data
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | RegistrarGRR20
    |--------------------------------------------------------------------------
    */
    public function registerGRR20($data){
        return self::sendAction('RegistrarGRR20', [
            'RegistrarGRR20' => $data
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ConsultarXMLGRR
    |--------------------------------------------------------------------------
    */
    public function queryXML($data){
        return self::sendAction('ConsultarXMLGRR', [
            'ConsultarXMLGRR' => $data
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ConsultarXMLGRR
    |--------------------------------------------------------------------------
    */
    public function queryResponseSUNAT($data){
        return self::sendAction('ConsultarRespuestaGRR', [
            'ConsultarRespuestaGRR' => $data
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ConfirmarRespuestaGRR
    |--------------------------------------------------------------------------
    */
    public function confirmResponseSUNAT($data){
        return self::sendAction('ConfirmarRespuestaGRR', [
            'ConfirmarRespuestaGRR' => $data
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ConsultarRI_GRR
    |--------------------------------------------------------------------------
    */
    public function queryPdf($data){
        return self::sendAction('ConsultarRI_GRR', [
            'ConsultarRI_GRR' => $data
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ConsultaIndividualGRR
    |--------------------------------------------------------------------------
    */
    public function queryOneGRR($data){
        return self::sendAction('ConsultaIndividualGRR', [
            'ConsultaIndividualGRR' => $data
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | RegistrarResumenReversion
    |--------------------------------------------------------------------------
    */
    public function registerResumeReversion($data){
        return self::sendActionReversion('RegistrarResumenReversion', [
            'RegistrarResumenReversion' => $data
        ]);
    }
    
    private function getHeaders($action){
        return [
            'Content-Type: text/xml;charset=UTF-8',
            'SOAPAction: http://tci.net.pe/WS_eCica/GuiaRemisionRemitente/IServicioGuiaRemisionRemitente/'.$action
        ];
    }

    private function getHeadersReversion($action){
        return [
            'Content-Type: text/xml;charset=UTF-8',
            'SOAPAction: http://tci.net.pe/WS_eCica/Reversiones/IServicioReversiones/'.$action
        ];
    }

    private function getXMLContent($data){
        $xmlHelper = new XMLHelper($data, $this->namespace);
        $xmlContent = $xmlHelper->generateXML();
        return $xmlContent;
    }

    private function getXMLContentReversion($data){
        $xmlHelper = new XMLHelper($data, $this->namespaceReversion);
        $xmlContent = $xmlHelper->generateXML();
        return $xmlContent;
    }

    private function sendAction($action, $data){
        $xmlContent = self::getXmlContent($data, $action);
        $response = $this->httpHelper->postXML($this->baseUrl, self::getHeaders($action), $xmlContent);
        // echo json_encode($response);
        $response['content_send'] = $xmlContent;
        return self::getResponse($response, $action);
    }

    private function sendActionReversion($action, $data){
        $xmlContent = self::getXMLContentReversion($data, $action);
        $response = $this->httpHelper->postXML($this->baseUrlReversion, self::getHeadersReversion($action), $xmlContent);
        // echo json_encode($response);
        $response['content_send'] = $xmlContent;
        return self::getResponse($response, $action);
    }

    private function getResponse($response, $action){
        $data = null;
        if($response['success']){
            if(isset($response['response'][$action.'Response'])){
                $data = isset($response['response'][$action.'Response'][$action.'Result'])?$response['response'][$action.'Response'][$action.'Result'] : null;
                if($data){
                    if(isset($data['at_CodigoError'])){
                        if($data['at_CodigoError'] != "0"){
                            $response['code'] = 400;
                        }
                        // && isset($data['at_NivelResultado'])
                        // if(!self::isBoolString($data['at_NivelResultado'])){
                        // }
                    }

                    if(isset($data['at_MensajeResultado'])){
                        if(!isset($data['at_CodigoError'])){
                            // if($data['at_CodigoError'] != "0"){
                                $response['success'] = true;
                            // }
                        }
                        else if($data['at_CodigoError'] != "0"){
                            $response['success'] = true;
                        }
                        // $response['success'] = self::isBoolString($data['at_NivelResultado']);
                        // $response['success'] = !isset($data['at_CodigoError']);
                    }

                    if(isset($data['at_MensajeResultado'])){
                        $response['message'] = $data['at_MensajeResultado'];
                    }
                }

            }
        }

        $response['data'] = $data;
        $response['original'] = $response['response']['original'];
        unset($response['response']);
        return $response;
    }

    private function isBoolString($value){
        if(strtolower($value) == "true"){
            return true;
        }
        else if(strtolower($value) == "false"){
            return false;
        }
    }
}
