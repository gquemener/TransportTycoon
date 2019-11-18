<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\Command;

use App\TraficRegulation\Domain\Model\VehicleFleetId;
use App\TraficRegulation\Domain\Model\Facility;

final class CreateVehicleFleet implements \JsonSerializable
{
    private $vehicleFleetId;

    public function __construct(VehicleFleetId $vehicleFleetId)
    {
        $this->vehicleFleetId = $vehicleFleetId->toString();
    }

    public function vehicleFleetId(): VehicleFleetId
    {
        return VehicleFleetId::fromString($this->vehicleFleetId);
    }

    public function jsonSerialize(): array
    {
        return [
            'vehicleFleetId' => $this->vehicleFleetId,
        ];
    }
}
