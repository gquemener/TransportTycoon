<?php
declare(strict_types=1);

namespace App\TransportTycoon\Domain\ProcessManager;

use App\ServiceBus\CommandBus;
use App\TransportTycoon\Domain\Event\VehicleHasParkedInFacility;
use App\TransportTycoon\Domain\Command\UnloadVehicle;

final class VehicleUnloading
{
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function onVehicleHasParkedInFacility(VehicleHasParkedInFacility $event)
    {
        $vehicle = $event->vehicle();

        if ($vehicle->hasLoad()) {
            $this->commandBus->dispatch(new UnloadVehicle($event->aggregateRoot(), [
                'vehicle' => $vehicle,
            ]));
        }
    }
}
