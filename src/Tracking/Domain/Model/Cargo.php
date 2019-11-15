<?php
declare(strict_types=1);

namespace App\Tracking\Domain\Model;

use App\Tracking\Domain\Event\CargoWasRegistered;
use App\AggregateRoot;
use App\TraficRegulation\Domain\Model\VehicleId;
use App\Tracking\Domain\Event\CargoWasLoaded;
use App\Tracking\Domain\Event\CargoWasUnloaded;

final class Cargo
{
    use AggregateRoot;

    private $id;
    private $position;
    private $destination;
    private $vehicle;

    public function __construct(
        CargoId $id,
        Facility $position,
        Facility $destination
    ) {
        $this->id = $id;
        $this->position = $position;
        $this->destination = $destination;
    }

    public static function register(
        CargoId $id,
        Facility $position,
        Facility $destination
    ): self {
        $self = new self($id, $position, $destination);
        $self->record(new CargoWasRegistered($id, $position, $destination));

        return $self;
    }

    public function loadInto(Vehicle $vehicle): void
    {
        $this->vehicle = $vehicle;
        $this->record(new CargoWasLoaded($this->id, $vehicle));
    }

    public function unload(Facility $position): void
    {
        if (!$this->isLoaded()) {
            throw new \RuntimeException(sprintf(
                'Cargo "%s" is not loaded',
                $this->id->toString()
            ));
        }

        $this->vehicle = null;
        $this->position = $position;

        $this->record(new CargoWasUnloaded($this->id, $position));
    }

    public function id(): CargoId
    {
        return $this->id;
    }

    public function position(): Facility
    {
        return $this->position;
    }

    public function vehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    public function isPending(): bool
    {
        return null === $this->vehicle;
    }

    public function isLoaded(): bool
    {
        return $this->vehicle instanceof Vehicle;
    }
}
