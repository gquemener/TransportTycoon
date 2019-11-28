<?php
declare(strict_types=1);

namespace App\TransportTycoon\Domain\Model;

use App\ServiceBus\CommandBus;
use App\TransportTycoon\Domain\Model\VehicleFleet;
use App\TransportTycoon\Domain\Command\AddOneHour;

final class Simulation
{
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function timeToDeliver(Cargo ...$cargos): int
    {
        $factory = Facility::build(FacilityName::FACTORY());
        $port = Facility::build(FacilityName::PORT());
        $warehouseA = Facility::build(FacilityName::WAREHOUSE_A());
        $warehouseB = Facility::build(FacilityName::WAREHOUSE_B());

        $factory->connect($port, 1);
        $port->connect($warehouseA, 6);
        $factory->connect($warehouseB, 5);

        foreach ($cargos as $cargo) {
            $cargo->moveTo($factory);
        }

        $world = World::create(
            $factory,
            [
                Vehicle::truck($factory),
                Vehicle::truck($factory),
                Vehicle::ship($port)
            ],
            $cargos
        );

        $loops = 0;
        while ($world->hasNonDeliveredCargos() && $loops++ <= 100) {
            $this->commandBus->dispatch(new AddOneHour($world));
        }

        return $world->age() - 1;
    }
}
