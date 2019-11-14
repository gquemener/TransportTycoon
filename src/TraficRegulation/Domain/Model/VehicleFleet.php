<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\Model;

use App\AggregateRoot;
use App\TraficRegulation\Domain\Event\VehicleFleetHasBeenCreated;
use App\TraficRegulation\Domain\Event\VehicleHasBeenAdded;
use App\TraficRegulation\Domain\Event\VehicleFleetHasBeenRepositioned;
use App\TraficRegulation\Domain\Event\VehicleRouteHasBeenSet;

final class VehicleFleet
{
    use AggregateRoot;

    private $id;
    private $initialPosition;
    private $vehicles = [];

    private function __construct(VehicleFleetId $id, Facility $initialPosition)
    {
        $this->id = $id;
        $this->initialPosition = $initialPosition;
    }

    public static function create(VehicleFleetId $id, Facility $initialPosition): self
    {
        $self = new self($id, $initialPosition);
        $self->record(new VehicleFleetHasBeenCreated($id));

        return $self;
    }

    public function addVehicle(string $name): void
    {
        if (isset($this->vehicles[$name])) {
            throw new \InvalidArgumentException(sprintf('Vehicle "%s" has already been added to this fleet', $name));
        }

        $this->vehicles[$name] = Vehicle::register($name, clone $this->initialPosition);
        $this->record(new VehicleHasBeenAdded($this->id, $this->vehicles[$name]));
    }

    public function id(): VehicleFleetId
    {
        return $this->id;
    }

    public function setVehicleRoute(
        string $vehicleName,
        Facility $destination,
        RouteFinder $finder
    ): void {
        if (!isset($this->vehicles[$vehicleName])) {
            throw new \InvalidArgumentException(sprintf(
                'Vehicle fleet "%s" has no vehicle named "%s"',
                $this->id->toString(),
                $vehicleName
            ));
        }

        $this->vehicles[$vehicleName]->configureRoute($destination, $finder);

        $this->record(new VehicleRouteHasBeenSet(
            $this->id,
            $this->vehicles[$vehicleName]
        ));
    }

    public function reposition(): void
    {
        foreach ($this->vehicles as $vehicle) {
            // compute new position
        }

        $this->record(new VehicleFleetHasBeenRepositioned($this->id /**, new vehicle positions */));
    }
}
