<?php
declare(strict_types=1);

namespace App\Simulation\Domain\Command;

use App\ServiceBus\EventBus;
use App\Simulation\Domain\Event\OneHourHasPassed;

final class WaitOneHourHandler
{
    private $eventBus;

    public function __construct(EventBus $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    public function handle(): void
    {
        $this->eventBus->dispatch(new OneHourHasPassed());
    }
}
