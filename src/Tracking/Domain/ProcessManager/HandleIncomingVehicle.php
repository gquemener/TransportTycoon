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
use App\Tracking\Domain\Model\CargoIsAlreadyLoaded;
use App\Tracking\Domain\Command\UnloadCargo;

final class HandleIncomingVehicle
{
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function onVehicleHasBeenAdded(VehicleHasBeenAdded $event): void
    {
        $this->commandBus->dispatch(
            new LoadPendingCargo(
                Vehicle::create(
                    $event->vehicleFleetId(),
                    $event->vehicleName()
                ),
                Facility::named($event->vehiclePosition()->toString()),
            )
        );
    }

    public function onVehicleHasEnteredFacility(VehicleHasEnteredFacility $event): void
    {
        $facility = Facility::named($event->vehiclePosition()->toString());

        try {
            $this->commandBus->dispatch(
                new LoadPendingCargo(
                    Vehicle::create(
                        $event->vehicleFleetId(),
                        $event->vehicleName()
                    ),
                    $facility
                )
            );
        } catch (CargoIsAlreadyLoaded $exception) {
            $this->commandBus->dispatch(
                new UnloadCargo(
                    $exception->cargoId(),
                    $facility
                )
            );
        }
    }
}
