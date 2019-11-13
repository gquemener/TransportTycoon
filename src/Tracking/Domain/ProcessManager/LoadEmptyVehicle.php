<?php
declare(strict_types=1);

namespace App\Tracking\Domain\ProcessManager;

use App\TraficRegulation\Domain\Event\VehicleWasRegistered;
use App\Tracking\Domain\Model\Facility;
use App\ServiceBus\CommandBus;
use App\Tracking\Domain\Model\CargoRepository;
use App\Tracking\Domain\Command\LoadPendingCargo;

final class LoadEmptyVehicle
{
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function onVehicleWasRegistered(VehicleWasRegistered $event): void
    {
        $this->commandBus->dispatch(
            new LoadPendingCargo($event->vehicleId(), Facility::named($event->position()->toString()))
        );
    }
}
