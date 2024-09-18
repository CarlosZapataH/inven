<?php
class Company {
    const TABLE_NAME = 'companies';

    private $id;
    private $name;
    private $commercial_name;
    private $document_type;
    private $document;

    public function __construct($id, $name, $commercial_name, $document_type, $document) {
        $this->id = $id;
        $this->name = $name;
        $this->commercial_name = $commercial_name;
        $this->document_type = $document_type;
        $this->document = $document;
    }

    // Getters y setters

    public function getId() {
        return $this->id;
    }

    public function getEntity(){
        return [
            "id" => $this->id,
            "name" => $this->name,
            "commercial_name" => $this->commercial_name,
            "document_type" => $this->document_type,
            "document" => $this->document
        ];
    }
}