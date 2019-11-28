<?php
declare(strict_types=1);

namespace App\TransportTycoon\Domain\Service;

use App\TransportTycoon\Domain\Model\FacilityName;
use App\TransportTycoon\Domain\Model\Route;

interface RouteFinder
{
    public function find(FacilityName $origin, FacilityName $destination): Route;
}
