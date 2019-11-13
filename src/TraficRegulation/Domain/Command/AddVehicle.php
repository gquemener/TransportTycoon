<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\Command;

use App\TraficRegulation\Domain\Model\VehicleId;
use App\TraficRegulation\Domain\Model\Facility;

final class AddVehicle
{
    private $vehicleId;
    private $facility;

    public function __construct(VehicleId $vehicleId, Facility $facility)
    {
        $this->vehicleId = $vehicleId;
        $this->facility = $facility;
    }

    public function vehicleId(): VehicleId
    {
        return $this->vehicleId;
    }

    public function facility(): Facility
    {
        return $this->facility;
    }
}
