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
        $this->id = $id;
        $this->position = $position;
        $this->destination = $destination;
    }

    public function cargoId(): CargoId
    {
        return $this->id;
    }

    public function destination(): string
    {
        return $this->destination->toString();
    }

    public function jsonSerialize(): array
    {
        return [
            'cargoId' => $this->id->toString(),
            'destination' => $this->destination(),
        ];
    }
}
