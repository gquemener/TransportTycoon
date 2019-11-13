<?php
declare(strict_types=1);

namespace App\Tracking\Domain\Command;

use App\Tracking\Domain\Model\CargoRepository;

final class LoadPendingCargoHandler
{
    private $repository;

    public function __construct(CargoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(LoadPendingCargo $command): void
    {
        if (null === $cargo = $this->repository->firstPendingInFacility($command->facility())) {
            return;
        }

        $cargo->loadInto($command->vehicleId());

        $this->repository->persist($cargo);
    }
}
