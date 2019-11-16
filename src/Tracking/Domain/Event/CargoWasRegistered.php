<?php
declare(strict_types=1);

namespace App\Tracking\Domain\Event;

use App\Tracking\Domain\Model\CargoId;
use App\Tracking\Domain\Model\Facility;

final class CargoWasRegistered implements \JsonSerializable
{
    private $id;
    private $position;
    private $destination;

    public function __construct(
        CargoId $id,
        Facility $position,
        Facility $destination
    ) {
        $this->id = $id->toString();
        $this->position = $position->toString();
        $this->destination = $destination->toString();
    }

    public function cargoId(): CargoId
    {
        return CargoId::fromString($this->id);
    }

    public function position(): Facility
    {
        return Facility::named($this->position);
    }

    public function destination(): Facility
    {
        return Facility::named($this->destination);
    }

    public function jsonSerialize(): array
    {
        return [
            'cargoId' => $this->id,
            'position' => $this->position,
            'destination' => $this->destination,
        ];
    }
}
