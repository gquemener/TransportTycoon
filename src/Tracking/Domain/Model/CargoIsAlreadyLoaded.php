<?php
declare(strict_types=1);

namespace App\Tracking\Domain\Model;

final class CargoIsAlreadyLoaded extends \RuntimeException
{
    private $cargoId;

    public function __construct(Cargo $cargo)
    {
        parent::__construct(sprintf(
            'Cargo "%s" is already loaded in vehicle "%s"',
            $cargo->id()->toString(),
            $cargo->vehicle()->toString()
        ));

        $this->cargoId = $cargo->id();
    }

    public function cargoId(): CargoId
    {
        return $this->cargoId;
    }
}
