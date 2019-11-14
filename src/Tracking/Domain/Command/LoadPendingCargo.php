<?php
declare(strict_types=1);

namespace App\Tracking\Domain\Command;

use App\Tracking\Domain\Model\Facility;
use App\Tracking\Domain\Model\Vehicle;

final class LoadPendingCargo
{
    private $vehicle;
    private $facility;

    public function __construct(
        Vehicle $vehicle,
        Facility $facility
    ) {
        $this->vehicle = $vehicle;
        $this->facility = $facility;
    }

    public function vehicle(): Vehicle
    {
        return $this->vehicle;
    }

    public function facility(): Facility
    {
        return $this->facility;
    }
}
