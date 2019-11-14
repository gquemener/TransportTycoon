<?php
declare(strict_types=1);

namespace App\Tracking\Domain\ProcessManager;

use App\Tracking\Domain\Model\Facility;
use App\ServiceBus\CommandBus;
use App\Tracking\Domain\Model\CargoRepository;
use App\Tracking\Domain\Command\LoadPendingCargo;
use App\TraficRegulation\Domain\Event\VehicleHasBeenAdded;
use App\Tracking\Domain\Model\Vehicle;

final class LoadEmptyVehicle
{
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function onVehicleHasBeenAdded(VehicleHasBeenAdded $event): void
    {
        $this->commandBus->dispatch(
            new LoadPendingCargo(
                Vehicle::create(
                    $event->vehicleFleetId(),
                    $event->name(),
                ),
                Facility::named($event->position()->toString()),
            )
        );
    }
}
