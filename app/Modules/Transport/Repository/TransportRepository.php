<?php
require_once __DIR__ . '/../../Common/Repositories/CommonRepository.php';
require_once __DIR__ . '/../../../Models/Transport.php';
require_once __DIR__ . '/../Contract/ITransport.php';

class TransportRepository extends CommonRepository implements ITransport {
    public function __construct() {
        parent::__construct(Transport::class);
    }

}