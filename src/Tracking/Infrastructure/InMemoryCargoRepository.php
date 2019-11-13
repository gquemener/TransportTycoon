<?php
declare(strict_types=1);

namespace App\Tracking\Infrastructure;

use App\Tracking\Domain\Model\CargoRepository;
use App\Tracking\Domain\Model\Cargo;
use App\Tracking\Domain\Model\Facility;
use App\ServiceBus\EventBus;

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

    public function firstPendingInFacility(Facility $facility): ?Cargo
    {
        xdebug_break();
        foreach ($this->cargos as $cargo) {
            if ($cargo->isPending() && $cargo->position()->equals($facility)) {
                return $cargo;
            }
        }

        return null;
    }
}
