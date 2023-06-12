<?php
class TransferGuide {
    const TABLE_NAME = 'transfers_guides';

    private $id;
    private $serie;
    private $number;
    private $date_issue;
    private $time_issue;
    private $observations;
    private $motive_code;
    private $motive_description;
    private $total_witght;
    private $unit_measure;
    private $total_quantity;
    private $movement_id;
    private $email_principal;
    private $email_secondary;
    private $transport_modality;
    private $created_at;
    private $updated_at;

    public function __construct(
        $id,
        $serie,
        $number,
        $date_issue,
        $time_issue,
        $observations,
        $motive_code,
        $motive_description,
        $total_witght,
        $unit_measure,
        $total_quantity,
        $movement_id,
        $email_principal,
        $email_secondary,
        $created_at,
        $updated_at
    ) {
        $this->id = $id;
        $this->serie = $serie;
        $this->number = $number;
        $this->date_issue = $date_issue;
        $this->time_issue = $time_issue;
        $this->observations = $observations;
        $this->motive_code = $motive_code;
        $this->motive_description = $motive_description;
        $this->total_witght = $total_witght;
        $this->unit_measure = $unit_measure;
        $this->total_quantity = $total_quantity;
        $this->movement_id = $movement_id;
        $this->email_principal = $email_principal;
        $this->email_secondary = $email_secondary;
        $this->transport_modality = $transport_modality;
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
            "serie" => $this->serie,
            "number" => $this->number,
            "date_issue" => $this->date_issue,
            "time_issue" => $this->time_issue,
            "observations" => $this->observations,
            "motive_code" => $this->motive_code,
            "motive_description" => $this->motive_description,
            "total_witght" => $this->total_witght,
            "unit_measure" => $this->unit_measure,
            "total_quantity" => $this->total_quantity,
            "movement_id" => $this->movement_id,
            "email_principal" => $this->email_principal,
            "email_secondary" => $this->email_secondary,
            "transport_modality" => $this->transport_modality,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at
        ];
    }
}