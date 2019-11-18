<?php
declare(strict_types=1);

namespace App\Tracking\Domain\Command;

use App\Tracking\Domain\Model\CargoId;
use App\Tracking\Domain\Model\Vehicle;
use App\TraficRegulation\Domain\Model\VehicleFleetId;

final class LoadCargo implements \JsonSerializable
{
    private $cargoId;
    private $vehicleFleetId;
    private $vehicleName;

    public function __construct(CargoId $cargoId, Vehicle $vehicle)
    {
        $this->cargoId = $cargoId->toString();
        $this->vehicleFleetId = $vehicle->vehicleFleetId()->toString();
        $this->vehicleName = $vehicle->name();
    }

    public function cargoId(): CargoId
    {
        return CargoId::fromString($this->cargoId);
    }

    public function vehicle(): Vehicle
    {
        return Vehicle::create(
            VehicleFleetId::fromString($this->vehicleFleetId),
            $this->vehicleName
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'cargoId' => $this->cargoId,
            'vehicleFleetId' => $this->vehicleFleetId,
            'vehicleName' => $this->vehicleName,
        ];
    }
}
