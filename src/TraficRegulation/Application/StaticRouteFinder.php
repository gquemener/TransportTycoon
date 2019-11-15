<?php
declare(strict_types=1);

namespace App\TraficRegulation\Application;

use App\TraficRegulation\Domain\Model\RouteFinder;
use App\TraficRegulation\Domain\Model\Facility;
use App\TraficRegulation\Domain\Model\Route;

final class StaticRouteFinder implements RouteFinder
{
    public function find(Facility $origin, Facility $destination): Route
    {
        switch ($origin) {
            case Facility::named('Factory'):
                return $this->getRouteFromFactory($destination);

            case Facility::named('Port'):
                return $this->getRouteFromPort($destination);

            case Facility::named('Warehouse A'):
                return $this->getRouteFromWarehouseA($destination);

            case Facility::named('Warehouse B'):
                return $this->getRouteFromWarehouseB($destination);

            default:
                throw new \RuntimeException(sprintf(
                    'Could not compute vehicle route from unknown position "%s"',
                    $position->toString()
                ));
        }
    }

    private function getRouteFromFactory(Facility $destination): Route
    {
        switch ($destination) {
            case Facility::named('Warehouse A'):
                return Route::to(
                    Facility::named('Warehouse A'),
                    5
                );

            case Facility::named('Warehouse B'):
                return Route::to(
                    Facility::named('Port'),
                    1
                );

            default:
                throw new \RuntimeException(sprintf(
                    'Could not compute vehicle route from "Factory" to "%s"',
                    $destination->toString()
                ));
        }
    }

    private function getRouteFromPort(Facility $destination): Route
    {
        switch ($destination) {
            case Facility::named('Factory'):
                return Route::to(
                    Facility::named('Factory'),
                    1
                );

            case Facility::named('Warehouse B'):
                return Route::to(
                    Facility::named('Warehouse B'),
                    4
                );

            default:
                throw new \RuntimeException(sprintf(
                    'Could not compute vehicle route from "Port" to "%s"',
                    $destination->toString()
                ));
        }
    }

    private function getRouteFromWarehouseA(Facility $destination): Route
    {
        switch ($destination) {
            case Facility::named('Facility'):
                return Route::to(
                    Facility::named('Facility'),
                    5
                );

            default:
                throw new \RuntimeException(sprintf(
                    'Could not compute vehicle route from "Warehouse A" to "%s"',
                    $destination->toString()
                ));
        }
    }

    private function getRouteFromWarehouseB(Facility $destination): Route
    {
        switch ($destination) {
            case Facility::named('Facility'):
                return Route::to(
                    Facility::named('Port'),
                    4
                );

            default:
                throw new \RuntimeException(sprintf(
                    'Could not compute vehicle route from "Warehouse B" to "%s"',
                    $destination->toString()
                ));
        }
    }
}
