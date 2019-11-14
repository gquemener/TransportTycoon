<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\Command;

use App\TraficRegulation\Domain\Model\VehicleFleetRepository;
use App\TraficRegulation\Domain\Model\Facility;
use App\TraficRegulation\Domain\Model\Route;
use App\TraficRegulation\Application\StaticRouteFinder;

final class ComputeVehicleRouteHandler
{
    private $repository;

    public function __construct(VehicleFleetRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(ComputeVehicleRoute $command): void
    {
        if (null === $vehicleFleet = $this->repository->find($command->vehicleFleetId())) {
            throw new \InvalidArgumentException(sprintf(
                'Could not find vehicle fleet "%s"',
                $command->vehicleFleetId()->toString()
            ));
        }

        $destination = $command->destination();

        $vehicleFleet->setVehicleRoute(
            $command->vehicleName(),
            $command->destination(),
            new StaticRouteFinder()
        );

        $this->repository->persist($vehicleFleet);
    }
}
