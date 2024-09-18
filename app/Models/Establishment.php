<?php
class Establishment {
    const TABLE_NAME = 'establishments';

    private $id;
    private $code;
    private $type;
    private $address;
    private $start_serie;
    private $start_number;
    private $length_number;
    private $ubigeo_id;

    public function __construct(
        $code,
        $type,
        $address,
        $start_serie,
        $start_number,
        $length_number,
        $ubigeo_id
    ) {
        $this->id = $id;
        $this->code = $code;
        $this->type = $type;
        $this->address = $address;
        $this->start_serie = $start_serie;
        $this->start_number = $start_number;
        $this->length_number = $length_number;
        $this->ubigeo_id = $ubigeo_id;
    }

    // Getters y setters

    public function getId() {
        return $this->id;
    }

    public function getEntity(){
        return [
            "id" => $this->id,
            "code" => $this->code,
            "type" => $this->type,
            "address" => $this->address,
            "start_serie" => $this->start_serie,
            "start_number" => $this->start_number,
            "length_number" => $this->length_number,
            "ubigeo_id" => $this->ubigeo_id
        ];
    }
}