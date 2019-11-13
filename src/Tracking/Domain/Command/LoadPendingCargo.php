<?php
declare(strict_types=1);

namespace App\Tracking\Domain\Command;

use App\TraficRegulation\Domain\Model\VehicleId;
use App\Tracking\Domain\Model\Facility;

final class LoadPendingCargo
{
    private $vehicleId;
    private $facility;

    public function __construct(
        VehicleId $vehicleId,
        Facility $facility
    ) {
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
