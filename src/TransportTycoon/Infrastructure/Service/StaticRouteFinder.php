<?php
declare(strict_types=1);

namespace App\TransportTycoon\Infrastructure\Service;

use App\TransportTycoon\Domain\Service\RouteFinder;
use App\TransportTycoon\Domain\Model\Facility;
use App\TransportTycoon\Domain\Model\Route;

final class StaticRouteFinder implements RouteFinder
{
    public function find(FacilityName $origin, FacilityName $destination): Route
    {
        switch ($origin) {
            case Facility::FACTORY():
                return $this->getRouteFromFactory($destination);

            case Facility::PORT():
                return $this->getRouteFromPort($destination);

            case Facility::WAREHOUSE_A():
                return $this->getRouteFromWarehouseA($destination);

            case Facility::WAREHOUSE_B():
                return $this->getRouteFromWarehouseB($destination);

            default:
                throw new \RuntimeException(sprintf(
                    'Could not compute vehicle route from unknown position "%s"',
                    $position->toString()
                ));
        }
    }

    private function getRouteFromFactory(FacilityName $destination): Route
    {
        switch ($destination) {
            case Facility::WAREHOUSE_A():
                return Route::to(
                    Facility::PORT(),
                    1
                );

            case Facility::WAREHOUSE_B():
                return Route::to(
                    Facility::WAREHOUSE_B(),
                    5
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
            case Facility::FACTORY():
                return Route::to(
                    Facility::FACTORY(),
                    1
                );

            case Facility::WAREHOUSE_A():
                return Route::to(
                    Facility::WAREHOUSE_A(),
                    6
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
            case Facility::FACTORY():
            case Facility::PORT():
                return Route::to(
                    Facility::PORT(),
                    6
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
            case Facility::FACTORY():
                return Route::to(
                    Facility::FACTORY(),
                    5
                );

            default:
                throw new \RuntimeException(sprintf(
                    'Could not compute vehicle route from "Warehouse B" to "%s"',
                    $destination->toString()
                ));
        }
    }
}
