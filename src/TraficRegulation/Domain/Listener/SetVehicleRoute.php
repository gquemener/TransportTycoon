<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\Listener;

final class SetVehicleRoute
{
    private $routeFinder;

    public function __construct(RouteFinder $routeFinder)
    {
        $this->routeFinder = $routeFinder;
    }

    public function onLoad(CargoHasBeenLoaded $event): void
    {
        $vehicle = $event->vehicle();
        $cargo = $event->cargo();

        $route = $this->routeFinder->between($cargo->position(), $cargo->destination());

        if (null === $route) {
            throw new \RuntimeException('no route found');
        }

        $vehicle->setRoute($route);
    }

    public function onUnload(CargoHasBeenUnloaded $event): void
    {
        $vehicle = $event->vehicle();
        $route = $vehicle->route();

        $route = $this->routeFinder->between(
            $route->end(),
            $route->start()
        );

        if (null === $route) {
            throw new \RuntimeException('no route found');
        }

        $vehicle->setRoute($route);
    }

    public function getSubscribedEvents(): array
    {
        return [
            CargoHasBeenLoaded::class => 'onLoad',
            CargoHasBeenUnloaded::class => 'onUnload',
        ];
    }
}
