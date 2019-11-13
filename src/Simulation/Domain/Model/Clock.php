<?php
declare(strict_types=1);

namespace Simulation\Domain\Model;

final class Clock
{
    private $elapsedHours = 0;

    private function __construct()
    {
    }

    public function start(): void
    {
        ++$this->elapsedHours;
    }

    public function elapsedHours(): int
    {
        return $this->elapsedHours;
    }
}
