<?php
declare(strict_types=1);

namespace App\TransportTycoon\Domain\Command;

final class AddOneHourHandler
{
    public function handle(AddOneHour $command): \Generator
    {
        /** @var \App\TransportTycoon\Domain\Model\World */
        $world = $command->aggregateRoot();

        yield from $world->addOneHour();
    }
}
