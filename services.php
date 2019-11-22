<?php

use App\Console\LogDomainCommandToConsoleDecorator;
use App\Console\LogDomainEventToConsoleDecorator;
use App\Debug\Listener\LogDomainEvents;
use App\ServiceBus\CommandBus;
use App\ServiceBus\EventBus;
use App\ServiceBus\SimpleEventBus;
use App\ServiceBus\SymfonyLocatorCommandBus;
use App\Simulation\Application\Service\StaticSimulator;
use App\Simulation\Domain\Command\StartSimulation;
use App\Simulation\Domain\Command\StartSimulationHandler;
use App\Simulation\Domain\Command\WaitOneHour;
use App\Simulation\Domain\Command\WaitOneHourHandler;
use App\Simulation\Domain\Event\OneHourHasPassed;
use App\Simulation\Domain\Event\SimulationHasStarted;
use App\Simulation\Domain\Service\Simulator;
use App\Tracking\Domain\Command\LoadCargo;
use App\Tracking\Domain\Command\LoadCargoHandler;
use App\Tracking\Domain\Command\RegisterCargoInTheFacility;
use App\Tracking\Domain\Command\RegisterCargoInTheFacilityHandler;
use App\Tracking\Domain\Command\UnloadCargo;
use App\Tracking\Domain\Command\UnloadCargoHandler;
use App\Tracking\Domain\Event\CargoWasLoaded;
use App\Tracking\Domain\Event\CargoWasRegistered;
use App\Tracking\Domain\Event\CargoWasUnloaded;
use App\Tracking\Domain\Model\CargoRepository;
use App\Tracking\Domain\ProcessManager\CargoHandler;
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
use App\TraficRegulation\Domain\Event\VehicleFleetHasBeenCreated;
use App\TraficRegulation\Domain\Event\VehicleFleetHasBeenRepositioned;
use App\TraficRegulation\Domain\Event\VehicleHasBeenAdded;
use App\TraficRegulation\Domain\Event\VehicleHasEnteredFacility;
use App\TraficRegulation\Domain\Event\VehicleRouteHasBeenSet;
use App\TraficRegulation\Domain\Event\VehicleWasRegistered;
use App\TraficRegulation\Domain\Model\VehicleFleetRepository;
use App\TraficRegulation\Domain\ProcessManager\DefineVehicleDestination;
use App\TraficRegulation\Domain\ProcessManager\PlanVehicleRoute;
use App\TraficRegulation\Domain\ProcessManager\RepositionVehicles;
use App\TraficRegulation\Infrastructure\InMemoryVehicleFleetRepository;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\ServiceLocator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

return function(ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services->set(CargoRepository::class, InMemoryCargoRepository::class)
             ->args([ref(EventBus::class)]);

    $services->set(VehicleFleetRepository::class, InMemoryVehicleFleetRepository::class)
             ->args([ref(EventBus::class)]);

    $services->set(CargoHandler::class)
             ->args([ref(CommandBus::class)]);

    $services->set(RegisterCargoInTheFacilityHandler::class)
             ->args([ref(CargoRepository::class)]);

    $services->set(LoadCargoHandler::class)
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

    $services->set(RepositionVehicles::class)
             ->args([ref(CommandBus::class)]);

    $services->set(StartSimulationHandler::class)
             ->args([ref(EventBus::class)]);

    $services->set(WaitOneHourHandler::class)
             ->args([ref(EventBus::class)]);

    $services->set(Simulator::class, StaticSimulator::class)
             ->args([ref(CommandBus::class)]);

    $services->set(LogDomainEvents::class);

    $services->set(CommandBus::class, SymfonyLocatorCommandBus::class)
             ->args([ref('app.command_handler_locator')]);

    $services->set(LogDomainCommandToConsoleDecorator::class)
        ->decorate(CommandBus::class)
        ->args([ref(LogDomainCommandToConsoleDecorator::class.'.inner')]);

    $services->set('app.command_handler_locator', ServiceLocator::class)
             ->args([[
                 // Vehicle Fleet Aggregate
                 CreateVehicleFleet::class => ref(CreateVehicleFleetHandler::class),
                 AddVehicle::class => ref(AddVehicleHandler::class),
                 ComputeVehicleRoute::class => ref(ComputeVehicleRouteHandler::class),
                 RepositionVehicleFleet::class => ref(RepositionVehicleFleetHandler::class),

                 // Cargo Aggregate
                 RegisterCargoInTheFacility::class => ref(RegisterCargoInTheFacilityHandler::class),
                 LoadCargo::class => ref(LoadCargoHandler::class),
                 UnloadCargo::class => ref(UnloadCargoHandler::class),

                 // Simulation Domain
                 StartSimulation::class => ref(StartSimulationHandler::class),
                 WaitOneHour::class => ref(WaitOneHourHandler::class),
             ]])
             ->tag('container.service_locator');

    $services->set(EventBus::class, SimpleEventBus::class)
             ->args([[
                 VehicleHasBeenAdded::class => [
                     [ ref(LogDomainEvents::class), 'onVehicleHasBeenAdded' ],
                     [ ref(CargoHandler::class), 'onVehicleHasBeenAdded' ],
                     [ ref(PlanVehicleRoute::class), 'onVehicleHasBeenAdded' ],
                 ],
                 VehicleHasEnteredFacility::class => [
                     [ ref(LogDomainEvents::class), 'onVehicleHasEnteredFacility' ],
                     [ ref(CargoHandler::class), 'onVehicleHasEnteredFacility' ],
                 ],
                 VehicleRouteHasBeenSet::class => [
                     [ ref(LogDomainEvents::class), 'onVehicleRouteHasBeenSet' ],
                     [ ref(CargoHandler::class), 'onVehicleRouteHasBeenSet' ],
                 ],
                 CargoWasRegistered::class => [
                     [ ref(LogDomainEvents::class), 'onCargoWasRegistered' ],
                     [ ref(CargoHandler::class), 'onCargoWasRegistered' ],
                     [ ref(PlanVehicleRoute::class), 'onCargoWasRegistered' ],
                     [ ref(Simulator::class), 'onCargoWasRegistered' ],
                 ],
                 CargoWasLoaded::class => [
                     [ ref(LogDomainEvents::class), 'onCargoWasLoaded' ],
                     [ ref(CargoHandler::class), 'onCargoWasLoaded' ],
                     [ ref(PlanVehicleRoute::class), 'onCargoWasLoaded' ],
                 ],
                 CargoWasUnloaded::class => [
                     [ ref(LogDomainEvents::class), 'onCargoWasUnloaded' ],
                     [ ref(PlanVehicleRoute::class), 'onCargoWasUnloaded' ],
                     [ ref(CargoHandler::class), 'onCargoWasUnloaded' ],
                     [ ref(Simulator::class), 'onCargoWasUnloaded' ],
                 ],
                 VehicleFleetHasBeenRepositioned::class => [
                     [ ref(LogDomainEvents::class), 'onVehicleFleetHasBeenRepositioned' ],
                 ],
                 SimulationHasStarted::class => [
                     [ ref(LogDomainEvents::class), 'onSimulationHasStarted' ],
                 ],
                 VehicleFleetHasBeenCreated::class => [
                     [ ref(RepositionVehicles::class), 'onVehicleFleetHasBeenCreated' ],
                 ],
                 OneHourHasPassed::class => [
                     [ ref(RepositionVehicles::class), 'onOneHourHasPassed' ],
                 ],
             ]]);

    $services->set(LogDomainEventToConsoleDecorator::class)
        ->decorate(EventBus::class)
        ->args([ref(LogDomainEventToConsoleDecorator::class.'.inner')]);
};
