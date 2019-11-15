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
        $this->cargoId = $cargoId;
        $this->position = $position;
    }

    public function cargoId(): CargoId
    {
        return $this->cargoId;
    }

    public function position(): Facility
    {
        return $this->position;
    }

    public function jsonSerialize(): array
    {
        return [
            'cargoId' => $this->cargoId->toString(),
            'position' => $this->position->toString(),
        ];
    }
}
