<?php
declare(strict_types=1);

namespace App\Tracking\Infrastructure;

use App\ServiceBus\EventBus;
use App\Tracking\Domain\Model\Cargo;
use App\Tracking\Domain\Model\CargoId;
use App\Tracking\Domain\Model\CargoRepository;
use App\Tracking\Domain\Model\Facility;
use App\Tracking\Domain\Model\Vehicle;

final class InMemoryCargoRepository implements CargoRepository
{
    private $cargos = [];
    private $eventBus;

    public function __construct(EventBus $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    public function persist(Cargo $cargo): void
    {
        $this->cargos[$cargo->id()->toString()] = $cargo;

        foreach ($cargo->popEvents() as $event) {
            $this->eventBus->dispatch($event);
        }
    }

    public function find(CargoId $cargoId): ?Cargo
    {
        if (!isset($this->cargos[$cargoId->toString()])) {
            return null;
        }

        return $this->cargos[$cargoId->toString()];
    }

    public function firstPendingInFacility(Facility $facility): ?Cargo
    {
        foreach ($this->cargos as $cargo) {
            if ($cargo->isPending() && $cargo->position()->equals($facility)) {
                return $cargo;
            }
        }

        return null;
    }

    public function loadedInVehicle(Vehicle $vehicle): ?Cargo
    {
        foreach ($this->cargos as $cargo) {
            if ($cargo->isLoaded() && $cargo->vehicle()->equals($vehicle)) {
                return $cargo;
            }
        }

        return null;
    }
}
