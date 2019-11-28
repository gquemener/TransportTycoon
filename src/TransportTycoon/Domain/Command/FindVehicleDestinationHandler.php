<?php
declare(strict_types=1);

namespace App\TransportTycoon\Domain\Command;

use App\TransportTycoon\Domain\Model\Facility;

final class FindVehicleDestinationHandler
{
    public function handle(FindVehicleDestination $command): \Generator
    {
        /** @var \App\TransportTycoon\Domain\Model\World */
        $world = $command->aggregateRoot();

        $vehicle = $command->vehicle();

        $position = $vehicle->position();
        if (!$position instanceof Facility) {
            throw new \LogicException('vehicle is not in facility');
        }
        if (null === $route = $position->findRouteTo($command->destination())) {
            throw new \LogicException('no route found');
        }

        yield from $world->startVehicle($vehicle, $route);
    }
}
