<?php
require_once __DIR__ . '/../Helpers/XMLHelper.php';
require_once __DIR__ . '/../Helpers/HttpHelper.php';

class TCIService{
    private $baseUrl = 'http://egestor.qa.efacturacion.pe/WS_eCica/GuiaRemisionRemitente/ServicioGuiaRemisionRemitente.svc/soap11';
    private $namespace = 'http://tci.net.pe/WS_eCica/GuiaRemisionRemitente/';
    private $httpHelper;
    
    public function __construct() {
        $this->httpHelper = new HttpHelper();
    }

    /*
    |--------------------------------------------------------------------------
    | RegistrarGRR20
    |--------------------------------------------------------------------------
    */
    public function queryStatusGRR20($data){
        return self::sendAction('ConsultarEstadoGRR', $data);
    }

    /*
    |--------------------------------------------------------------------------
    | RegistrarGRR20
    |--------------------------------------------------------------------------
    */
    public function registerGRR20($data){
        return self::sendAction('RegistrarGRR20', $data);
    }

    private function getHeaders($action){
        return [
            'Content-Type: text/xml;charset=UTF-8',
            'SOAPAction: http://tci.net.pe/WS_eCica/GuiaRemisionRemitente/IServicioGuiaRemisionRemitente/'.$action
        ];
    }

    private function getXMLContent($data){
        $xmlHelper = new XMLHelper($data, $this->namespace);
        $xmlContent = $xmlHelper->generateXML();
        return $xmlContent;
    }

    private function sendAction($action, $data){
        $xmlContent = self::getXmlContent($data, $action);
        $response = $this->httpHelper->postXML($this->baseUrl, self::getHeaders($action), $xmlContent);
        return self::getResponse($response, $action);
    }

    private function getResponse($response, $action){
        $data = null;

        if($response['success']){
            if(isset($response['response'][$action.'Response'])){
                $data = isset($response['response'][$action.'Response'][$action.'Result'])?$response['response'][$action.'Response'][$action.'Result'] : null;
            }
        }

        $response['data'] = $data;
        unset($response['response']);
        return $response;
    }
}