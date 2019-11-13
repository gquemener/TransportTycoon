<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\Model;

final class Route
{
    private $destination;

    private $eta;

    private function __construct(Facility $destination, int $eta)
    {
        $this->destination = $destination;
        $this->eta = $eta;
    }

    public static function to(Facility $destination, int $eta): self
    {
        return new self($destination, $eta);
    }
}
