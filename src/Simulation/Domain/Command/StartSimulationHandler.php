<?php
declare(strict_types=1);

namespace App\Simulation\Domain\Command;

use App\ServiceBus\EventBus;
use App\Simulation\Domain\Event\SimulationHasStarted;

final class StartSimulationHandler
{
    private $eventBus;

    public function __construct(EventBus $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    public function handle(): void
    {
        $this->eventBus->dispatch(new SimulationHasStarted());
    }
}
