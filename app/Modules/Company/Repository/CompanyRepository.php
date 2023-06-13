<?php
require_once __DIR__ . '/../../Common/Repositories/CommonRepository.php';
require_once __DIR__ . '/../../../Models/Company.php';
require_once __DIR__ . '/../Contract/ICompany.php';

class CompanyRepository extends CommonRepository implements ICompany {
    public function __construct() {
        parent::__construct(Company::class);
    }

    public function validateCompany($documentType, $document){
        try{
            $data = null;
            $result = self::query('
                SELECT 
                    *
                FROM companies
                WHERE 
                    companies.document_type_id = '.$documentType.' AND
                    companies.document = '.$document.'
                LIMIT 1
            ');

            if($result){
                if(is_array($result)){
                    if(count($result) > 0){
                        return $result[0];
                    }
                }
            }
        }
        catch(Exception $e){

        }
        return null;
    }
}