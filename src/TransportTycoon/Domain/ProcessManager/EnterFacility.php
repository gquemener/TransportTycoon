<?php
declare(strict_types=1);

namespace App\TransportTycoon\Domain\ProcessManager;

use App\ServiceBus\CommandBus;
use App\TransportTycoon\Domain\Event\VehicleHasMoved;
use App\TransportTycoon\Domain\Command\ParkVehicleInFacility;

final class EnterFacility
{
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function onVehicleHasMoved(VehicleHasMoved $event): void
    {
        $game = $event->aggregateRoot();
        $vehicle = $event->vehicle();

        if ($game->hasVehicleReachedDestination($vehicle)) {
            $route = $event->route();
            $this->commandBus->dispatch(
                new ParkVehicleInFacility(
                    $game,
                    [
                        'vehicle' => $vehicle,
                        'facility' => $route->destination(),
                    ]
                )
            );
        }
    }
}
