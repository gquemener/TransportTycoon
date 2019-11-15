<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\Command;

use App\TraficRegulation\Domain\Model\VehicleFleetRepository;
use App\TraficRegulation\Domain\Model\VehicleFleet;

final class AddVehicleHandler
{
    private $repository;

    public function __construct(VehicleFleetRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(AddVehicle $command): void
    {
        $vehicleFleetId = $command->vehicleFleetId();

        if (null === $vehicleFleet = $this->repository->find($vehicleFleetId)) {
            throw new \RuntimeException(sprintf(
                'Could not find vehicle fleet "%s"',
                $vehicleFleetId->toString()
            ));
        }
        $vehicleFleet->addVehicle($command->name(), $command->initialPosition());

        $this->repository->persist($vehicleFleet);
    }
}
