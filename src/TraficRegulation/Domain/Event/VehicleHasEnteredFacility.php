<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\Event;

use App\TraficRegulation\Domain\Model\VehicleFleetId;
use App\TraficRegulation\Domain\Model\Vehicle;
use App\TraficRegulation\Domain\Model\Facility;

final class VehicleHasEnteredFacility implements \JsonSerializable
{
    private $vehicleFleetId;
    private $vehicleName;
    private $vehiclePosition;

    public function __construct(
        VehicleFleetId $vehicleFleetId,
        Vehicle $vehicle
    ) {
        $this->vehicleFleetId = $vehicleFleetId->toString();
        $this->vehicleName = $vehicle->name();

        /** @var Facility */
        $position = $vehicle->position();

        $this->vehiclePosition = $position->toString();
    }

    public function vehicleFleetId(): VehicleFleetId
    {
        return VehicleFleetId::fromString($this->vehicleFleetId);
    }

    public function vehicle(): Vehicle
    {
        return Vehicle::register(
            $this->vehicleName,
            Facility::named($this->vehiclePosition)
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'vehicleFleetId' => $this->vehicleFleetId,
            'vehicleName' => $this->vehicleName,
            'vehiclePosition' => $this->vehiclePosition,
        ];
    }
}
