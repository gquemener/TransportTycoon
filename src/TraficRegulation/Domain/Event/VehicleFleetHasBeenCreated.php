<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\Event;

use App\TraficRegulation\Domain\Model\VehicleFleetId;

final class VehicleFleetHasBeenCreated implements \JsonSerializable
{
    private $vehicleFleetId;

    public function __construct(VehicleFleetId $vehicleFleetId)
    {
        $this->vehicleFleetId = $vehicleFleetId->toString();
    }

    public function jsonSerialize(): array
    {
        return [
            'vehicleFleetId' => $this->vehicleFleetId,
        ];
    }
}
