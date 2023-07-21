<?php
require_once __DIR__ . '/../../Common/Requests/CommonRequest.php';

class TransferBetweenCompanyRequest extends CommonRequest
{
    // GUIDE
    public function validateGuide($data, $send, $requireDescription)
    {
        $rules = self::validateGuideRules($send, $requireDescription);
        $messages = self::validateGuideMessages();

        return $this->validate($data, $rules, $messages);
    }

    private function validateGuideRules($send, $requireDescription){
        $rules = [];
        if($send){
            $rules = [
                'id' => [['nullable']],
                'name' => [['required']],
                'detail' => [['array']],
                'motive_code' => [['required']],
                'observations' => [['nullable']],
                'total_witght' => [['required']],
                'total_quantity' => [['required']],
                'transport_modality' => [['required']]
            ];
        }
        else{
            $rules = [
                'id' => [['nullable']],
                'name' => [['nullable']],
                'detail' => [['array']],
                'motive_code' => [['required']],
                'observations' => [['nullable']],
                'total_witght' => [['nullable']],
                'total_quantity' => [['nullable']],
                'transport_modality' => [['nullable']]
            ];
        }

        if($requireDescription){
            $rules['motive_description'] = [['required']];
        }

        return $rules;
    }

    private function validateGuideMessages(){
        return [
            // en_BienesGRR
            'serie' => [
                'required' => 'La serie es obligatoria.',
            ],
            'name' => [
                'required' => 'El nombre es obligatorio.',
            ],
            'number' => [
                'required' => 'El número de serie es obligatorio.',
            ],
            'date_issue' => [
                'required' => 'La fecha de emisión es obligatoria.',
            ],
            'time_issue' => [
                'required' => 'El hora de emisión es obligatoria.',
            ],
            'total_witght' => [
                'required' => 'El peso total es obligatorio.',
            ],
            'total_quantity' => [
                'required' => 'La cantidad total es obligatoria.',
            ],
            'transport_modality' => [
                'required' => 'El modalidad del transporte es obligatoria.',
            ]
        ];
    }

    // COMPANY
    public function validateCompany($data, $rules)
    {
        $messages = self::validateCompanyMessages();
        return $this->validate($data, $rules, $messages);
    }

    private function validateCompanyMessages(){
        return [
            'name' => [
                'required' => 'El nombre de la empresa es obligatorio.',
            ],
            'commercial_name' => [
                'required' => 'El nombre comercial es obligatorio.',
            ],
            'document_types_id' => [
                'required' => 'El tipo de documento es obligatorio.',
            ],
            'document' => [
                'required' => 'El documento es obligatorio.',
            ]
        ];
    }

    // DETAIL
    public function validateDetail($data)
    {
        $rules = self::validateDetailRules();
        $messages = self::validateDetailMessages();

        return $this->validate($data, $rules, $messages);
    }

    private function validateDetailRules(){
        return [
            // en_BienesGRR
            'movement_id' => [['required']],
            'movement_detail_id' => [['required']],
            'inventory_id' => [['required']],
            'unit_measure_sunat' => [['required']],
            'additional_description' => [['nullable']]
        ];
    }

    private function validateDetailMessages(){
        return [
            // en_BienesGRR
            'movement_id' => [
                'required' => 'El id de transferencia es obligatorio.',
            ],
            'movement_detail_id' => [
                'required' => 'El id de detalle de transferencia es obligatorio.',
            ],
            'inventory_id' => [
                'required' => 'El id de inventario es obligatorio.',
            ],
            'unit_measure_sunat' => [
                'required' => 'La unidad de medida del inventario es obligatoria.',
            ]
        ];
    }

    public function validateStore($data, $rulesAdd)
    {
        $rules = array_merge(self::validateStoreRules(), $rulesAdd);
        $messages = self::validateStoreMessages();

        return $this->validate($data, $rules, $messages);
    }

    public function validatedTransportPublic($data)
    {
        $rules = self::validateStoreRulesTransportPublic();
        $messages = self::validateStoreMessagesTransportPublic();

        return $this->validate($data, $rules, $messages);
    }

    public function validatedTransportPrivate($data)
    {
        $rules = self::validateStoreRulesTransportPrivate();
        $messages = self::validateStoreMessagesTransportPrivate();

        return $this->validate($data, $rules, $messages);
    }

    public function validatedVehicle($data)
    {
        $rules = self::validateStoreRulesVehicle();
        $messages = self::validateStoreMessagesVehicle();

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
            'transport_modality' => [
                'required' => 'La modalidad de transporte es obligatoria.',
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

    public function validateStoreTransportPublic($data, $send)
    {
        $rules = self::validateStoreRulesTransportPublic($send);
        $messages = self::validateStoreMessagesTransportPublic();

        return $this->validate($data, $rules, $messages);
    }

    public function validateStoreTransportPrivate($data, $send)
    {
        $rules = self::validateStoreRulesTransportPrivate($send);
        $messages = self::validateStoreMessagesTransportPrivate();

        return $this->validate($data, $rules, $messages);
    }

    public function validateStoreVehicle($data)
    {
        $rules = self::validateStoreRulesVehicle();
        $messages = self::validateStoreMessagesVehicle();

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

    // TRANSPORT PUBLIC
    private function validateStoreRulesTransportPublic($send){
        if($send){
            return [
                // en_BienesGRR
                'start_date' => [['required']],
                'document_type_code' => [['required']],
                'document' => [['required']],
                'company_name' => [['required']],
                'mtc_number' => [['required']]
            ];
        }
        else{
            return [
                // en_BienesGRR
                'start_date' => [['nullable']],
                'document_type_code' => [['nullable']],
                'document' => [['required']],
                'company_name' => [['nullable']],
                'mtc_number' => [['nullable']]
            ];
        }
    }

    private function validateStoreMessagesTransportPublic(){
        return [
            // en_BienesGRR
            'start_date' => [
                'required' => 'La fecha de inicio de transporte es obligatoria.',
            ],
            'document_type_code' => [
                'required' => 'El tipo de documento de transporte es obligatorio.',
            ],
            'document' => [
                'required' => 'El documento de transporte es obligatorio.',
            ],
            'company_name' => [
                'required' => 'La razón social del transporte es obligatorio.',
            ],
            'mtc_number' => [
                'required' => 'El número MTC del transporte es obligatorio.',
            ]
        ];
    }

    // TRANSPORT PRIVATE
    private function validateStoreRulesTransportPrivate($send){
        if($send){
            return [
                'start_date' => [['required']],
                'document_type_code' => [['required']],
                'document' => [['required']],
                'license' => [['required']],
                'name' => [['required']],
                'last_name' => [['required']]
            ];
        }
        else{
            return [
                'start_date' => [['nullable']],
                'document_type_code' => [['nullable']],
                'document' => [['required']],
                'license' => [['nullable']],
                'name' => [['nullable']],
                'last_name' => [['nullable']]
            ];
        }
    }

    private function validateStoreMessagesTransportPrivate(){
        return [
            // en_BienesGRR
            'start_date' => [
                'required' => 'La fecha de inicio de transporte es obligatoria.',
            ],
            'document_type_code' => [
                'required' => 'El tipo de documento de transporte es obligatorio.',
            ],
            'documento' => [
                'required' => 'El documento de transporte es obligatorio.',
            ],
            'license' => [
                'required' => 'La licencia de transporte es obligatoria.',
            ],
            'name' => [
                'required' => 'El nombre del transporte es obligatorio.',
            ],
            'last_name' => [
                'required' => 'El apellido del transporte es obligatorio.',
            ]
        ];
    }

    // VEHICLE
    private function validateStoreRulesVehicle(){
        return [
            // en_BienesGRR
            'plate' => [['required']]
        ];
    }

    private function validateStoreMessagesVehicle(){
        return [
            // en_BienesGRR
            'plate' => [
                'required' => 'La placa de transporte es obligatoria.',
            ]
        ];
    }

    // PROVIDER
    public function validateProvider($data, $send)
    {
        $rules = self::validateProviderRules($send);
        $messages = self::validateProviderMessages();

        return $this->validate($data, $rules, $messages);
    }

    private function validateProviderRules(){
        if($send){
            return  [
                'document_type_code' => [['required']],
                'document' => [['required']],
                'name' => [['required']]
            ];
        }
        else{
            return  [
                'document_type_code' => [['nullable']],
                'document' => [['nullable']],
                'name' => [['required']]
            ];
        }
    }

    private function validateProviderMessages(){
        return [
            'document_type_code' => [
                'required' => 'El tipo de documento del proveedor es obligatorio.',
            ],
            'document' => [
                'required' => 'El documento del proveedor es obligatorio.',
            ],
            'name' => [
                'required' => 'El nombre del proveedor es obligatorio.',
            ]
        ];
    }

    // BUYER
    public function validateBuyer($data, $send)
    {
        $rules = self::validateBuyerRules($send);
        $messages = self::validateBuyerMessages();

        return $this->validate($data, $rules, $messages);
    }

    private function validateBuyerRules($send){
        if($send){
            return  [
                'document_type_code' => [['required']],
                'document' => [['required']],
                'name' => [['required']]
            ];
        }
        else{
            return  [
                'document_type_code' => [['nullable']],
                'document' => [['nullable']],
                'name' => [['required']]
            ];
        }
    }

    private function validateBuyerMessages(){
        return [
            'document_type_code' => [
                'required' => 'El tipo de documento del comprador es obligatorio.',
            ],
            'document' => [
                'required' => 'El documento del comprador es obligatorio.',
            ],
            'name' => [
                'required' => 'El nombre del comprador es obligatorio.',
            ]
        ];
    }
}