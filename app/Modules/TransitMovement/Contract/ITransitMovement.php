<?php
require_once __DIR__ . '/../../Common/Repositories/ICommonRepository.php';

interface ITransitMovement extends ICommonRepository
{
  public function findWithDetails($id, $available);
  public function findOneWithDetails($id);
  public function updateAvailable($id, $value);
}