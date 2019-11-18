<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\Event;

use App\TraficRegulation\Domain\Model\VehicleFleetId;
use App\TraficRegulation\Domain\Model\Vehicle;
use App\TraficRegulation\Domain\Model\Facility;
use App\TraficRegulation\Domain\Model\Route;

final class VehicleRouteHasBeenSet implements \JsonSerializable
{
    private $vehicleFleetId;
    private $vehicleName;
    private $vehicleDestination;
    private $vehicleEta;

    public function __construct(
        VehicleFleetId $vehicleFleetId,
        Vehicle $vehicle
    ) {
        $this->vehicleFleetId = $vehicleFleetId->toString();
        $this->vehicleName = $vehicle->name();

        /** @var Route */
        $position = $vehicle->position();

        $this->vehicleDestination = $position->destination()->toString();
        $this->vehicleEta = $position->eta();
    }

    public function vehicleFleetId(): VehicleFleetId
    {
        return VehicleFleetId::fromString($this->vehicleFleetId);
    }

    public function vehicle(): Vehicle
    {
        return Vehicle::create(
            $this->vehicleName,
            Route::to(
                Facility::named($this->vehicleDestination),
                $this->vehicleEta
            )
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'vehicleFleetId' => $this->vehicleFleetId,
            'vehicleName' => $this->vehicleName,
            'vehicleDestination' => $this->vehicleDestination,
            'vehicleEta' => $this->vehicleEta,
        ];
    }
}
