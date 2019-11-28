<?php
declare(strict_types=1);

namespace App\TransportTycoon\Domain\Command;

use App\TransportTycoon\Domain\Command\UnloadVehicle;

final class UnloadVehicleHandler
{
    public function handle(UnloadVehicle $command): \Generator
    {
        /** @var \App\TransportTycoon\Domain\Model\World */
        $world = $command->aggregateRoot();

        yield from $world->unloadVehicle(
            $command->vehicle()
        );
    }
}
