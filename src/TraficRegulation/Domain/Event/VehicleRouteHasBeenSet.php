<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\Event;

use App\TraficRegulation\Domain\Model\VehicleFleetId;
use App\TraficRegulation\Domain\Model\Vehicle;
use App\TraficRegulation\Domain\Model\Facility;

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

    public function vehicleFleetId(): VehicleFleetId
    {
        return $this->vehicleFleetId;
    }

    public function vehicleName(): string
    {
        return $this->vehicle->name();
    }

    public function vehiclePosition(): Facility
    {
        return $this->vehicle->position();
    }

    public function jsonSerialize(): array
    {
        return [
            'vehicleFleetId' => $this->vehicleFleetId,
            'vehicleName' => $this->vehicle->name(),
            'vehiclePosition' => $this->vehicle->position()->description(),
        ];
    }
}
