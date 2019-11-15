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
        $this->vehicleToOrigins[$event->vehicleFleetId()->toString()][$event->vehicleName()] =
            $event->vehiclePosition();
    }

    public function onCargoWasRegistered(CargoWasRegistered $event): void
    {
        $this->cargoToDestinations[$event->cargoId()->toString()] = $event->destination();
    }

    public function onCargoWasLoaded(CargoWasLoaded $event): void
    {
        $cargoId = $event->cargoId();

        $this->cargoToVehicles[$cargoId->toString()] = [
            'vehicleFleetId' => $event->vehicleFleetId(),
            'vehicleName' => $event->vehicleName(),
        ];

        if (!isset($this->cargoToDestinations[$cargoId->toString()])) {
            throw new \RuntimeException(sprintf('Destination of cargo "%s" is unknown', $cargoId->toString()));
        }

        $destination = $this->cargoToDestinations[$cargoId->toString()];
        unset($this->cargoToDestinations[$cargoId->toString()]);

        $this->commandBus->dispatch(
            new ComputeVehicleRoute(
                $event->vehicleFleetId(),
                $event->vehicleName(),
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

        if (!isset($this->vehicleToOrigins[$vehicleFleetId->toString()][$vehicleName])) {
            throw new \RuntimeException(sprintf(
                'Could not retrieve vehicle origin: unknown vehicle "%s" from vehicle fleet "%s"',
                $vehicleName,
                $vehicleFleetId->toString()
            ));
        }
        $origin = $this->vehicleToOrigins[$vehicleFleetId->toString()][$vehicleName];
        unset($this->cargoToVehicles[$cargoId->toString()]);

        $this->commandBus->dispatch(
            new ComputeVehicleRoute(
                $vehicleFleetId,
                $vehicleName,
                $origin
            )
        );
    }
}
