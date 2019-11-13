<?php
declare(strict_types=1);

namespace App\TraficRegulation\Infrastructure;

use App\TraficRegulation\Domain\Model\VehicleRepository;
use App\TraficRegulation\Domain\Model\Vehicle;
use App\TraficRegulation\Domain\Model\VehicleId;
use App\ServiceBus\EventBus;

final class InMemoryVehicleRepository implements VehicleRepository
{
    private $vehicles = [];
    private $eventBus;

    public function __construct(EventBus $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    public function persist(Vehicle $vehicle): void
    {
        $this->vehicles[$vehicle->id()->toString()] = $vehicle;

        foreach ($vehicle->popEvents() as $event) {
            $this->eventBus->dispatch($event);
        }
    }

    public function find(VehicleId $id): ?Vehicle
    {
        if (!isset($this->vehicles[$id->toString()])) {
            return null;
        }

        $vehicle = clone $this->vehicles[$id->toString()];

        return $vehicle;
    }
}
