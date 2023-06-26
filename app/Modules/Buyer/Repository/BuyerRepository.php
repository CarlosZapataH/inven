<?php
require_once __DIR__ . '/../../Common/Repositories/CommonRepository.php';
require_once __DIR__ . '/../../../Models/Buyer.php';
require_once __DIR__ . '/../Contract/IBuyer.php';

class BuyerRepository extends CommonRepository implements IBuyer {
    public function __construct() {
        parent::__construct(Buyer::class);
    }

}