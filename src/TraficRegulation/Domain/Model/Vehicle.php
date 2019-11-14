<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\Model;

final class Vehicle
{
    private $name;
    private $position;
    private $route;

    public function __construct(
        string $name,
        Facility $position
    ) {
        $this->name = $name;
        $this->position = $position;
    }

    public static function register(string $name, Facility $position): self
    {
        return new self($name, $position);
    }

    public function configureRoute(Facility $destination, RouteFinder $finder): void
    {
        $this->route = $finder->find($this->position, $destination);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function position(): Facility
    {
        return $this->position;
    }
}
