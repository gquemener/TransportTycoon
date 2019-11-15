<?php
declare(strict_types=1);

namespace App\Tracking\Domain\Model;

use App\TraficRegulation\Domain\Model\VehicleFleetId;

final class Vehicle
{
    private $vehicleFleetId;
    private $name;

    private function __construct(
        VehicleFleetId $vehicleFleetId,
        string $name
    ) {
        $this->vehicleFleetId = $vehicleFleetId;
        $this->name = $name;
    }

    public static function create(
        VehicleFleetId $vehicleFleetId,
        string $name
    ): self{
        return new self($vehicleFleetId, $name);
    }

    public function vehicleFleetId(): VehicleFleetId
    {
        return $this->vehicleFleetId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function toString(): string
    {
        return $this->name();
    }

    public function equals(Vehicle $vehicle): bool
    {
        return get_class($vehicle) === get_class($this)
            && $vehicle->name === $this->name;
    }
}
