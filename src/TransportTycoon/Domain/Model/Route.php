<?php
declare(strict_types=1);

namespace App\TransportTycoon\Domain\Model;

final class Route
{
    private $origin;

    private $destination;

    private $eta;

    private function __construct(
        Facility $origin,
        Facility $destination,
        int $eta
    ) {
        $this->origin = $origin;
        $this->destination = $destination;
        $this->eta = $eta;
    }

    public static function between(
        Facility $origin,
        Facility $destination,
        int $eta
    ): self {
        return new self($origin, $destination, $eta);
    }

    public function origin(): Facility
    {
        return $this->origin;
    }

    public function destination(): Facility
    {
        return $this->destination;
    }

    public function eta(): int
    {
        return $this->eta;
    }
}
