<?php
declare(strict_types=1);

namespace App\TransportTycoon\Domain\Model;

use App\TransportTycoon\Domain\Event\VehicleWasLoaded;
use App\TransportTycoon\Domain\Event\OneHourHasPassed;
use App\TransportTycoon\Domain\Event\VehicleHasStarted;
use App\TransportTycoon\Domain\Event\VehicleHasMoved;
use App\TransportTycoon\Domain\Event\VehicleHasParkedInFacility;
use App\TransportTycoon\Domain\Event\VehicleWasUnloaded;

final class Game
{
    private $vehicleFleet;
    private $pendingCargos;
    private $elapsedHours;

    private function __construct(
        VehicleFleet $vehicleFleet,
        PendingCargos $pendingCargos
    ) {
        $this->vehicleFleet = $vehicleFleet;
        $this->pendingCargos = $pendingCargos;
        $this->elapsedHours = 0;
    }

    public static function start(
        VehicleFleet $vehicleFleet,
        PendingCargos $pendingCargos
    ): self {
        return new self($vehicleFleet, $pendingCargos);
    }

    public function loadCargos(): \Generator
    {
        foreach ($this->vehicleFleet->readyToLoadVehicles() as $vehicle) {
            $cargos = array_slice(
                $this->pendingCargos->inFacility($vehicle->position()),
                0,
                $vehicle->maxLoad()
            );

            if (0 === count($cargos)) {
                continue;
            }

            if (0 !== $vehicle->handlingHours()) {
                throw new \Exception('Not implemented yet');
            }

            $this->vehicleFleet = $this->vehicleFleet->loadVehicle($vehicle, $cargos);
            $this->pendingCargos = $this->pendingCargos->remove($cargos);

            yield new VehicleWasLoaded($this, [
                'vehicle' => $vehicle,
                'cargos' => $cargos
            ]);
        }
    }

    public function startVehicle(Vehicle $vehicle, Route $route): \Generator
    {
        $this->vehicleFleet = $this->vehicleFleet->startVehicleRoute($vehicle, $route);

        yield new VehicleHasStarted($this, [
            'vehicle' => $vehicle,
            'route' => $route,
        ]);
    }

    public function moveVehicles(): \Generator
    {
        foreach ($this->vehicleFleet->enRouteVehicles() as $vehicle) {
            $this->vehicleFleet = $this->vehicleFleet->progressVehicleRoute($vehicle);

            yield new VehicleHasMoved($this, [
                'vehicle' => $vehicle,
                'route' => $this->vehicleFleet->route($vehicle),
            ]);
        }
    }

    public function increaseElapsedHours(): \Generator
    {
        ++$this->elapsedHours;

        yield new OneHourHasPassed();
    }

    public function elapsedHours(): int
    {
        return $this->elapsedHours;
    }

    public function hasVehicleReachedDestination(Vehicle $vehicle): bool
    {
        $route = $this->vehicleFleet->route($vehicle);

        return $route->isOver();
    }

    public function parkVehicle(Vehicle $vehicle, Facility $facility): \Generator
    {
        $this->vehicleFleet = $this->vehicleFleet->parkVehicle($vehicle, $facility);

        yield new VehicleHasParkedInFacility($this, [
            'vehicle' => $vehicle,
            'facility' => $facility,
        ]);
    }

    public function unloadVehicle(Vehicle $vehicle, Facility $facility): \Generator
    {
        $this->vehicleFleet = $this->vehicleFleet->unloadVehicle($vehicle);

        $load = $vehicle->load();
        foreach ($load->cargos() as $cargo) {
            $this->pendingCargos = $this->pendingCargos->add(
                $cargo->moveTo($facility)
            );
        }

        yield new VehicleWasUnloaded($this, [
            'vehicle' => $vehicle,
            'facility' => $facility,
        ]);
    }
}
