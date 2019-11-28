<?php
declare(strict_types=1);

namespace App\TransportTycoon\Domain\Model;

final class VehicleFleet
{
    private $vehicles;

    private function __construct(array $vehicles)
    {
        $this->vehicles = $vehicles;
    }

    public static function withVehicles(Vehicle ...$vehicles): self
    {
        return new self($vehicles);
    }

    public function readyToLoadVehicles(): array
    {
        return array_filter($this->vehicles, function(Vehicle $vehicle) {
            return $vehicle->isInFacility();
        });
    }

    public function enRouteVehicles(): array
    {
        return array_filter($this->vehicles, function(Vehicle $vehicle) {
            return $vehicle->isEnRoute();
        });
    }

    public function loadVehicle(Vehicle $vehicle, array $cargos): self
    {
        $self = clone $this;
        foreach ($self->vehicles as $k => $v) {
            if ($v->equals($vehicle)) {
                $self->vehicles[$k] = $self->vehicles[$k]->loadCargos($cargos);
                break;
            }
        }

        return $self;
    }

    public function startVehicleRoute(Vehicle $vehicle, Position $route): self
    {
        $self = clone $this;
        foreach ($self->vehicles as $k => $v) {
            if ($v->equals($vehicle)) {
                $self->vehicles[$k] = $self->vehicles[$k]->startRoute($route);
                break;
            }
        }

        return $self;
    }

    public function progressVehicleRoute(Vehicle $vehicle): self
    {
        $self = clone $this;
        $index = $this->vehicleIndex($vehicle);
        $self->vehicles[$index] = $self->vehicles[$index]->progressRoute();

        return $self;
    }

    public function parkVehicle(Vehicle $vehicle, Facility $facility): self
    {
        $self = clone $this;
        $index = $this->vehicleIndex($vehicle);
        $self->vehicles[$index] = $self->vehicles[$index]->park($facility);

        return $self;
    }

    public function route(Vehicle $vehicle): Route
    {
        return $this->vehicles[$this->vehicleIndex($vehicle)]->position();
    }

    public function unloadVehicle(Vehicle $vehicle): self
    {
        $self = clone $this;
        $index = $this->vehicleIndex($vehicle);
        $self->vehicles[$index] = $self->vehicles[$index]->unload();

        return $self;
    }

    private function vehicleIndex(Vehicle $vehicle): ?int
    {
        foreach ($this->vehicles as $k => $v) {
            if ($v->equals($vehicle)) {
                return $k;
            }
        }

        return null;
    }
}
