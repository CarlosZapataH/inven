<?php
class Buyer {
    const TABLE_NAME = 'buyers';

    private $id;
    private $document_type_code;
    private $document;
    private $name;
    private $transfer_guide_id;
    private $created_at;
    private $updated_at;

    public function __construct(
        $id,
        $document_type_code,
        $document,
        $name,
        $transfer_guide_id,
        $created_at,
        $updated_at
    ) {
        $this->id = $id;
        $this->document_type_code = $document_type_code;
        $this->document = $document;
        $this->name = $name;
        $this->transfer_guide_id = $transfer_guide_id;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    // Getters y setters

    public function getId() {
        return $this->id;
    }

    public function getEntity(){
        return [
            "id" => $this->id,
            "document_type_code" => $this->document_type_code,
            "document" => $this->document,
            "name" => $this->name,
            "transfer_guide_id" => $this->transfer_guide_id,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at
        ];
    }
}