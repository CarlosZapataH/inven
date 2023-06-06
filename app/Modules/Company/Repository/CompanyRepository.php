<?php
require_once '../../Common/Repositories/CommonRepository.php';
require_once '../../../Models/Company.php';
require_once '../Contract/ICompany.php';

class CompanyRepository extends CommonRepository implements ICompany {
    public function __construct() {
        parent::__construct(Company::class);
    }
}