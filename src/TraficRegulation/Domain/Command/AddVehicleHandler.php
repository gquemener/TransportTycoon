<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\Command;

use App\TraficRegulation\Domain\Model\VehicleRepository;
use App\TraficRegulation\Domain\Model\Vehicle;

final class AddVehicleHandler
{
    private $repository;

    public function __construct(VehicleRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(AddVehicle $command): void
    {
        $vehicleId = $command->vehicleId();
        $facility = $command->facility();

        $vehicle = Vehicle::register($vehicleId, $facility);

        $this->repository->persist($vehicle);
    }
}
