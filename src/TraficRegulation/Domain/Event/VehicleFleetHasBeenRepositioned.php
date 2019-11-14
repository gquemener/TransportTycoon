<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\Event;

use App\TraficRegulation\Domain\Model\VehicleFleetId;

final class VehicleFleetHasBeenRepositioned
{
    private $vehicleFleetId;

    public function __construct(VehicleFleetId $vehicleFleetId)
    {
        $this->vehicleFleetId = $vehicleFleetId;
    }
}
