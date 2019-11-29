<?php
declare(strict_types=1);

namespace App\TransportTycoon\Domain\Model\Operation;

use App\TransportTycoon\Domain\Model\World;
use App\TransportTycoon\Domain\Event\VehicleWasLoaded;
use App\TransportTycoon\Domain\Event\VehicleLoadingHasStarted;
use App\TransportTycoon\Domain\Event\VehicleWasUnloaded;

final class LoadCargoInAvailableVehicle
{
    public function execute(World $world): \Generator
    {
        $cargos = $world->cargos();
        $vehicles = $world->vehicles();

        foreach ($vehicles as $vehicle) {
            if ($vehicle->isAtLoadingArea()) {
                $vehicle->processLoading();

                if ($vehicle->hasLoad()) {
                    yield new VehicleWasLoaded($world, [
                        'vehicle' => $vehicle,
                        'cargos' => $vehicle->cargoLoad(),
                    ]);
                }

                continue;
            }

            if (!$vehicle->isInOriginalPosition()) {
                continue;
            }
            $load = [];
            foreach ($cargos as $cargo) {
                if ($cargo->isStoredInFacility($vehicle->position())) {
                    $load[] = $cargo;
                }
                if (count($load) === $vehicle->maxLoad()) {
                    break;
                }
            }
            if (0 !== count($load)) {
                if ($vehicle->hasImmediateHandlingCapability()) {
                    $vehicle->load($load);

                    yield new VehicleWasLoaded($world, [
                        'vehicle' => $vehicle,
                        'cargos' => $load,
                    ]);

                    continue;
                }

                $vehicle->startLoading($load);

                yield new VehicleLoadingHasStarted($world, [
                    'vehicle' => $vehicle,
                    'cargos' => $load,
                ]);
            }
        }
    }
}
