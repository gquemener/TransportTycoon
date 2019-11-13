<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\Model;

interface VehicleRepository
{
    public function persist(Vehicle $facility): void;

    public function find(VehicleId $id): ?Vehicle;
}
