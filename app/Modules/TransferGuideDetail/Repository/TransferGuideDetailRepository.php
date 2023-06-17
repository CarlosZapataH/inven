<?php
require_once __DIR__ . '/../../Common/Repositories/CommonRepository.php';
require_once __DIR__ . '/../../../Models/TransferGuideDetail.php';
require_once __DIR__ . '/../Contract/ITransferGuideDetail.php';

class TransferGuideDetailRepository extends CommonRepository implements ITransferGuideDetail {
    public function __construct() {
        parent::__construct(TransferGuideDetail::class);
    }

}