<?php
require_once __DIR__ . '/../../Common/Repositories/CommonRepository.php';
require_once __DIR__ . '/../../../Models/TransferGuide.php';
require_once __DIR__ . '/../Contract/ITransferGuide.php';

class TransferGuideRepository extends CommonRepository implements ITransferGuide {
    public function __construct() {
        parent::__construct(TransferGuide::class);
    }
}