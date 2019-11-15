<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\Command;

use App\TraficRegulation\Domain\Model\VehicleFleetId;
use App\TraficRegulation\Domain\Model\Facility;

final class AddVehicle
{
    private $vehicleFleetId;
    private $name;
    private $initialPosition;

    public function __construct(
        VehicleFleetId $vehicleFleetId,
        string $name,
        Facility $initialPosition
    ) {
        $this->vehicleFleetId = $vehicleFleetId;
        $this->name = $name;
        $this->initialPosition = $initialPosition;
    }

    public function vehicleFleetId(): VehicleFleetId
    {
        return $this->vehicleFleetId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function initialPosition(): Facility
    {
        return $this->initialPosition;
    }
}
