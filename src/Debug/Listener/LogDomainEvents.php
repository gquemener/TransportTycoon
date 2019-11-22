<?php
declare(strict_types=1);

namespace App\Debug\Listener;

use App\TraficRegulation\Domain\Event\VehicleHasEnteredFacility;
use App\TraficRegulation\Domain\Event\VehicleRouteHasBeenSet;
use Symfony\Component\Console\Output\OutputInterface;
use App\TraficRegulation\Domain\Model\Facility;
use App\TraficRegulation\Domain\Event\VehicleHasBeenAdded;
use App\Tracking\Domain\Event\CargoWasLoaded;
use App\Tracking\Domain\Event\CargoWasUnloaded;
use App\Tracking\Domain\Model\CargoId;
use App\Tracking\Domain\Event\CargoWasRegistered;

final class LogDomainEvents
{
    private const EVENT_DEPART = 'DEPART';
    private const EVENT_ARRIVE = 'ARRIVE';
    private const TRANSPORT_KIND_TRUCK = 'TRUCK';
    private const TRANSPORT_KIND_SHIP = 'SHIP';

    private static $facilityLogNames = [
        'Factory' => 'FACTORY',
        'Port' => 'PORT',
        'Warehouse A' => 'A',
        'Warehouse B' => 'B',
    ];

    private static $vehicleLogId = [
        'Truck 1' => 0,
        'Truck 2' => 1,
        'Ship' => 2,
    ];

    private $logFile;
    private $resource;

    private $time = 0;

    private $cargoToOrigin = [];
    private $cargoToDestination = [];
    private $cargoToId = [];
    private $vehicleToCargo = [];
    private $vehicleToOrigin = [];
    private $vehicleToId = [];

    public function setLogFile(string $logFile): void
    {
        $this->logFile = $logFile;
    }

    public function onVehicleHasEnteredFacility(VehicleHasEnteredFacility $event): void
    {
        $vehicle = $event->vehicle();

        /** @var Facility */
        $position = $vehicle->position();

        $vehicleFleetId = $event->vehicleFleetId()->toString();
        $cargoId = null;
        if (isset($this->vehicleToCargo[$vehicleFleetId][$vehicle->name()])) {
            $cargoId = $this->vehicleToCargo[$vehicleFleetId][$vehicle->name()];
        }

        $this->log(
            self::EVENT_ARRIVE,
            $this->time,
            self::$vehicleLogId[$vehicle->name()],
            0 === strpos($vehicle->name(), 'Truck') ? self::TRANSPORT_KIND_TRUCK : self::TRANSPORT_KIND_SHIP,
            $position->toString(),
            null,
            $cargoId ? $this->cargoToId[$cargoId] : null,
            $cargoId ? $this->cargoToDestination[$cargoId] : null,
            $cargoId ? $this->cargoToOrigin[$cargoId] : null
        );

        $this->vehicleToOrigin[$vehicleFleetId][$vehicle->name()] =
            $position->toString();
    }

    public function onVehicleRouteHasBeenSet(VehicleRouteHasBeenSet $event): void
    {
        $vehicle = $event->vehicle();
        /** @var \App\TraficRegulation\Domain\Model\Route */
        $position = $vehicle->position();
        $vehicleFleetId = $event->vehicleFleetId()->toString();
        $cargoId = null;
        if (isset($this->vehicleToCargo[$vehicleFleetId][$vehicle->name()])) {
            $cargoId = $this->vehicleToCargo[$vehicleFleetId][$vehicle->name()];
        }

        $this->log(
            self::EVENT_DEPART,
            $this->time,
            self::$vehicleLogId[$vehicle->name()],
            0 === strpos($vehicle->name(), 'Truck') ? self::TRANSPORT_KIND_TRUCK : self::TRANSPORT_KIND_SHIP,
            $this->vehicleToOrigin[$vehicleFleetId][$vehicle->name()],
            $position->destination()->toString(),
            $cargoId ? $this->cargoToId[$cargoId] : null,
            $cargoId ? $this->cargoToDestination[$cargoId] : null,
            $cargoId ? $this->cargoToOrigin[$cargoId] : null
        );
    }

    public function onVehicleHasBeenAdded(VehicleHasBeenAdded $event): void
    {
        $vehicleFleetId = $event->vehicleFleetId()->toString();
        $vehicle = $event->vehicle();
        /** @var Facility */
        $position = $vehicle->position();


        $this->vehicleToOrigin[$vehicleFleetId][$vehicle->name()] =
            $position->toString();
        if (!isset($this->vehicleToId[$vehicleFleetId])) {
            $this->vehicleToId[$vehicleFleetId] = [];
        }
        $this->vehicleToId[$vehicleFleetId][$vehicle->name()] = count($this->vehicleToId[$vehicleFleetId]);
    }

    public function onCargoWasRegistered(CargoWasRegistered $event): void
    {
        $cargoId = $event->cargoId()->toString();
        $this->cargoToOrigin[$cargoId] = $event->position()->toString();
        $this->cargoToDestination[$cargoId] = $event->destination()->toString();
        $this->cargoToId[$cargoId] = count($this->cargoToId);
    }

    public function onCargoWasLoaded(CargoWasLoaded $event): void
    {
        $vehicle = $event->vehicle();
        $vehicleFleetId = $vehicle->vehicleFleetId()->toString();
        $vehicleName = $vehicle->name();
        $cargoId = $event->cargoId()->toString();

        if (
            isset($this->vehicleToCargo[$vehicleFleetId][$vehicleName])
            && in_array($cargoId, $this->vehicleToCargo[$vehicleFleetId][$vehicleName])
        ) {
            throw new \LogicException('Cargo is already loaded');
        }

        $this->vehicleToCargo[$vehicleFleetId][$vehicleName] = $cargoId;
    }

    public function onCargoWasUnloaded(CargoWasUnloaded $event): void
    {
        foreach ($this->vehicleToCargo as $fleetId => $vehicles) {
            foreach ($vehicles as $name => $cargoId) {
                if ($event->cargoId()->equals(CargoId::fromString($cargoId))) {
                    unset($this->vehicleToCargo[$fleetId][$name]);

                    return;
                }
            }
        }
    }

    public function onSimulationHasStarted(): void
    {
        $this->incTime();
    }

    public function onVehicleFleetHasBeenRepositioned(): void
    {
        $this->incTime();
    }

    private function incTime()
    {
        ++$this->time;
    }

    private function log(
        string $event,
        int $time,
        int $transportId,
        string $transportKind,
        string $location,
        ?string $destination,
        ?int $cargoId,
        ?string $cargoDestination,
        ?string $cargoOrigin
    ): void {
        if (null === $this->logFile) {
            return;
        }

        $data = [
            'event' => $event,
            'time' => $time,
            'transport_id' => $transportId,
            'kind' => $transportKind,
            'location' => self::$facilityLogNames[$location],
        ];

        if (null !== $destination) {
            $data['destination'] = self::$facilityLogNames[$destination];
        }

        if (null !== $cargoId) {
            $data['cargo'] = [
                [
                    'cargo_id' => $cargoId,
                    'destination' => self::$facilityLogNames[$cargoDestination],
                    'origin' => self::$facilityLogNames[$cargoOrigin],
                ]
            ];
        }

        if (null === $this->resource) {
            if (is_file($this->logFile)) {
                unlink($this->logFile);
            }
            $this->resource = fopen($this->logFile, 'a', false);
        }
        fwrite($this->resource, json_encode($data) . "\n");
    }
}
