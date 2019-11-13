<?php
declare(strict_types=1);

namespace App\Tracking\Domain\Model;

use App\Tracking\Domain\Event\CargoWasRegistered;
use App\AggregateRoot;
use App\TraficRegulation\Domain\Model\VehicleId;
use App\Tracking\Domain\Event\CargoWasLoaded;

final class Cargo
{
    use AggregateRoot;

    private $id;
    private $position;
    private $destination;
    private $vehicleId;

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

    public function loadInto(VehicleId $vehicleId): void
    {
        $this->vehicleId = $vehicleId;
        $this->record(new CargoWasLoaded($vehicleId, $this->destination));
    }

    public function id(): CargoId
    {
        return $this->id;
    }

    public function position(): Facility
    {
        return $this->position;
    }

    public function isPending(): bool
    {
        return null === $this->vehicleId;
    }
}
