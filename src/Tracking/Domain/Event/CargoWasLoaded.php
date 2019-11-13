<?php
declare(strict_types=1);

namespace App\Tracking\Domain\Event;

use App\TraficRegulation\Domain\Model\VehicleId;
use App\Tracking\Domain\Model\Facility;

final class CargoWasLoaded
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
