<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\Command;

use App\TraficRegulation\Domain\Model\VehicleFleetId;
use App\TraficRegulation\Domain\Model\Facility;

final class CreateVehicleFleet
{
    private $vehicleFleetId;
    private $initialPosition;

    public function __construct(VehicleFleetId $vehicleFleetId, Facility $initialPosition)
    {
        $this->vehicleFleetId = $vehicleFleetId;
        $this->initialPosition = $initialPosition;
    }

    public function vehicleFleetId(): VehicleFleetId
    {
        return $this->vehicleFleetId;
    }

    public function initialPosition(): Facility
    {
        return $this->initialPosition;
    }
}
