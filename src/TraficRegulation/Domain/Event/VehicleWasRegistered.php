<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\Event;

use App\TraficRegulation\Domain\Model\VehicleId;
use App\TraficRegulation\Domain\Model\Facility;

final class VehicleWasRegistered
{
    private $vehicleId;
    private $position;

    public function __construct(
        VehicleId $vehicleId,
        Facility $position
    ) {
        $this->vehicleId = $vehicleId;
        $this->position = $position;
    }

    public function vehicleId(): VehicleId
    {
        return $this->vehicleId;
    }

    public function position(): Facility
    {
        return $this->position;
    }
}
