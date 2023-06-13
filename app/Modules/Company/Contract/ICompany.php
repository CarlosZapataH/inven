<?php
require_once __DIR__ . '/../../Common/Repositories/ICommonRepository.php';

interface ICompany extends ICommonRepository
{
    public function validateCompany($documentType, $document);
}