<?php
declare(strict_types=1);

namespace App\TransportTycoon\Domain\Model\Operation;

use App\TransportTycoon\Domain\Model\World;
use App\TransportTycoon\Domain\Event\VehicleWasLoaded;

final class LoadCargoInAvailableVehicle
{
    public function execute(World $world): \Generator
    {
        $cargos = $world->cargos();
        $vehicles = $world->vehicles();

        xdebug_break();
        foreach ($vehicles as $vehicle) {
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
                $vehicle->load($load);

                yield new VehicleWasLoaded($world, [
                    'vehicle' => $vehicle,
                    'cargos' => $load,
                ]);
            }
        }
    }
}
