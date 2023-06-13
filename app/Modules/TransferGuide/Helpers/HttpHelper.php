<?php
class HttpHelper{
    public function __construct() {
    }

    public function postXML($url, $headers, $xml) {
        $error = null;
        $arrayResponse = null;
        $success = false;

        try{
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
    
            $response = curl_exec($ch);

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

            if ($httpCode != 200 || !$response){
                $error = 'Ocurrio un error';

                if (curl_errno($ch)) {
                    if(curl_error($ch)){
                        $errno = curl_errno($ch);
                        $error = curl_error($ch);
                        curl_close($ch);
                    }
                }
            }
            else{
                curl_close($ch);
            }
            
            if(!$error){
                $arrayResponse = self::parseResponse($response);
                $success = true;
            }
        }
        catch(Exception $e){
            $error = $e->getMessage();
        }

        return [
            'response' => $arrayResponse,
            'message' => $error,
            'code' => $httpCode,
            'success' => $success
        ];
    }

    public function parseResponse($response){
        // Crear un objeto SimpleXMLElement a partir del XML con el espacio de nombres registrado
        // $xmlObject = simplexml_load_string($response, null, 0, 'http://schemas.xmlsoap.org/soap/envelope/');
        $xmlObject = simplexml_load_string(stripslashes($response));
        
        // Registrar el espacio de nombres
        $xmlObject->registerXPathNamespace('ns', 'http://schemas.xmlsoap.org/soap/envelope/');
        
        // Utilizar XPath para acceder a los elementos dentro del espacio de nombres
        $resultado = $xmlObject->xpath('//ns:Body');
        
        // Convertir el resultado XPath en un array
        $arrayResponse = json_decode(json_encode($resultado[0]), true);
        $arrayResponse['original'] = $response;

        return $arrayResponse;
    }
}