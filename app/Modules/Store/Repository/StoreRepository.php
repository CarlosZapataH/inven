<?php
require_once __DIR__ . '/../../Common/Repositories/CommonRepository.php';
require_once __DIR__ . '/../../../Models/Store.php';
require_once __DIR__ . '/../Contract/IStore.php';

class StoreRepository extends CommonRepository implements IStore {
    public function __construct() {
        parent::__construct(Store::class);
    }

}