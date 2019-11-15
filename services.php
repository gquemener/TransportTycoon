<?php

use App\ServiceBus\CommandBus;
use App\ServiceBus\EventBus;
use App\Tracking\Domain\Command\LoadPendingCargo;
use App\Tracking\Domain\Command\LoadPendingCargoHandler;
use App\Tracking\Domain\Command\RegisterCargoInTheFacility;
use App\Tracking\Domain\Command\RegisterCargoInTheFacilityHandler;
use App\Tracking\Domain\Event\CargoWasLoaded;
use App\Tracking\Domain\Event\CargoWasRegistered;
use App\Tracking\Domain\Model\CargoRepository;
use App\Tracking\Domain\ProcessManager\HandleIncomingVehicle;
use App\Tracking\Infrastructure\InMemoryCargoRepository;
use App\TraficRegulation\Domain\Command\AddVehicle;
use App\TraficRegulation\Domain\Command\AddVehicleHandler;
use App\TraficRegulation\Domain\Command\ComputeVehicleDestination;
use App\TraficRegulation\Domain\Command\ComputeVehicleDestinationHandler;
use App\TraficRegulation\Domain\Command\ComputeVehicleRoute;
use App\TraficRegulation\Domain\Command\ComputeVehicleRouteHandler;
use App\TraficRegulation\Domain\Command\CreateVehicleFleet;
use App\TraficRegulation\Domain\Command\CreateVehicleFleetHandler;
use App\TraficRegulation\Domain\Command\RepositionVehicleFleet;
use App\TraficRegulation\Domain\Command\RepositionVehicleFleetHandler;
use App\TraficRegulation\Domain\Event\VehicleHasBeenAdded;
use App\TraficRegulation\Domain\Event\VehicleWasRegistered;
use App\TraficRegulation\Domain\Model\VehicleFleetRepository;
use App\TraficRegulation\Domain\ProcessManager\DefineVehicleDestination;
use App\TraficRegulation\Domain\ProcessManager\PlanVehicleRoute;
use App\TraficRegulation\Infrastructure\InMemoryVehicleFleetRepository;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\ServiceLocator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;
use App\TraficRegulation\Domain\Event\VehicleHasEnteredFacility;
use App\Tracking\Domain\Command\UnloadCargoHandler;
use App\Tracking\Domain\Command\UnloadCargo;

return function(ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services->set(CargoRepository::class, InMemoryCargoRepository::class)
             ->args([ref(EventBus::class)]);

    $services->set(VehicleFleetRepository::class, InMemoryVehicleFleetRepository::class)
             ->args([ref(EventBus::class)]);

    $services->set(HandleIncomingVehicle::class)
             ->args([ref(CommandBus::class)]);

    $services->set(RegisterCargoInTheFacilityHandler::class)
             ->args([ref(CargoRepository::class)]);

    $services->set(LoadPendingCargoHandler::class)
             ->args([ref(CargoRepository::class)]);

    $services->set(UnloadCargoHandler::class)
             ->args([ref(CargoRepository::class)]);

    $services->set(CreateVehicleFleetHandler::class)
             ->args([ref(VehicleFleetRepository::class)]);

    $services->set(AddVehicleHandler::class)
             ->args([ref(VehicleFleetRepository::class)]);

    $services->set(ComputeVehicleRouteHandler::class)
             ->args([ref(VehicleFleetRepository::class)]);

    $services->set(RepositionVehicleFleetHandler::class)
             ->args([ref(VehicleFleetRepository::class)]);

    $services->set(PlanVehicleRoute::class)
             ->args([ref(CommandBus::class)]);

    $services->set(CommandBus::class)
             ->args([ref('app.command_handler_locator')]);

    $services->set('app.command_handler_locator', ServiceLocator::class)
             ->args([[
                 // Vehicle Fleet Aggregate
                 CreateVehicleFleet::class => ref(CreateVehicleFleetHandler::class),
                 AddVehicle::class => ref(AddVehicleHandler::class),
                 ComputeVehicleRoute::class => ref(ComputeVehicleRouteHandler::class),
                 RepositionVehicleFleet::class => ref(RepositionVehicleFleetHandler::class),

                 // Cargo Aggregate
                 RegisterCargoInTheFacility::class => ref(RegisterCargoInTheFacilityHandler::class),
                 LoadPendingCargo::class => ref(LoadPendingCargoHandler::class),
                 UnloadCargo::class => ref(UnloadCargoHandler::class),
             ]])
             ->tag('container.service_locator');

    $services->set(EventBus::class)
             ->args([[
                 VehicleHasBeenAdded::class => [
                     [ ref(HandleIncomingVehicle::class), 'onVehicleHasBeenAdded' ]
                 ],
                 VehicleHasEnteredFacility::class => [
                     [ ref(HandleIncomingVehicle::class), 'onVehicleHasEnteredFacility' ]
                 ],
                 CargoWasRegistered::class => [
                     [ ref(PlanVehicleRoute::class), 'onCargoWasRegistered' ]
                 ],
                 CargoWasLoaded::class => [
                     [ ref(PlanVehicleRoute::class), 'onCargoWasLoaded' ]
                 ],
             ]]);
};
