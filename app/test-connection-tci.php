<?php
require_once __DIR__ . '/Helpers/GlobalHelper.php';
require_once __DIR__ . '/Modules/TransferGuide/Services/TCIService.php';

header('Content-Type: application/json');

$response = GlobalHelper::getGlobalResponse();
try {
    $tciService = new TCIService();
    $tciResponse = $tciService->queryStatusGRR20([
        'ent_ConsultarEstado' => [
            'at_NumeroDocumentoIdentidad' => '20357259976',
            'at_CantidadConsultar' => 300
        ]
    ]);

    $response['response'] = $tciResponse;
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
catch (Exception $e) {
    echo $e->getMessage();
}

http_response_code($response['code']);
echo json_encode($response);