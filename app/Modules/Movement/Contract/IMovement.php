<?php
require_once __DIR__ . '/../../Common/Repositories/ICommonRepository.php';

interface IMovement extends ICommonRepository
{
  public function getMovement($id);
}