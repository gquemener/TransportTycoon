<?php

use App\Console\LogDomainCommandToConsoleDecorator;
use App\Console\LogDomainEventToConsoleDecorator;
use App\ServiceBus\CommandBus;
use App\ServiceBus\EventBus;
use App\ServiceBus\EventDispatchingCommandHandler;
use App\ServiceBus\SimpleEventBus;
use App\ServiceBus\SymfonyLocatorCommandBus;
use App\Tracer\Domain\Command\AppendArriveEntry;
use App\Tracer\Domain\Command\AppendArriveEntryHandler;
use App\Tracer\Domain\Command\AppendDepartEntry;
use App\Tracer\Domain\Command\AppendDepartEntryHandler;
use App\Tracer\Domain\Model\JournalRepository;
use App\Tracer\Domain\ProcessManager\AppendEntriesToJournal;
use App\Tracer\Infrastructure\FilesystemJournalRepository;
use App\TransportTycoon\Domain\Command\AddOneHour;
use App\TransportTycoon\Domain\Command\AddOneHourHandler;
use App\TransportTycoon\Domain\Command\FindVehicleDestination;
use App\TransportTycoon\Domain\Command\FindVehicleDestinationHandler;
use App\TransportTycoon\Domain\Command\UnloadVehicle;
use App\TransportTycoon\Domain\Command\UnloadVehicleHandler;
use App\TransportTycoon\Domain\Event\VehicleHasMoved;
use App\TransportTycoon\Domain\Event\VehicleHasParkedInFacility;
use App\TransportTycoon\Domain\Event\VehicleWasLoaded;
use App\TransportTycoon\Domain\Event\VehicleWasUnloaded;
use App\TransportTycoon\Domain\ProcessManager\PlanVehicleRoute;
use App\TransportTycoon\Domain\ProcessManager\VehicleUnloading;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator;
use Symfony\Component\DependencyInjection\ServiceLocator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

return function(ContainerConfigurator $configurator) {
    $services = $configurator->services();
    $decorateCommandHandler = function(string $id) use ($services) {
        static $count = 0;
        $services->set(EventDispatchingCommandHandler::class . $count, EventDispatchingCommandHandler::class)
                 ->decorate($id)
                 ->args([
                     ref(EventDispatchingCommandHandler::class . $count . '.inner'),
                     ref(EventBus::class)
                 ]);
        $count++;
    };

    $services->set(AddOneHourHandler::class);
    $decorateCommandHandler(AddOneHourHandler::class);

    $services->set(AppendDepartEntryHandler::class)
        ->args([ref(JournalRepository::class)]);

    $services->set(AppendArriveEntryHandler::class)
        ->args([ref(JournalRepository::class)]);

    $services->set(JournalRepository::class, FilesystemJournalRepository::class);

    $services->set(FindVehicleDestinationHandler::class);
    $decorateCommandHandler(FindVehicleDestinationHandler::class);

    $services->set(UnloadVehicleHandler::class);
    $decorateCommandHandler(UnloadVehicleHandler::class);

    $services->set(CommandBus::class, SymfonyLocatorCommandBus::class)
             ->args([ref('app.command_handler_locator')]);

    $services->set(LogDomainCommandToConsoleDecorator::class)
        ->decorate(CommandBus::class)
        ->args([ref(LogDomainCommandToConsoleDecorator::class.'.inner')]);

    $services->set('app.command_handler_locator', ServiceLocator::class)
             ->args([[
                 AddOneHour::class => ref(AddOneHourHandler::class),
                 FindVehicleDestination::class => ref(FindVehicleDestinationHandler::class),
                 UnloadVehicle::class => ref(UnloadVehicleHandler::class),
                 AppendDepartEntry::class => ref(AppendDepartEntryHandler::class),
                 AppendArriveEntry::class => ref(AppendArriveEntryHandler::class),
             ]])
             ->tag('container.service_locator');

    $services->set(PlanVehicleRoute::class)
             ->args([ref(CommandBus::class)]);

    $services->set(VehicleUnloading::class)
             ->args([ref(CommandBus::class)]);

    $services->set(AppendEntriesToJournal::class)
             ->args([ref(CommandBus::class)]);

    $services->set(EventBus::class, SimpleEventBus::class)
             ->args([[
                 VehicleWasLoaded::class => [
                     ref(PlanVehicleRoute::class),
                 ],
                 VehicleWasUnloaded::class => [
                     ref(PlanVehicleRoute::class),
                 ],
                 VehicleHasParkedInFacility::class => [
                     ref(VehicleUnloading::class),
                 ],
             ]]);

    $services->set(LogDomainEventToConsoleDecorator::class)
        ->decorate(EventBus::class)
        ->args([ref(LogDomainEventToConsoleDecorator::class.'.inner')]);
};
