<?php
declare(strict_types=1);

namespace App\Tracking\Domain\Command;

use App\Tracking\Domain\Model\CargoRepository;

final class LoadCargoHandler
{
    private $repository;

    public function __construct(CargoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(LoadCargo $command): void
    {
        if (null === $cargo = $this->repository->find($command->cargoId())) {
            throw new \InvalidArgumentException(sprintf(
                'Cargo "%s" does not exist',
                $command->cargoId()->toString()
            ));
        }

        $cargo->loadInto($command->vehicle());

        $this->repository->persist($cargo);
    }
}
