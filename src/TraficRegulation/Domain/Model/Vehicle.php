<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\Model;

use App\AggregateRoot;
use App\TraficRegulation\Domain\Event\VehicleWasRegistered;

final class Vehicle
{
    use AggregateRoot;

    private $id;
    private $position;

    public function __construct(
        VehicleId $id,
        Facility $position
    ) {
        $this->id = $id;
        $this->position = $position;
    }

    public static function register(VehicleId $id, Facility $position): self
    {
        $self = new self($id, $position);
        $self->record(new VehicleWasRegistered($id, $position));

        return $self;
    }

    public function followRoute(Route $route): void
    {
        $this->route = $route;
    }

    public function id(): VehicleId
    {
        return $this->id;
    }

    public function position(): Facility
    {
        return $this->position;
    }
}
