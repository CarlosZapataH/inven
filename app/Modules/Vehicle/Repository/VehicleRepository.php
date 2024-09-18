<?php
require_once __DIR__ . '/../../Common/Repositories/CommonRepository.php';
require_once __DIR__ . '/../../../Models/Vehicle.php';
require_once __DIR__ . '/../Contract/IVehicle.php';

class VehicleRepository extends CommonRepository implements IVehicle {
    public function __construct() {
        parent::__construct(Vehicle::class);
    }

}