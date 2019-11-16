<?php
declare(strict_types=1);

namespace App\Simulation\Application\Service;

use App\ServiceBus\CommandBus;
use App\Simulation\Domain\Service\Simulator;
use App\Tracking\Domain\Command\RegisterCargoInTheFacility;
use App\Tracking\Domain\Event\CargoWasRegistered;
use App\Tracking\Domain\Event\CargoWasUnloaded;
use App\Tracking\Domain\Model\CargoId;
use App\Tracking\Domain\Model\Facility as TrackingFacility;
use App\TraficRegulation\Domain\Command\AddVehicle;
use App\TraficRegulation\Domain\Command\CreateVehicleFleet;
use App\TraficRegulation\Domain\Command\RepositionVehicleFleet;
use App\TraficRegulation\Domain\Model\Facility as TraficRegulationFacility;
use App\TraficRegulation\Domain\Model\VehicleFleetId;

final class StaticSimulator implements Simulator
{
    private const MAX_LOOPS = 100;

    private $commandBus;
    private $loops = 0;
    private $cargoDestinations = [];

    public function __construct(
        CommandBus $commandBus
    ) {
        $this->commandBus = $commandBus;
    }

    public function run(array $cargoDestinations): void
    {
        $this->loops = 0;
        $this->cargoDestinations = [];

        foreach ($cargoDestinations as $destination) {
            $this->commandBus->dispatch(new RegisterCargoInTheFacility(
                CargoId::generate(),
                TrackingFacility::named('Factory'),
                TrackingFacility::named($destination),
            ));
        }

        $vehicleFleetId = VehicleFleetId::generate();
        $this->commandBus->dispatch(new CreateVehicleFleet($vehicleFleetId));

        $this->commandBus->dispatch(new AddVehicle(
            $vehicleFleetId,
            'Ship',
            TraficRegulationFacility::named('Port')
        ));
        $this->commandBus->dispatch(new AddVehicle(
            $vehicleFleetId,
            'Truck 1',
            TraficRegulationFacility::named('Factory')
        ));
        $this->commandBus->dispatch(new AddVehicle(
            $vehicleFleetId,
            'Truck 2',
            TraficRegulationFacility::named('Factory')
        ));

        do {
            ++$this->loops;
            $this->commandBus->dispatch(new RepositionVehicleFleet($vehicleFleetId));

        } while ($this->loops < self::MAX_LOOPS && 0 < count($this->cargoDestinations));
    }

    public function onCargoWasRegistered(CargoWasRegistered $event): void
    {
        $this->cargoDestinations[$event->cargoId()->toString()] = $event->destination();
    }

    public function onCargoWasUnloaded(CargoWasUnloaded $event): void
    {
        $cargoId = $event->cargoId();
        $position = $event->position();

        if (!isset($this->cargoDestinations[$cargoId->toString()])) {
            throw new \InvalidArgumentException(sprintf(
                'Cargo "%s" has already been delivered',
                $cargoId->toString()
            ));
        }

        if ($position->equals($this->cargoDestinations[$cargoId->toString()])) {
            unset($this->cargoDestinations[$cargoId->toString()]);
        }
    }

    public function loops(): int
    {
        return $this->loops;
    }
}
