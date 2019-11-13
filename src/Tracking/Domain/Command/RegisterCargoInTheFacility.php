<?php
declare(strict_types=1);

namespace App\Tracking\Domain\Command;

use App\Tracking\Domain\Model\CargoId;
use App\Tracking\Domain\Model\Facility;

final class RegisterCargoInTheFacility
{
    private $cargoId;
    private $origin;
    private $destination;

    public function __construct(
        CargoId $cargoId,
        Facility $origin,
        Facility $destination
    ) {
        $this->cargoId = $cargoId;
        $this->origin = $origin;
        $this->destination = $destination;
    }

    public function cargoId(): CargoId
    {
        return $this->cargoId;
    }

    public function origin(): Facility
    {
        return $this->origin;
    }

    public function destination(): Facility
    {
        return $this->destination;
    }
}
