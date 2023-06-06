<?php
require_once __DIR__ . '/../../Common/Repositories/ICommonRepository.php';

interface ITransitMovement extends ICommonRepository
{
  public function getTransitMovement($id);
}