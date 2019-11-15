<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\Event;

use App\TraficRegulation\Domain\Model\VehicleFleetId;

final class VehicleFleetHasBeenCreated implements \JsonSerializable
{
    private $id;

    public function __construct(VehicleFleetId $id)
    {
        $this->id = $id;
    }

    public function jsonSerialize(): array
    {
        return [
            'vehicleFleetId' => $this->id->toString(),
        ];
    }
}
