<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\Model;

interface RouteFinder
{
    public function find(Facility $origin, Facility $destination): Route;
}
