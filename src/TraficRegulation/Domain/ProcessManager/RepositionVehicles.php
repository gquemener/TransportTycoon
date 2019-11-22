<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\ProcessManager;

use App\ServiceBus\CommandBus;
use App\TraficRegulation\Domain\Event\VehicleFleetHasBeenCreated;
use App\TraficRegulation\Domain\Command\RepositionVehicleFleet;
use App\TraficRegulation\Domain\Model\VehicleFleetId;

final class RepositionVehicles
{
    private $commandBus;

    private $vehicleFleetIds = [];

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function onVehicleFleetHasBeenCreated(VehicleFleetHasBeenCreated $event): void
    {
        $this->vehicleFleetIds[] = $event->vehicleFleetId()->toString();
    }

    public function onOneHourHasPassed(): void
    {
        foreach ($this->vehicleFleetIds as $vehicleFleetId) {
            $this->commandBus->dispatch(
                new RepositionVehicleFleet(VehicleFleetId::fromString($vehicleFleetId))
            );
        }
    }
}
