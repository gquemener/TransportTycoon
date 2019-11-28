<?php
declare(strict_types=1);

namespace App\TransportTycoon\Domain\Model;

final class Cargo
{
    private $position;
    private $destination;

    private static $count = 0;

    private $id;

    public static function toWarehouseA(): self
    {
        return new self(FacilityName::WAREHOUSE_A());
    }

    public static function toWarehouseB(): self
    {
        return new self(FacilityName::WAREHOUSE_B());
    }

    private function __construct(
        FacilityName $destination
    ) {
        $this->id = ++self::$count;
        $this->destination = $destination;
    }

    public function position(): ?Position
    {
        return $this->position;
    }

    public function destination(): FacilityName
    {
        return $this->destination;
    }

    public function moveTo(Position $position): void
    {
        $this->position = $position;
    }

    public function isDelivered(): bool
    {
        return $this->position() instanceof Facility
            && $this->position()->name()->equals($this->destination);
    }

    public function isStoredInFacility(Facility $facility): bool
    {
        return $this->position() instanceof Facility
            && $this->position()->equals($facility);
    }

    public function toString(): string
    {
        return sprintf('Cargo %d (Destination: %s)', $this->id, $this->destination->toString());
    }
}
