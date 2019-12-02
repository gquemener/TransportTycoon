<?php
declare(strict_types=1);

namespace App\Tracer\Domain\ProcessManager;

use App\ServiceBus\CommandBus;
use App\Tracer\Domain\Command\AppendDepartEntry;
use App\Tracer\Domain\Command\AppendArriveEntry;
use App\TransportTycoon\Domain\Event\VehicleHasStarted;
use App\TransportTycoon\Domain\Model\Cargo;
use App\Tracer\Domain\Model\JournalId;
use App\TransportTycoon\Domain\Model\FacilityName;
use App\TransportTycoon\Domain\Event\VehicleHasParkedInFacility;
use App\TransportTycoon\Domain\Model\World;

final class AppendEntriesToJournal
{
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function onVehicleHasStarted(VehicleHasStarted $event): void
    {
        /** @var \App\TransportTycoon\Domain\Model\World */
        $world = $event->aggregateRoot();
        $journalId = $this->getJournalId($world);
        $vehicle = $event->vehicle();
        list($kind, $transportId) = explode(' ', $vehicle->name());
        /** @var \App\TransportTycoon\Domain\Model\EnRoute */
        $position = $vehicle->position();

        $this->commandBus->dispatch(new AppendDepartEntry($journalId, [
            'time' => $world->age(),
            'transportId' => (int) $transportId,
            'kind' => strtoupper($kind),
            'location' => $position->origin()->toString(),
            'destination' => $position->destination()->toString(),
            'cargos' => $vehicle->cargoLoad(),
        ]));
    }

    public function onVehicleHasParkedInFacility(VehicleHasParkedInFacility $event): void
    {
        /** @var \App\TransportTycoon\Domain\Model\World */
        $world = $event->aggregateRoot();
        $journalId = $this->getJournalId($world);
        $vehicle = $event->vehicle();
        list($kind, $transportId) = explode(' ', $vehicle->name());
        /** @var \App\TransportTycoon\Domain\Model\EnRoute */
        $position = $vehicle->position();

        $this->commandBus->dispatch(new AppendArriveEntry($journalId, [
            'time' => $world->age(),
            'transportId' => (int) $transportId,
            'kind' => strtoupper($kind),
            'location' => $position->toString(),
            'cargos' => $vehicle->cargoLoad(),
        ]));
    }

    private function getJournalId(World $world): JournalId
    {
        return JournalId::fromString(
            array_reduce($world->cargos(), function(string $carry, Cargo $cargo): string {
                switch ($cargo->destination()) {
                    case FacilityName::WAREHOUSE_A():
                        return $carry . 'A';

                    case FacilityName::WAREHOUSE_B():
                        return $carry . 'B';
                }
                return $cargo->destination()->toString();
            }, '')
        );
    }
}
