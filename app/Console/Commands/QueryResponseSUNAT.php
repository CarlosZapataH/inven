<?php
require_once __DIR__ . '/../../Modules/TransferGuide/Services/TCIService.php';
require_once __DIR__ . '/../../Modules/TransferGuide/Helpers/FormatHelper.php';
require_once __DIR__ . '/../../Modules/TransferGuide/Repository/TransferGuideRepository.php';
require_once __DIR__ . '/../../Modules/TransferGuideHistory/Repository/TransferGuideHistoryRepository.php';

function queryResponseSUNAT(){
    header('Content-Type: application/json');
    try {
            $documentEmisor = '20357259976';
            $transferGuideRepository = new TransferGuideRepository();
            $tciService = new TCIService();
            $tciResponse = $tciService->queryResponseSUNAT([
                'ent_ConsultarRespuesta' => [
                    'at_NumeroDocumentoIdentidad' => $documentEmisor,
                    'at_CantidadConsultar' => 100
                ]
            ]);
            
            if($tciResponse['success']){
                $documents = FormatHelper::parseResponseQueryResponseSUNAT($tciResponse['data']);
                if(count($documents) > 0){
                    foreach($documents as $row){
                        $nGuide = $row['serie'].'-'.$row['number'];
                        $guide = $transferGuideRepository->findByNumber($row['serie'], $row['number']);
                        
                        if($guide){
                            $transferGuideRepository->update($guide['id'], [
                                'tci_response_code' => $row['code_response'],
                                'tci_response_type' => $row['type_response'],
                                'tci_response_description' => $row['description'],
                                'tci_response_date' => $row['date'],
                                'tci_confirm_status_response' => json_encode($row)
                            ]);

                            $transferGuideHistoryRepository = new TransferGuideHistoryRepository();
                            $transferGuideHistoryRepository->store([
                                'status' => $row['type_response'],
                                'code' => $row['code_response'],
                                'description' => $row['description'],
                                'date' => $row['date'],
                                'transfer_guide_id' => $guide['id'],
                                'tci_confirm_status_response' => json_encode($row),
                                'created_at' => date("Y-m-d H:i:s")
                            ]);
                            
                            $tciServiceConfirm = new TCIService();
                            $res = $tciServiceConfirm->confirmResponseSUNAT([
                                'ent_ConfirmarRespuesta' => [
                                    'at_NumeroDocumentoIdentidad' => $documentEmisor,
                                    'l_Comprobante' => [
                                        'en_ComprobanteConfirmarRespuesta' => [
                                            'at_Serie' => $row['serie'],
                                            'at_Numero' => $row['number'],
                                            'at_CodigoRespuesta' => $row['code_response']
                                        ]
                                    ]
                                ]
                            ]);
                        }
                    }
                }
                echo json_encode("QUERY DOCUMENTS - ".date("Y-m-d H:i:s"));
                echo json_encode($documents);
            }
    } 
    catch (PDOException $e) {
        // echo json_encode($e->getMessage());
    }
}

queryResponseSUNAT();