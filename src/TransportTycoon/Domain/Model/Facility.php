<?php
declare(strict_types=1);

namespace App\TransportTycoon\Domain\Model;

final class Facility implements Position
{
    private $name;
    private $routes;

    private function __construct(FacilityName $name)
    {
        $this->name = $name;
        $this->routes = [];
    }

    public static function build(FacilityName $name): self
    {
        return new self($name);
    }

    public function connect(Facility $facility, int $eta): void
    {
        $route = Route::between($this, $facility, $eta);
        if (false === array_search($route, $this->routes)) {
            $this->routes[] = $route;

            $facility->connect($this, $eta);
        }
    }

    public function storeCargo(Cargo $cargo): void
    {
        $cargo->moveTo($this);
    }

    public function parkVehicle(Vehicle $vehicle): void
    {
        $vehicle->moveTo($this);
    }

    public function name(): FacilityName
    {
        return $this->name;
    }

    public function routes(): array
    {
        return $this->routes;
    }

    public function findRouteTo(FacilityName $destination, array $visited = []): ?Route
    {
        if (in_array($this, $visited)) {
            return null;
        }
        $visited[] = $this;
        foreach ($this->routes as $route) {
            if (
                $route->destination()->name()->equals($destination)
                || null !== $route->destination()->findRouteTo($destination, $visited)
            ) {
                return $route;
            }
        }

        return null;
    }

    public function toString(): string
    {
        return $this->name->toString();
    }

    public function equals(Position $position): bool
    {
        return $position instanceof self
            && $position->name->equals($this->name);
    }
}
