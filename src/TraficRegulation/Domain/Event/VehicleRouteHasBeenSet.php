<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\Event;

use App\TraficRegulation\Domain\Model\VehicleFleetId;
use App\TraficRegulation\Domain\Model\Vehicle;

final class VehicleRouteHasBeenSet implements \JsonSerializable
{
    private $vehicleFleetId;
    private $vehicle;

    public function __construct(
        VehicleFleetId $vehicleFleetId,
        Vehicle $vehicle
    ) {
        $this->vehicleFleetId = $vehicleFleetId;
        $this->vehicle = $vehicle;
    }

    public function jsonSerialize(): array
    {
        return [
            'vehicleFleetId' => $this->vehicleFleetId->toString(),
            'vehicleName' => $this->vehicle->name(),
            'vehiclePosition' => $this->vehicle->position()->description(),
        ];
    }
}
