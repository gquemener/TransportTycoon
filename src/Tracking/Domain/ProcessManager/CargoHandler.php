<?php
declare(strict_types=1);

namespace App\Tracking\Domain\ProcessManager;

use App\ServiceBus\CommandBus;
use App\Tracking\Domain\Command\LoadCargo;
use App\Tracking\Domain\Command\LoadPendingCargo;
use App\Tracking\Domain\Command\LoadVehicle;
use App\Tracking\Domain\Command\UnloadCargo;
use App\Tracking\Domain\Event\CargoWasLoaded;
use App\Tracking\Domain\Event\CargoWasRegistered;
use App\Tracking\Domain\Event\CargoWasUnloaded;
use App\Tracking\Domain\Model\CargoId;
use App\Tracking\Domain\Model\CargoRepository;
use App\Tracking\Domain\Model\Facility;
use App\Tracking\Domain\Model\Vehicle;
use App\Tracking\Domain\Model\VehicleIsAlreadyLoaded;
use App\TraficRegulation\Domain\Event\VehicleHasBeenAdded;
use App\TraficRegulation\Domain\Event\VehicleHasEnteredFacility;
use App\TraficRegulation\Domain\Event\VehicleRouteHasBeenSet;

final class CargoHandler
{
    private $commandBus;

    private $loadedVehicles = [];
    private $facilityCargos = [];
    private $facilityVehicles = [];

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function onCargoWasRegistered(CargoWasRegistered $event): void
    {
        $this->facilityCargos[$event->position()->toString()][] = $event->cargoId()->toString();
    }

    public function onVehicleHasBeenAdded(VehicleHasBeenAdded $event): void
    {
        $vehicle = Vehicle::create(
            $event->vehicleFleetId(),
            $event->vehicle()->name()
        );
        /** @var Facility */
        $facility = $event->vehicle()->position();
        $position = Facility::named($facility->toString());

        $this->facilityVehicles[$position->toString()][] = $vehicle;

        $this->dispatchLoadCargoCommand($vehicle, $position);
    }

    public function onVehicleHasEnteredFacility(VehicleHasEnteredFacility $event): void
    {
        $vehicle = Vehicle::create(
            $event->vehicleFleetId(),
            $event->vehicle()->name()
        );
        /** @var Facility */
        $facility = $event->vehicle()->position();
        $position = Facility::named($facility->toString());
        $this->facilityVehicles[$position->toString()][] = $vehicle;

        if (isset($this->loadedVehicles[$vehicle->vehicleFleetId()->toString()][$vehicle->name()])) {
            $cargoId = $this->loadedVehicles[$vehicle->vehicleFleetId()->toString()][$vehicle->name()];
            unset($this->loadedVehicles[$vehicle->vehicleFleetId()->toString()][$vehicle->name()]);

            $this->commandBus->dispatch(new UnloadCargo($cargoId, $position));

            return;
        }

        $this->dispatchLoadCargoCommand($vehicle, $position);
    }

    public function onVehicleRouteHasBeenSet(VehicleRouteHasBeenSet $event): void
    {
        $leavingVehicle = Vehicle::create(
            $event->vehicleFleetId(),
            $event->vehicle()->name()
        );

        foreach ($this->facilityVehicles as $facility => $vehicles) {
            foreach ($vehicles as $key => $vehicle) {
                if ($vehicle->equals($leavingVehicle)) {
                    unset($this->facilityVehicles[$facility][$key]);
                }
            }
        }
    }

    public function onCargoWasLoaded(CargoWasLoaded $event): void
    {

        $vehicle = $event->vehicle();

        $this->loadedVehicles[$vehicle->vehicleFleetId()->toString()][$vehicle->name()] = $event->cargoId();
    }

    public function onCargoWasUnloaded(CargoWasUnloaded $event): void
    {
        if ($event->hasReachedDestination()) {
            return;
        }

        $position = $event->position();
        $this->facilityCargos[$position->toString()][] = $event->cargoId()->toString();

        if (0 === count($this->facilityVehicles[$position->toString()])) {
            return;
        }
        $vehicle = array_shift($this->facilityVehicles[$position->toString()]);
        $this->dispatchLoadCargoCommand($vehicle, $position);
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
        $this->commandBus->dispatch(new LoadCargo(CargoId::fromString($cargoId), $vehicle));
    }
}
