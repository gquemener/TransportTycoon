<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\ProcessManager;

use App\ServiceBus\CommandBus;
use App\Tracking\Domain\Event\CargoWasLoaded;
use App\TraficRegulation\Domain\Command\ComputeVehicleRoute;
use App\TraficRegulation\Domain\Model\Facility;

final class PlanVehicleRoute
{
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function onCargoWasLoaded(CargoWasLoaded $event): void
    {
        $vehicleId = $event->vehicleId();
        $destination = $event->destination();

        $this->commandBus->dispatch(
            new ComputeVehicleRoute(
                $vehicleId,
                Facility::named($destination->toString())
            )
        );
    }
}
