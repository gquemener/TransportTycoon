<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\Command;

use App\TraficRegulation\Domain\Model\VehicleFleetRepository;
use App\TraficRegulation\Domain\Model\VehicleFleet;

final class CreateVehicleFleetHandler
{
    private $repository;

    public function __construct(VehicleFleetRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(CreateVehicleFleet $command): void
    {
        $vehicleFleetId = $command->vehicleFleetId();
        $initialPosition = $command->initialPosition();

        $vehicleFleet = VehicleFleet::create($vehicleFleetId, $initialPosition);

        $this->repository->persist($vehicleFleet);
    }
}
