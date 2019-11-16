<?php
declare(strict_types=1);

namespace App\Simulation\Domain\Service;

use App\Tracking\Domain\Event\CargoWasRegistered;
use App\Tracking\Domain\Event\CargoWasUnloaded;

interface Simulator
{
    public function run(array $cargoDestinations): void;

    public function onCargoWasRegistered(CargoWasRegistered $event): void;

    public function onCargoWasUnloaded(CargoWasUnloaded $event): void;
}
