<?php
require_once __DIR__ . '/../../Common/Repositories/ICommonRepository.php';

interface IUbigeo extends ICommonRepository
{
    public function getDepartments();
    public function getProvinces($departmentId = null);
    public function getDistricts($provinceId = null);
    public function getDistrict($ubigeoCode);
}