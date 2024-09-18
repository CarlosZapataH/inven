<?php
require_once __DIR__ . '/../../Common/Repositories/CommonRepository.php';
require_once __DIR__ . '/../../../Models/Establishment.php';
require_once __DIR__ . '/../Contract/IEstablishment.php';

class EstablishmentRepository extends CommonRepository implements IEstablishment {
    public function __construct() {
        parent::__construct(Establishment::class);
    }

}