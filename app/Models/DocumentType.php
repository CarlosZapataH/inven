<?php
class DocumentType {
    const TABLE_NAME = 'document_types';

    private $id;
    private $code;
    private $description;

    public function __construct($id, $code, $description) {
        $this->id = $id;
        $this->code = $code;
        $this->description = $description;
    }

    // Getters y setters

    public function getId() {
        return $this->id;
    }

    public function getEntity(){
        return [
            "id" => $this->id,
            "code" => $this->code,
            "description" => $this->description
        ];
    }
}