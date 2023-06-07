<?php
require_once __DIR__ . '/../../Common/Requests/CommonRequest.php';

class TransitMovementGuideRequest extends CommonRequest
{
    public function validateStore($data, $rulesAdd)
    {
        $rules = array_merge(self::validateStoreRules(), $rulesAdd);
        $messages = self::validateStoreMessages();

        return $this->validate($data, $rules, $messages);
    }

    private function validateStoreRules(){
        return [
            // // ent_RemitenteGRR
            // 'almacen_partida.document' => [['required']],
            // 'almacen_partida.name' => [['required']],
            // 'almacen_partida.commercial_name' => [['required']],
            // // // ent_PuntoPartidaGRR
            // 'almacen_partida.ubigeo' => [['required']],
            // 'almacen_partida.address' => [['required']],
            // // ent_DestinatarioGRR
            // 'almacen_destino.document_type_code' => [['required']],
            // 'almacen_destino.document' => [['required']],
            // 'almacen_destino.name' => [['required']],
            // 'almacen_destino.email_principal' => [['required'], ['email']],
            // 'almacen_destino.email_secondary' => [['required'], ['email']],
            // // // ent_PuntoLlegadaGRR
            // 'almacen_destino.ubigeo' => [['required']],
            // 'almacen_destino.address' => [['required']],
            // ent_DatosGeneralesGRR
            // 'fecha_emision' => [['required']],
            // 'serie' => [['required']],
            // 'numero' => [['required']],
            // 'observacion' => [['required']],
            // 'hora_emision' => [['required']],
            // ent_InformacionTrasladoGRR
            // // ent_InformacionPesoBrutoGRR
            // 'codigo_motivo' => [['required']],
            // 'peso' => [['required']],
            // 'unidad_medida' => [['required']],
            // 'cantidad' => [['required']],
            // // en_InformacionTransporteGRR,
            // 'transporte.modalidad' => [['required']],
            // 'transporte.fecha_inicio' => [['required']],
            // 'transporte.tipo_documento' => [['required']],
            // 'transporte.documento' => [['required']],
            // 'transporte.razon_social' => [['required']],
            // 'transporte.numero_mtc' => [['required']],
            // en_BienesGRR
            // 'bienes' => [['required'],['array'], ['min_items:1']]
        ];
    }

    private function validateStoreMessages(){
        return [
            // ent_RemitenteGRR
            'almacen_partida.document' => [
                'required' => 'El documento del almacen de partida es obligatorio.',
            ],
            'almacen_partida.name' => [
                'required' => 'El nombre del almacen de partida es obligatorio.',
            ],
            'almacen_partida.commercial_name' => [
                'required' => 'El nombre comercial del almacen de partida es obligatorio.',
            ],
            // // ent_PuntoPartidaGRR
            'almacen_partida.ubigeo' => [
                'required' => 'El ubigeo del punto de partida es obligatorio.',
            ],
            'almacen_partida.address' => [
                'required' => 'La dirección del punto de partida es obligatorio.',
            ],
            // ent_DestinatarioGRR
            'almacen_destino.document_type_code' => [
                'required' => 'El tipo de documento del almacen de destino es obligatorio.',
            ],
            'almacen_destino.document' => [
                'required' => 'El documento del almacen de destino es obligatorio.',
            ],
            'almacen_destino.name' => [
                'required' => 'El nombre del almacen de destino es obligatorio.',
            ],
            'almacen_destino.email_principal' => [
                'required' => 'El correo electrónico principal del almacen de destino es obligatorio.',
                'email' => 'El correo electrónico principal del almacen de destino es inválido.'
            ],
            'almacen_destino.email_secondary' => [
                'required' => 'El correo electrónico secundario del almacen de destino es obligatorio.',
                'email' => 'El correo electrónico secundario del almacen de destino es inválido.'
            ],
            // // ent_PuntoLlegadaGRR
            'almacen_destino.ubigeo' => [
                'required' => 'El ubigeo del punto de llegada es obligatorio.',
            ],
            'almacen_destino.address' => [
                'required' => 'La dirección del punto de llegada es obligatorio.',
            ],
            // ent_DatosGeneralesGRR
            'fecha_emision' => [
                'required' => 'La fecha de emisión es obligatoria.',
            ],
            'serie' => [
                'required' => 'La serie es obligatoria.',
            ],
            'numero' => [
                'required' => 'El número de guia es obligatorio.',
            ],
            'observacion' => [
                'required' => 'La observación es obligatoria.',
            ],
            'hora_emision' => [
                'required' => 'La hora de emisión es obligatoria.',
            ],
            // ent_InformacionTrasladoGRR
            // // ent_InformacionPesoBrutoGRR
            'codigo_motivo' => [
                'required' => 'El código de motivo es obligatorio.',
            ],
            'peso' => [
                'required' => 'El peso bruto es obligatorio.',
            ],
            'unidad_medida' => [
                'required' => 'La unidad de medida del peso bruto es obligatoria.',
            ],
            'cantidad' => [
                'required' => 'La cantidad de transporte es obligatoria.',
            ],
            // // en_InformacionTransporteGRR,
            'transporte.modalidad' => [
                'required' => 'La modalidad de transporte es obligatoria.',
            ],
            'transporte.fecha_inicio' => [
                'required' => 'La fecha de inicio de transporte es obligatoria.',
            ],
            'transporte.tipo_documento' => [
                'required' => 'El tipo de documento del transportista es obligatorio.',
            ],
            'transporte.documento' => [
                'required' => 'La documento del transportista es obligatorio.',
            ],
            'transporte.razon_social' => [
                'required' => 'La razon social del transportista es obligatoria.',
            ],
            'transporte.numero_mtc' => [
                'required' => 'El número MTC del transportista es obligatorio.',
            ],
            // en_BienesGRR
            'bienes' => [
                'required' => 'Los bienes son obligatorios.',
                'array' => 'Los bienes deben ser una lista.',
            ]
        ];
    }

    public function validateStoreDetail($data)
    {
        $rules = self::validateStoreRulesDetail();
        $messages = self::validateStoreMessagesDetail();

        return $this->validate($data, $rules, $messages);
    }

    private function validateStoreRulesDetail(){
        return [
            // en_BienesGRR
            'cantidad' => [['required']],
            'unidad_medida' => [['required']],
            'descripcion' => [['required']],
            'codigo' => [['required']]
        ];
    }

    private function validateStoreMessagesDetail(){
        return [
            // en_BienesGRR
            'cantidad' => [
                'required' => 'La cantidad de los bienes es obligatoria.',
            ],
            'unidad_medida' => [
                'required' => 'La unidad de medida de los bienes es obligatoria.',
            ],
            'descripcion' => [
                'required' => 'La descripción de los bienes es obligatoria.',
            ],
            'codigo' => [
                'required' => 'El código de los bienes es obligatorio.',
            ]
        ];
    }
}