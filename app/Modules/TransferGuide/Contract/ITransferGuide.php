<?php
require_once __DIR__ . '/../../Common/Repositories/ICommonRepository.php';

interface ITransferGuide extends ICommonRepository
{
  public function findWithPaginate($filters);
  public function findOneWithDetails($id);
  public function getMaxSerieNumber($serie);
}