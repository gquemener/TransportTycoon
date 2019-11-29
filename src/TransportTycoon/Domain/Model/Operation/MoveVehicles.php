<?php
declare(strict_types=1);

namespace App\TransportTycoon\Domain\Model\Operation;

use App\TransportTycoon\Domain\Model\World;
use App\TransportTycoon\Domain\Event\VehicleHasMoved;
use App\TransportTycoon\Domain\Event\VehicleHasParkedInFacility;
use App\TransportTycoon\Domain\Event\VehicleWasUnloaded;

final class MoveVehicles
{
    public function execute(World $world): \Generator
    {
        $cargos = $world->cargos();
        $vehicles = $world->vehicles();

        foreach ($vehicles as $vehicle) {
            if ($vehicle->isAtUnloadingArea()) {
                $vehicle->processUnloading();

                if (!$vehicle->hasLoad()) {
                    yield new VehicleWasUnloaded($world, [
                        'vehicle' => $vehicle,
                        'cargos' => $vehicle->cargoLoad(),
                    ]);
                }

                continue;
            }

            if ($vehicle->isEnRoute()) {
                $vehicle->moveForward();

                yield new VehicleHasMoved($world, ['vehicle' => $vehicle]);

                if ($vehicle->hasReachedDestination()) {
                    $vehicle->enterDestination();

                    yield new VehicleHasParkedInFacility($world, ['vehicle' => $vehicle]);
                }
            }
        }
    }
}
