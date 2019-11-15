<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\Command;

use App\TraficRegulation\Domain\Model\VehicleFleetRepository;

final class RepositionVehicleFleetHandler
{
    private $repository;

    public function __construct(VehicleFleetRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(RepositionVehicleFleet $command): void
    {
        $vehicleFleetId = $command->vehicleFleetId();

        if (null === $vehicleFleet = $this->repository->find($vehicleFleetId)) {
            throw new \RuntimeException(sprintf(
                'Could not find vehicle fleet "%s"',
                $vehicleFleetId->toString()
            ));
        }

        $vehicleFleet->repositionVehicles();

        $this->repository->persist($vehicleFleet);
    }
}
