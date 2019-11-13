<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\Service;

use App\Transportation\Domain\Model\Route;
use App\Transportation\Domain\Model\Facility;

final class RouteFinder
{
    public function between(Facility $start, Facility $end, array $visited = []): ?Route
    {
        foreach ($start->routes() as $route) {
            if (
                !in_array($route->end(), $visited)
                && (
                    $route->end()->equals($end)
                    || $this->between($route->end(), $end, array_merge($visited, [$start])) instanceof Route
                )
            ) {
                return $route;
            }
        }

        return null;
    }
}
