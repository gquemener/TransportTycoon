<?php
declare(strict_types=1);

namespace App\Tracking\Domain\Command;

use App\Tracking\Domain\Model\CargoRepository;
use App\Tracking\Domain\Model\CargoIsAlreadyLoaded;

final class LoadPendingCargoHandler
{
    private $repository;

    public function __construct(CargoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(LoadPendingCargo $command): void
    {
        $vehicle = $command->vehicle();
        if (null !== $cargo = $this->repository->loadedInVehicle($vehicle)) {
            throw new CargoIsAlreadyLoaded($cargo);
        }

        if (null === $cargo = $this->repository->firstPendingInFacility($command->facility())) {
            return;
        }

        $cargo->loadInto($vehicle);

        $this->repository->persist($cargo);
    }
}
