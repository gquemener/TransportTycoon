<?php
declare(strict_types=1);

namespace App\Tracking\Domain\Event;

use App\Tracking\Domain\Model\CargoId;
use App\Tracking\Domain\Model\Vehicle;
use App\TraficRegulation\Domain\Model\VehicleFleetId;

final class CargoWasLoaded
{
    private $cargoId;
    private $vehicle;

    public function __construct(
        CargoId $cargoId,
        Vehicle $vehicle
    ) {
        $this->cargoId = $cargoId;
        $this->vehicle = $vehicle;
    }

    public function cargoId(): CargoId
    {
        return $this->cargoId;
    }

    public function vehicleFleetId(): VehicleFleetId
    {
        return $this->vehicle->vehicleFleetId();
    }

    public function vehicleName(): string
    {
        return $this->vehicle->name();
    }
}
