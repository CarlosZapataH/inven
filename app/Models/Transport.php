<?php
class Transport {
    const TABLE_NAME = 'transports';

    private $id;
    private $modality;
    private $document_type;
    private $document;
    private $start_date;
    private $company_name;
    private $mtc_number;
    private $license;
    private $name;
    private $last_name;
    private $transfer_guide_id;

    public function __construct(
        $id,
        $modality,
        $document_type,
        $document,
        $start_date,
        $company_name,
        $mtc_number,
        $license,
        $name,
        $last_name,
        $transfer_guide_id
    ) {
        $this->id = $id;
        $this->modality = $modality;
        $this->document_type = $document_type;
        $this->document = $document;
        $this->start_date = $start_date;
        $this->company_name = $company_name;
        $this->mtc_number = $mtc_number;
        $this->license = $license;
        $this->name = $name;
        $this->last_name = $last_name;
        $this->transfer_guide_id = $transfer_guide_id;
    }

    // Getters y setters

    public function getId() {
        return $this->id;
    }

    public function getEntity(){
        return [
            "id" => $this->id,
            "modality" => $this->modality,
            "document_type" => $this->document_type,
            "document" => $this->document,
            "start_date" => $this->start_date,
            "company_name" => $this->company_name,
            "mtc_number" => $this->mtc_number,
            "license" => $this->license,
            "name" => $this->name,
            "last_name" => $this->last_name,
            "transfer_guide_id" => $this->transfer_guide_id
        ];
    }
}