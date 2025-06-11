<?php

namespace App\Message;

class AddPointsToUsersMessage
{
  private int $points;

  public function __construct(int $points = 1000)
  {
    $this->points = $points;
  }

  public function getPoints(): int
  {
    return $this->points;
  }
}
