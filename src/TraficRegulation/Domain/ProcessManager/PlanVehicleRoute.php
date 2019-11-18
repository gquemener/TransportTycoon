<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\ProcessManager;

use App\ServiceBus\CommandBus;
use App\Tracking\Domain\Event\CargoWasLoaded;
use App\TraficRegulation\Domain\Command\ComputeVehicleRoute;
use App\TraficRegulation\Domain\Model\Facility;
use App\Tracking\Domain\Event\CargoWasRegistered;
use App\Tracking\Domain\Event\CargoWasUnloaded;
use App\TraficRegulation\Domain\Event\VehicleHasBeenAdded;
use App\TraficRegulation\Domain\Model\VehicleFleetId;

final class PlanVehicleRoute
{
    private $commandBus;
    private $cargoToDestinations = [];
    private $cargoToVehicles = [];
    private $vehicleToOrigins = [];

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function onVehicleHasBeenAdded(VehicleHasBeenAdded $event): void
    {
        $vehicleFleetId = $event->vehicleFleetId();
        $vehicle = $event->vehicle();
        $this->vehicleToOrigins[$vehicleFleetId->toString()][$vehicle->name()] =
            $vehicle->position();
    }

    public function onCargoWasRegistered(CargoWasRegistered $event): void
    {
        $this->cargoToDestinations[$event->cargoId()->toString()] = $event->destination()->toString();
    }

    public function onCargoWasLoaded(CargoWasLoaded $event): void
    {
        $cargoId = $event->cargoId();
        $vehicle = $event->vehicle();

        $this->cargoToVehicles[$cargoId->toString()] = [
            'vehicleFleetId' => $vehicle->vehicleFleetId()->toString(),
            'vehicleName' => $vehicle->name(),
        ];

        if (!isset($this->cargoToDestinations[$cargoId->toString()])) {
            throw new \RuntimeException(sprintf('Destination of cargo "%s" is unknown', $cargoId->toString()));
        }

        $destination = $this->cargoToDestinations[$cargoId->toString()];

        $this->commandBus->dispatch(
            new ComputeVehicleRoute(
                $vehicle->vehicleFleetId(),
                $vehicle->name(),
                Facility::named($destination)
            )
        );
    }

    public function onCargoWasUnloaded(CargoWasUnloaded $event): void
    {
        $cargoId = $event->cargoId();
        if (!isset($this->cargoToVehicles[$cargoId->toString()])) {
            throw new \RuntimeException(sprintf(
                'Could not retrieve cargo vehicle: unknown cargo "%s"',
                $cargoId->toString()
            ));
        }

        [
            'vehicleFleetId' => $vehicleFleetId,
            'vehicleName' => $vehicleName
        ] = $this->cargoToVehicles[$cargoId->toString()];

        if (!isset($this->vehicleToOrigins[$vehicleFleetId][$vehicleName])) {
            throw new \RuntimeException(sprintf(
                'Could not retrieve vehicle origin: unknown vehicle "%s" from vehicle fleet "%s"',
                $vehicleName,
                $vehicleFleetId
            ));
        }
        $origin = $this->vehicleToOrigins[$vehicleFleetId][$vehicleName];
        unset($this->cargoToVehicles[$cargoId->toString()]);

        $this->commandBus->dispatch(
            new ComputeVehicleRoute(
                VehicleFleetId::fromString($vehicleFleetId),
                $vehicleName,
                $origin
            )
        );
    }
}
