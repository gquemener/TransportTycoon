<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\ProcessManager;

use App\ServiceBus\CommandBus;
use App\Tracking\Domain\Event\CargoWasLoaded;
use App\TraficRegulation\Domain\Command\ComputeVehicleRoute;
use App\TraficRegulation\Domain\Model\Facility;
use App\Tracking\Domain\Event\CargoWasRegistered;

final class PlanVehicleRoute
{
    private $commandBus;
    private $cargoDestinations = [];

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function onCargoWasRegistered(CargoWasRegistered $event): void
    {
        $this->cargoDestinations[$event->cargoId()->toString()] = $event->destination();
    }

    public function onCargoWasLoaded(CargoWasLoaded $event): void
    {
        $cargoId = $event->cargoId();
        if (!isset($this->cargoDestinations[$cargoId->toString()])) {
            throw new \RuntimeException(sprintf('Destination of cargo "%s" is unknown', $cargoId->toString()));
        }

        $destination = $this->cargoDestinations[$cargoId->toString()];
        unset($this->cargoDestinations[$cargoId->toString()]);

        $this->commandBus->dispatch(
            new ComputeVehicleRoute(
                $event->vehicleFleetId(),
                $event->vehicleName(),
                Facility::named($destination)
            )
        );
    }
}
