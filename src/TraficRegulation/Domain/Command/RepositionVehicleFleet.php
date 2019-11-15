<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\Command;

use App\TraficRegulation\Domain\Model\VehicleFleetId;

final class RepositionVehicleFleet
{
    private $vehicleFleetId;

    public function __construct(VehicleFleetId $vehicleFleetId)
    {
        $this->vehicleFleetId = $vehicleFleetId;
    }

    public function vehicleFleetId(): VehicleFleetId
    {
        return $this->vehicleFleetId;
    }
}
