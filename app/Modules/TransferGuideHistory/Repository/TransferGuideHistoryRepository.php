<?php
require_once __DIR__ . '/../../Common/Repositories/CommonRepository.php';
require_once __DIR__ . '/../../../Models/TransferGuideHistory.php';
require_once __DIR__ . '/../Contract/ITransferGuideHistory.php';

class TransferGuideHistoryRepository extends CommonRepository implements ITransferGuideHistory {
    public function __construct() {
        parent::__construct(TransferGuideHistory::class);
    }

}