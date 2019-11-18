<?php
declare(strict_types=1);

namespace App\Tracking\Domain\Model;

interface CargoRepository
{
    public function persist(Cargo $cargo): void;

    public function find(CargoId $cargoId): ?Cargo;

    public function hasCargo(Vehicle $vehicle): bool;
}
