<?php

use App\ServiceBus\CommandBus;
use App\ServiceBus\EventBus;
use App\Tracking\Domain\Command\LoadPendingCargo;
use App\Tracking\Domain\Command\LoadPendingCargoHandler;
use App\Tracking\Domain\Command\RegisterCargoInTheFacility;
use App\Tracking\Domain\Command\RegisterCargoInTheFacilityHandler;
use App\Tracking\Domain\Event\CargoWasLoaded;
use App\Tracking\Domain\Model\CargoRepository;
use App\Tracking\Domain\ProcessManager\LoadEmptyVehicle;
use App\Tracking\Infrastructure\InMemoryCargoRepository;
use App\TraficRegulation\Domain\Command\AddVehicle;
use App\TraficRegulation\Domain\Command\AddVehicleHandler;
use App\TraficRegulation\Domain\Command\ComputeVehicleDestination;
use App\TraficRegulation\Domain\Command\ComputeVehicleDestinationHandler;
use App\TraficRegulation\Domain\Event\VehicleWasRegistered;
use App\TraficRegulation\Domain\Model\VehicleRepository;
use App\TraficRegulation\Domain\ProcessManager\DefineVehicleDestination;
use App\TraficRegulation\Infrastructure\InMemoryVehicleRepository;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\ServiceLocator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;
use App\TraficRegulation\Domain\ProcessManager\PlanVehicleRoute;
use App\TraficRegulation\Domain\Command\ComputeVehicleRoute;
use App\TraficRegulation\Domain\Command\ComputeVehicleRouteHandler;

return function(ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services->set(CargoRepository::class, InMemoryCargoRepository::class)
             ->args([ref(EventBus::class)]);

    $services->set(VehicleRepository::class, InMemoryVehicleRepository::class)
             ->args([ref(EventBus::class)]);

    $services->set(LoadEmptyVehicle::class)
             ->args([ref(CommandBus::class)]);

    $services->set(RegisterCargoInTheFacilityHandler::class)
             ->args([ref(CargoRepository::class)]);

    $services->set(LoadPendingCargoHandler::class)
             ->args([ref(CargoRepository::class)]);

    $services->set(AddVehicleHandler::class)
             ->args([ref(VehicleRepository::class)]);

    $services->set(ComputeVehicleRouteHandler::class)
             ->args([ref(VehicleRepository::class)]);

    $services->set(PlanVehicleRoute::class)
             ->args([ref(CommandBus::class)]);

    $services->set(CommandBus::class)
             ->args([ref('app.command_handler_locator')]);

    $services->set('app.command_handler_locator', ServiceLocator::class)
             ->args([[
                 RegisterCargoInTheFacility::class => ref(RegisterCargoInTheFacilityHandler::class),
                 AddVehicle::class => ref(AddVehicleHandler::class),
                 LoadPendingCargo::class => ref(LoadPendingCargoHandler::class),
                 ComputeVehicleRoute::class => ref(ComputeVehicleRouteHandler::class),
             ]])
             ->tag('container.service_locator');

    $services->set(EventBus::class)
             ->args([[
                 VehicleWasRegistered::class => [
                     [ ref(LoadEmptyVehicle::class), 'onVehicleWasRegistered' ]
                 ],
                 CargoWasLoaded::class => [
                     [ ref(PlanVehicleRoute::class), 'onCargoWasLoaded' ]
                 ]
             ]]);
};
