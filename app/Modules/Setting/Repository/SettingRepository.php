<?php
require_once __DIR__ . '/../../Common/Repositories/CommonRepository.php';
require_once __DIR__ . '/../../../Models/Setting.php';
require_once __DIR__ . '/../Contract/ISetting.php';

class SettingRepository extends CommonRepository implements ISetting {
    public function __construct() {
        parent::__construct(Setting::class);
    }

}