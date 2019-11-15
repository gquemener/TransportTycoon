<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\Model;

final class Vehicle
{
    private $name;
    private $position;

    public function __construct(
        string $name,
        Position $position
    ) {
        $this->name = $name;
        $this->position = $position;
    }

    public static function register(string $name, Facility $position): self
    {
        return new self($name, $position);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function position(): Position
    {
        return $this->position;
    }

    public function configureRoute(Facility $destination, RouteFinder $finder): self
    {
        $self = clone $this;
        $self->position = $finder->find($this->position, $destination);

        return $self;
    }

    public function move(): self
    {
        if ($this->isInFacility()) {
            return $this;
        }

        $self = clone $this;
        $self->position = $self->position->progress();
        if ($self->position->isOver()) {
            $self->position = $self->position->destination();
        }

        return $self;
    }

    public function isEnRoute(): bool
    {
        return $this->position instanceof Route;
    }

    public function isInFacility(): bool
    {
        return $this->position instanceof Facility;
    }
}
