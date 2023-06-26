<?php
require_once __DIR__ . '/../../Common/Repositories/CommonRepository.php';
require_once __DIR__ . '/../../../Models/Provider.php';
require_once __DIR__ . '/../Contract/IProvider.php';

class ProviderRepository extends CommonRepository implements IProvider {
    public function __construct() {
        parent::__construct(Provider::class);
    }

}