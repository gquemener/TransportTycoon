<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\Command;

use App\TraficRegulation\Domain\Model\VehicleRepository;
use App\TraficRegulation\Domain\Model\Facility;
use App\TraficRegulation\Domain\Model\Route;

final class ComputeVehicleRouteHandler
{
    private $repository;

    public function __construct(VehicleRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(ComputeVehicleRoute $command): void
    {
        if (null === $vehicle = $this->repository->find($command->vehicleId())) {
            throw new \InvalidArgumentException('Could not find vehicle');
        }

        $destination = $command->destination();
        $position = $vehicle->position();

        switch ($position) {
            case Facility::named('Factory'):
                $route = $this->getRouteFromFactory($destination);
                break;

            case Facility::named('Port'):
                $route = $this->getRouteFromPort($destination);
                break;

            case Facility::named('Warehouse A'):
                $route = $this->getRouteFromWarehouseA($destination);
                break;

            case Facility::named('Warehouse B'):
                $route = $this->getRouteFromWarehouseB($destination);
                break;

            default:
                throw new \RuntimeException(sprintf(
                    'Could not compute vehicle route from unknown position "%s"',
                    $position->toString()
                ));
        }
        var_dump($route);
        $vehicle->followRoute($route);
        $this->repository->persist($vehicle);
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
            case Facility::named('Facility'):
                return Route::to(
                    Facility::named('Facility'),
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
