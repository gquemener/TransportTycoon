<?php
declare(strict_types=1);

namespace App\TransportTycoon\Domain\Model;

final class Vehicle
{
    private static $truckCount = 0;
    private static $shipCount = 0;

    private $origin;
    private $position;
    private $name;
    private $maxLoad;
    private $handlingHours;
    private $cargos;

    private function __construct(
        Facility $origin,
        string $name,
        int $maxLoad,
        int $handlingHours
    ) {
        $this->origin = $this->position = $origin;
        $this->name = $name;
        $this->maxLoad = $maxLoad;
        $this->handlingHours = $handlingHours;
        $this->cargos = [];
    }

    public static function truck(Facility $origin): self
    {
        return new self($origin, sprintf('Truck %d', ++self::$truckCount), 1, 0);
    }

    public static function ship(Facility $origin): self
    {
        return new self($origin, sprintf('Ship %d', ++self::$shipCount), 4, 1);
    }

    public function moveTo(Position $position): void
    {
        $this->position = $position;
        foreach ($this->cargos as $cargo) {
            $cargo->moveTo($position);
        }
    }

    public function load(array $cargos): void
    {
        $this->cargos = $cargos;
    }

    public function unload(): void
    {
        $this->cargos = [];
    }

    public function hasLoad(): bool
    {
        return !empty($this->cargos);
    }

    public function moveForward(): void
    {
        if (!$this->isEnRoute()) {
            throw new \LogicException('Vehicle is parked');
        }

        $this->position->moveForward();
    }

    public function hasReachedDestination(): bool
    {
        if (!$this->isEnRoute()) {
            throw new \LogicException('Vehicle is parked');
        }

        return $this->position->hasReachedDestination();
    }

    public function enterDestination(): void
    {
        if (!$this->isEnRoute()) {
            throw new \LogicException('Vehicle is parked');
        }

        $this->moveTo(
            $this->position->destination()
        );
    }

    public function isEnRoute(): bool
    {
        return $this->position instanceof EnRoute;
    }

    public function isInOriginalPosition(): bool
    {
        return $this->position->equals($this->origin);
    }

    public function originName(): FacilityName
    {
        return $this->origin->name();
    }

    public function position(): Position
    {
        return $this->position;
    }

    public function maxLoad(): int
    {
        return $this->maxLoad;
    }

    public function toString(): string
    {
        return sprintf('%s (Position: %s)', $this->name, $this->position->toString());
    }
}
