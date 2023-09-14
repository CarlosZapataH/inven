<?php
require_once __DIR__ . '/../../Common/Repositories/CommonRepository.php';
require_once __DIR__ . '/../../../Models/Profile.php';
require_once __DIR__ . '/../Contract/IProfile.php';

class ProfileRepository extends CommonRepository implements IProfile {
    public function __construct() {
        parent::__construct(Profile::class);
    }
}