<?php
declare(strict_types=1);

namespace App\TraficRegulation\Infrastructure;

use App\TraficRegulation\Domain\Model\VehicleFleetRepository;
use App\TraficRegulation\Domain\Model\VehicleFleet;
use App\TraficRegulation\Domain\Model\VehicleFleetId;
use App\ServiceBus\EventBus;

final class InMemoryVehicleFleetRepository implements VehicleFleetRepository
{
    private $vehicleFleets = [];
    private $eventBus;

    public function __construct(EventBus $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    public function persist(VehicleFleet $vehicleFleet): void
    {
        $this->vehicleFleets[$vehicleFleet->id()->toString()] = $vehicleFleet;

        foreach ($vehicleFleet->popEvents() as $event) {
            $this->eventBus->dispatch($event);
        }
    }

    public function find(VehicleFleetId $id): ?VehicleFleet
    {
        if (!isset($this->vehicleFleets[$id->toString()])) {
            return null;
        }

        return $this->vehicleFleets[$id->toString()];
    }
}
