<?php
declare(strict_types=1);

namespace App\Tracking\Domain\Model;

interface CargoRepository
{
    public function persist(Cargo $cargo): void;

    public function find(CargoId $cargoId): ?Cargo;

    public function firstPendingInFacility(Facility $facility): ?Cargo;

    public function loadedInVehicle(Vehicle $vehicle): ?Cargo;
}
