<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\Command;

use App\TraficRegulation\Domain\Model\Facility;
use App\TraficRegulation\Domain\Model\VehicleFleetId;
use App\TraficRegulation\Domain\Model\Vehicle;

final class ComputeVehicleRoute implements \JsonSerializable
{
    private $vehicleFleetId;
    private $vehicleName;
    private $destination;

    public function __construct(
        VehicleFleetId $vehicleFleetId,
        string $vehicleName,
        Facility $destination
    ) {
        $this->vehicleFleetId = $vehicleFleetId->toString();
        $this->vehicleName = $vehicleName;
        $this->destination = $destination->toString();
    }

    public function vehicleFleetId(): VehicleFleetId
    {
        return VehicleFleetId::fromString($this->vehicleFleetId);
    }

    public function vehicleName(): string
    {
        return $this->vehicleName;
    }

    public function destination(): Facility
    {
        return Facility::named($this->destination);
    }

    public function jsonSerialize(): array
    {
        return [
            'vehicleFleetId' => $this->vehicleFleetId,
            'vehicleName' => $this->vehicleName,
            'destination' => $this->destination,
        ];
    }
}
