<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\Command;

use App\TraficRegulation\Domain\Model\Facility;
use App\TraficRegulation\Domain\Model\VehicleFleetId;
use App\TraficRegulation\Domain\Model\Vehicle;

final class ComputeVehicleRoute
{
    private $vehicleFleetId;
    private $vehicleName;
    private $destination;

    public function __construct(
        VehicleFleetId $vehicleFleetId,
        string $vehicleName,
        Facility $destination
    ) {
        $this->vehicleFleetId = $vehicleFleetId->toString();
        $this->vehicleName = $vehicleName;
        $this->destination = $destination;
    }

    public function vehicleFleetId(): VehicleFleetId
    {
        return VehicleFleetId::fromString($this->vehicleFleetId);
    }

    public function vehicleName(): string
    {
        return $this->vehicleName;
    }

    public function destination(): Facility
    {
        return $this->destination;
    }
}
