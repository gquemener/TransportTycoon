<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\Command;

use App\TraficRegulation\Domain\Model\VehicleId;
use App\TraficRegulation\Domain\Model\Facility;

final class ComputeVehicleRoute
{
    private $vehicleId;
    private $destination;

    public function __construct(
        VehicleId $vehicleId,
        Facility $destination
    ) {
        $this->vehicleId = $vehicleId;
        $this->destination = $destination;
    }

    public function vehicleId(): VehicleId
    {
        return $this->vehicleId;
    }

    public function destination(): Facility
    {
        return $this->destination;
    }
}
