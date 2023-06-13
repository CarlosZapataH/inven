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
        // echo $xmlContent;
        $response = $this->httpHelper->postXML($this->baseUrl, self::getHeaders($action), $xmlContent);
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
                            if($data['at_CodigoError'] != "0"){
                                $response['success'] = true;
                            }
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
