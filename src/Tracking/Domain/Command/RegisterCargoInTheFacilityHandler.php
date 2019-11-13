<?php
declare(strict_types=1);

namespace App\Tracking\Domain\Command;

use App\Tracking\Domain\Model\CargoRepository;
use App\Tracking\Domain\Model\Cargo;

final class RegisterCargoInTheFacilityHandler
{
    private $repository;

    public function __construct(CargoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(RegisterCargoInTheFacility $command): void
    {
        $cargoId = $command->cargoId();
        $origin = $command->origin();
        $destination = $command->destination();

        $cargo = Cargo::register($cargoId, $origin, $destination);

        $this->repository->persist($cargo);
    }
}
