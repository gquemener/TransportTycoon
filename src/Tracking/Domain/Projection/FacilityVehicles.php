<?php
declare(strict_types=1);

namespace App\Tracking\Domain\Projection;

use App\TraficRegulation\Domain\Event\VehicleHasEnteredFacility;
use App\TraficRegulation\Domain\Event\VehicleRouteHasBeenSet;
use App\Tracking\Domain\Model\Facility;
use App\Tracking\Domain\Model\Vehicle;

final class FacilityVehicles
{
    private $vehicles = [];

    public function onVehicleHasBeenAdded(VehicleHasBeenAdded $event): void
    {
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

        $this->vehicles[$position->toString()][] = $vehicle;
    }

    public function onVehicleRouteHasBeenSet(VehicleRouteHasBeenSet $event): void
    {
        $leavingVehicle = Vehicle::create(
            $event->vehicleFleetId(),
            $event->vehicle()->name()
        );

        foreach ($this->vehicles as $facility => $vehicles) {
            foreach ($vehicles as $key => $vehicle) {
                if ($vehicle->equals($leavingVehicle)) {
                    unset($this->vehicles[$facility][$key]);
                }
            }
        }
    }

    public function pushVehicle(Facility $facility, Vehicle $vehicle): void
    {
        $this->vehicles[$facility->toString()][] = $vehicle;
    }

    public function popVehicle(Facility $facility): ?Vehicle
    {
        $name = $facility->toString();
        if (isset($this->vehicles[$name]) && !empty($this->vehicles[$name])) {
            return array_shift($this->vehicles[$name]);
        }

        return null;
    }
}
