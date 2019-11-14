<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\Model;

use App\TraficRegulation\Domain\Model\VehicleFleet;
use App\TraficRegulation\Domain\Model\VehicleFleetId;

interface VehicleFleetRepository
{
    public function persist(VehicleFleet $vehicleFleet): void;

    public function find(VehicleFleetId $id): ?VehicleFleet;
}
