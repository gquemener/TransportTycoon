<?php
declare(strict_types=1);

namespace App\Tracking\Domain\ProcessManager;

use App\Tracking\Domain\Model\Facility;
use App\ServiceBus\CommandBus;
use App\Tracking\Domain\Model\CargoRepository;
use App\Tracking\Domain\Command\LoadPendingCargo;
use App\TraficRegulation\Domain\Event\VehicleHasBeenAdded;
use App\Tracking\Domain\Model\Vehicle;
use App\TraficRegulation\Domain\Event\VehicleHasEnteredFacility;
use App\Tracking\Domain\Model\VehicleIsAlreadyLoaded;
use App\Tracking\Domain\Command\UnloadCargo;
use App\Tracking\Domain\Event\CargoWasUnloaded;
use App\Tracking\Domain\Command\LoadVehicle;
use App\Tracking\Domain\Event\CargoWasLoaded;
use App\Tracking\Domain\Command\LoadCargo;
use App\Tracking\Domain\Event\CargoWasRegistered;

// TODO (2019-11-16 09:07 by Gildas): REPLUG ALL SERVICE DEFINITIONS!!!
final class CargoHandler
{
    private $commandBus;

    private $loadedVehicles = [];
    private $facilityCargos = [];

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function onCargoWasRegistered(CargoWasRegistered $event): void
    {
        $this->facilityCargos[$event->position()->toString()][] = $event->cargoId();
    }

    public function onVehicleHasBeenAdded(VehicleHasBeenAdded $event): void
    {
        $this->dispatchLoadCargoCommand(
            Vehicle::create($event->vehicleFleetId(), $event->vehicleName()),
            Facility::named($event->vehiclePosition()->toString())
        );
    }

    public function onVehicleHasEnteredFacility(VehicleHasEnteredFacility $event): void
    {
        $vehicle = Vehicle::create($event->vehicleFleetId(), $event->vehicleName());
        $position = Facility::named($event->vehiclePosition()->toString());

        if (isset($this->loadedVehicles[$vehicle->vehicleFleetId()->toString()][$vehicle->name()])) {
            $cargoId = $this->loadedVehicles[$vehicle->vehicleFleetId()->toString()][$vehicle->name()];
            unset($this->loadedVehicles[$vehicle->vehicleFleetId()->toString()][$vehicle->name()]);

            $this->commandBus->dispatch(new UnloadCargo($cargoId, $position));

            return;
        }

        $this->dispatchLoadCargoCommand($vehicle, $position);
    }

    public function onCargoWasLoaded(CargoWasLoaded $event): void
    {
        $vehicle = $event->vehicle();

        $this->loadedVehicles[$vehicle->vehicleFleetId()->toString()][$vehicle->name()] = $event->cargoId();
    }

    public function onCargoWasUnloaded(CargoWasUnloaded $event): void
    {
        $this->facilityCargos[$event->position()->toString()][] = $event->cargoId();
    }

    private function dispatchLoadCargoCommand(Vehicle $vehicle, Facility $facility): void
    {
        if (
            !isset($this->facilityCargos[$facility->toString()])
            || empty($this->facilityCargos[$facility->toString()])
        ) {
            return;
        }

        $cargoId = array_shift($this->facilityCargos[$facility->toString()]);
        $this->commandBus->dispatch(new LoadCargo($cargoId, $vehicle));
    }
}
