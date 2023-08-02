<?php
require_once __DIR__ . '/../../Common/Repositories/ICommonRepository.php';

interface IUbigeo extends ICommonRepository
{
    public function getDistrict($ubigeoCode);
}