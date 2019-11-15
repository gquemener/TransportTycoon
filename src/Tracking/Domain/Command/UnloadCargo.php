<?php
declare(strict_types=1);

namespace App\Tracking\Domain\Command;

use App\Tracking\Domain\Model\CargoId;
use App\Tracking\Domain\Model\Facility;

final class UnloadCargo
{
    private $cargoId;
    private $facility;

    public function __construct(CargoId $cargoId, Facility $facility)
    {
        $this->cargoId = $cargoId;
        $this->facility = $facility;
    }

    public function cargoId(): CargoId
    {
        return $this->cargoId;
    }

    public function facility(): Facility
    {
        return $this->facility;
    }
}
