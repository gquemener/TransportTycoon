<?php
declare(strict_types=1);

namespace App\TransportTycoon\Domain\ProcessManager;

use App\ServiceBus\CommandBus;
use App\TransportTycoon\Domain\Event\VehicleWasLoaded;
use App\TransportTycoon\Domain\Command\FindVehicleDestination;
use App\TransportTycoon\Domain\Event\VehicleWasUnloaded;

final class PlanVehicleRoute
{
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function onVehicleWasLoaded(VehicleWasLoaded $event): void
    {
        $destination = $event->cargos()[0]->destination();
        $vehicle = $event->vehicle();
        $game = $event->aggregateRoot();
        $this->commandBus->dispatch(new FindVehicleDestination(
            $game,
            [
                'vehicle' => $vehicle,
                'destination' => $destination,
            ]
        ));
    }

    public function onVehicleWasUnloaded(VehicleWasUnloaded $event): void
    {
        $vehicle = $event->vehicle();

        $this->commandBus->dispatch(new FindVehicleDestination($event->aggregateRoot(), [
            'vehicle' => $vehicle,
            'destination' => $vehicle->originName(),
        ]));
    }
}
