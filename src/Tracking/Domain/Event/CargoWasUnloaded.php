<?php
declare(strict_types=1);

namespace App\Tracking\Domain\Event;

use App\Tracking\Domain\Model\CargoId;
use App\Tracking\Domain\Model\Facility;
use App\TraficRegulation\Domain\Model\VehicleFleetId;

final class CargoWasUnloaded implements \JsonSerializable
{
    private $cargoId;
    private $position;

    public function __construct(
        CargoId $cargoId,
        Facility $position
    ) {
        $this->cargoId = $cargoId->toString();
        $this->position = $position->toString();
    }

    public function cargoId(): CargoId
    {
        return CargoId::fromString($this->cargoId);
    }

    public function position(): Facility
    {
        return Facility::named($this->position);
    }

    public function jsonSerialize(): array
    {
        return [
            'cargoId' => $this->cargoId,
            'position' => $this->position,
        ];
    }
}
