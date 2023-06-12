<?php
class Vehicle {
    const TABLE_NAME = 'vehicles';

    private $id;
    private $plate;
    private $transfer_guide_id;

    public function __construct(
        $id,
        $plate,
        $transfer_guide_id
    ) {
        $this->id = $id;
        $this->plate = $plate;
        $this->transfer_guide_id = $transfer_guide_id;
    }

    // Getters y setters

    public function getId() {
        return $this->id;
    }

    public function getEntity(){
        return [
            "id" => $this->id,
            "plate" => $this->plate,
            "transfer_guide_id" => $this->transfer_guide_id
        ];
    }
}