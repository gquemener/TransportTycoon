<?php
declare(strict_types=1);

namespace App\TransportTycoon\Domain\Model;

use App\TransportTycoon\Domain\Model\Operation\LoadCargoInAvailableVehicle;
use App\TransportTycoon\Domain\Event\VehicleHasStarted;
use App\TransportTycoon\Domain\Event\OneHourHasPassed;
use App\TransportTycoon\Domain\Model\Operation\MoveVehicles;
use App\TransportTycoon\Domain\Event\VehicleWasUnloaded;
use App\TransportTycoon\Domain\Model\Operation\UnloadCargos;
use App\TransportTycoon\Domain\Event\VehicleUnloadingHasStarted;

final class World
{
    private $root;

    private $cargos;

    private $vehicles;

    private $age;

    private function __construct(Facility $root, array $vehicles, array $cargos)
    {
        $this->root = $root;
        $this->vehicles = $vehicles;
        $this->cargos = $cargos;
        $this->age = 0;
    }

    public static function create(
        Facility $root,
        array $vehicles,
        array $cargos
    ): self {
        return new self($root, $vehicles, $cargos);
    }

    public function hasNonDeliveredCargos(?Facility $facility = null, array $visited = []): bool
    {
        foreach ($this->cargos as $cargo) {
            if (!$cargo->isDelivered()) {
                return true;
            }
        }

        return false;
    }

    public function addOneHour(): \Generator
    {
        //foreach ((new UnloadCargoInAvailableVehicle())->execute($this->root) as $event) {
        //    yield new $event[0]($this, $event[1]);
        //}
        yield from (new MoveVehicles())->execute($this);

        yield from (new LoadCargoInAvailableVehicle())->execute($this);

        ++$this->age;

        yield new OneHourHasPassed();

    }

    public function startVehicle(Vehicle $vehicle, Route $route): \Generator
    {
        $vehicle->moveTo(EnRoute::fromRoute($route));

        yield new VehicleHasStarted($this, [
            'vehicle' => $vehicle,
        ]);
    }

    public function unloadVehicle(Vehicle $vehicle): \Generator
    {
        if ($vehicle->hasImmediateHandlingCapability()) {
            $vehicle->unload();

            yield new VehicleWasUnloaded($this, [
                'vehicle' => $vehicle,
            ]);

            return;
        }

        $vehicle->startUnloading();

        yield new VehicleUnloadingHasStarted($this, [
            'vehicle' => $vehicle,
        ]);
    }

    public function age(): int
    {
        return $this->age; // in hours
    }

    public function vehicles(): array
    {
        return $this->vehicles;
    }

    public function cargos(): array
    {
        return $this->cargos;
    }
}
