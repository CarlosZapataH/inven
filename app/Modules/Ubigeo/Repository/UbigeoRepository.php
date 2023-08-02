<?php
require_once __DIR__ . '/../../Common/Repositories/CommonRepository.php';
require_once __DIR__ . '/../../../Models/Ubigeo.php';
require_once __DIR__ . '/../Contract/IUbigeo.php';

class UbigeoRepository extends CommonRepository implements IUbigeo {
    public function __construct() {
        parent::__construct(Ubigeo::class);
    }

    public function getDistrict($ubigeoCode){
        try{
            $data = null;

            $query = 'SELECT * FROM ubigeo WHERE codigo_inei = ' . $ubigeoCode;
            $result = self::query($query);


            if($result){
                if(is_array($result)){
                    if(count($result) > 0){
                        $data = $result[0];
                    }
                }
            }

            return $data;
        }
        catch(Exception $e){
            echo $e->getMessage();
        }
        return null;
    }
}