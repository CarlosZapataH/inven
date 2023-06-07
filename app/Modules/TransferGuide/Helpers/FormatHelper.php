<?php
require_once __DIR__ . '/ValidateHelper.php';

class FormatHelper{
    public function __construct() {
    }

    public static function parseStoreTransitMovementGuide($movement, $data){
        $remitenteDocumento = "";
        $remitenteRazonSocial = "";
        $remitenteNombreComercial = "";
        
        if(ValidateHelper::validateProperty($movement, ['almacen_partida.company.document'])){ 
            $remitenteDocumento = $movement['almacen_partida']['company']['document'];
        }
        else{
            $remitenteDocumento = $data['almacen_partida']['document'];
        }

        if(ValidateHelper::validateProperty($movement, ['almacen_partida.company.name'])){ 
            $remitenteRazonSocial = $movement['almacen_partida']['company']['name'];
        }
        else{
            $remitenteRazonSocial = $data['almacen_partida']['name'];
        }

        if(ValidateHelper::validateProperty($movement, ['almacen_partida.company.commercial_name'])){ 
            $remitenteNombreComercial = $movement['almacen_partida']['company']['commercial_name'];
        }
        else{
            $remitenteNombreComercial = $data['almacen_partida']['commercial_name'];
        }

        $destinatarioTipoDocumento = "";
        $destinatarioDocumento = "";
        $destinatarioRazonSocial = "";
    // echo json_encode($data);
        if(ValidateHelper::validateProperty($movement, ['almacen_destino.company.document_type_code'])){ 
            $destinatarioTipoDocumento = $movement['almacen_destino']['company']['document_type_code'];
        }
        else{
            $destinatarioTipoDocumento = $data['almacen_destino']['document_type_code'];
        }

        if(ValidateHelper::validateProperty($movement, ['almacen_destino.company.document'])){ 
            $destinatarioDocumento = $movement['almacen_destino']['company']['document'];
        }
        else{
            $destinatarioDocumento = $data['almacen_destino']['document'];
        }

        if(ValidateHelper::validateProperty($movement, ['almacen_destino.company.name'])){ 
            $destinatarioRazonSocial = $movement['almacen_destino']['company']['name'];
        }
        else{
            $destinatarioRazonSocial = $data['almacen_destino']['name'];
        }

        $destinatarioCorreoPrincipal = $data['almacen_destino']['email_principal'];
        $destinatarioCorreoSecundario = $data['almacen_destino']['email_secondary'];

        $generalFechaEmision = $data['fecha_emision'];
        $generalSerie = $data['serie'];
        $generalNumero = $data['numero'];
        $generalObservacion = $data['observacion'];
        $generalHoraEmision = $data['hora_emision'];

        $trasladoPeso = $data['peso'];
        $trasladoCantidad = count($movement['detalle']);

        $transporteModalidad = "01";
        $transporteFechaInicio = $data['transporte']['fecha_inicio'];

        $transportePublicoTipoDocumento = $data['transporte']['tipo_documento'];
        $transportePublicoDocumento = $data['transporte']['documento'];
        $transportePublicoRazonSocial = $data['transporte']['razon_social'];
        $transportePublicoNumeroMTC = $data['transporte']['numero_mtc'];

        $puntoPartidaUbigeo = "";
        $puntoPartidaDireccion = "";
        $puntoPartidaDocumento = "";

        if(ValidateHelper::validateProperty($movement, ['almacen_partida.district.code'])){ 
            $puntoPartidaUbigeo = $movement['almacen_partida']['district']['code'];
        }
        else{
            $puntoPartidaUbigeo = $data['almacen_partida']['ubigeo'];
        }

        if(ValidateHelper::validateProperty($movement, ['almacen_partida.direccion_alm'])){ 
            $puntoPartidaDireccion = $movement['almacen_partida']['direccion_alm'];
        }
        else{
            $puntoPartidaDireccion = $data['almacen_partida']['address'];
        }

        if(ValidateHelper::validateProperty($movement, ['almacen_partida.company.document'])){ 
            $puntoPartidaDocumento = $movement['almacen_partida']['company']['document'];
        }
        else{
            $puntoPartidaDocumento = $data['almacen_partida']['document'];
        }

        $puntoLlegadaUbigeo = "";
        $puntoLlegadaDireccion = "";
        $puntoLlegadaDocumento = "";

        if(ValidateHelper::validateProperty($movement, ['almacen_destino.district.code'])){ 
            $puntoLlegadaUbigeo = $movement['almacen_destino']['district']['code'];
        }
        else{
            $puntoLlegadaUbigeo = $data['almacen_destino']['ubigeo'];
        }

        if(ValidateHelper::validateProperty($movement, ['almacen_destino.direccion_alm'])){ 
            $puntoLlegadaDireccion = $movement['almacen_destino']['direccion_alm'];
        }
        else{
            $puntoLlegadaDireccion = $data['almacen_destino']['address'];
        }

        if(ValidateHelper::validateProperty($movement, ['almacen_destino.company.document'])){ 
            $puntoLlegadaDocumento = $movement['almacen_destino']['company']['document'];
        }
        else{
            $puntoLlegadaDocumento = $data['almacen_destino']['document'];
        }

        $bienes = [];

        foreach($movement['detalle'] as $item){
            $bienes['en_BienesGRR'] = [
                'at_Cantidad' => $item['cant_mde'],
                'at_UnidadMedida' => $item['um_mde'],
                'at_Descripcion' => $item['des_mde'],
                'at_Codigo' => $item['cod_inv']
            ];
        }

        $result = [
            'ent_GuiaRemisionRemitente' => [
                'ent_RemitenteGRR' => [
                    'at_NumeroDocumentoIdentidad' => $remitenteDocumento,
                    'at_RazonSocial' => $remitenteRazonSocial,
                    'at_NombreComercial' => $remitenteNombreComercial
                ],
                'ent_DestinatarioGRR' => [
                    'at_TipoDocumentoIdentidad' => $destinatarioTipoDocumento,
                    'at_NumeroDocumentoIdentidad' => $destinatarioDocumento,
                    'at_RazonSocial' => $destinatarioRazonSocial,
                    'ent_Correo' => [
                        'at_CorreoPrincipal' => $destinatarioCorreoPrincipal,
                        'aa_CorreoSecundario' => [
                            'string' => $destinatarioCorreoSecundario
                        ]
                    ]
                ],
                'ent_DatosGeneralesGRR' => [
                    'at_FechaEmision' => $generalFechaEmision,
                    'at_Serie' => $generalSerie,
                    'at_Numero' => $generalNumero,
                    'at_Observacion' => $generalObservacion,
                    'at_HoraEmision' => $generalHoraEmision,
                    'ent_InformacionTrasladoGRR' => [
                        'at_CodigoMotivo' => '04',
                        'ent_InformacionPesoBrutoGRR' => [
                            'at_Peso' => $trasladoPeso,
                            'at_UnidadMedida' => 'KGM',
                            'at_Cantidad' => $trasladoCantidad,
                        ],
                        'l_InformacionTransporteGRR' => [
                            'en_InformacionTransporteGRR' => [
                                'at_Modalidad' => $transporteModalidad,
                                'at_FechaInicio' => $transporteFechaInicio,
                                'ent_TransportePublicoGRR' => [
                                    'at_TipoDocumentoIdentidad' => $transportePublicoTipoDocumento,
                                    'at_NumeroDocumentoIdentidad' => $transportePublicoDocumento,
                                    'at_RazonSocial' => $transportePublicoRazonSocial,
                                    'at_NumeroMTC' => $transportePublicoNumeroMTC
                                ]
                            ]
                        ],
                        'ent_PuntoPartidaGRR' => [
                            'at_Ubigeo' => $puntoPartidaUbigeo,
                            'at_DireccionCompleta' => $puntoPartidaDireccion,
                            'at_CodigoEstablecimiento' => '0000',
                            'at_NumeroDocumentoIdentidad' => $puntoPartidaDocumento
                        ],
                        'ent_PuntoLlegadaGRR' => [
                            'at_Ubigeo' => $puntoLlegadaUbigeo,
                            'at_DireccionCompleta' => $puntoLlegadaDireccion,
                            'at_CodigoEstablecimiento' => '0003',
                            'at_NumeroDocumentoIdentidad' => $puntoLlegadaDocumento
                        ],
                        'l_BienesGRR' => $bienes
                    ]
                ],
                'at_ControlOtorgamiento' => 1
            ]
        ];

        return $result;
    }
}