<?php

use App\Tracking;
use App\TraficRegulation;
use App\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\Config\FileLocator;
use App\ServiceBus\CommandBus;

require __DIR__.'/../vendor/autoload.php';

$containerBuilder = new ContainerBuilder();
$loader = new PhpFileLoader($containerBuilder, new FileLocator(__DIR__.'/..'));
$loader->load('services.php');
$containerBuilder->compile();


/** @var CommandBus */
$commandBus = $containerBuilder->get(CommandBus::class);

$cargoId1 = Tracking\Domain\Model\CargoId::generate();
$cargoId2 = Tracking\Domain\Model\CargoId::generate();
$cargoId3 = Tracking\Domain\Model\CargoId::generate();

$commandBus->dispatch(new Tracking\Domain\Command\RegisterCargoInTheFacility(
    $cargoId1,
    Tracking\Domain\Model\Facility::named('Factory'),
    Tracking\Domain\Model\Facility::named('Warehouse A')
));
$commandBus->dispatch(new Tracking\Domain\Command\RegisterCargoInTheFacility(
    $cargoId2,
    Tracking\Domain\Model\Facility::named('Factory'),
    Tracking\Domain\Model\Facility::named('Warehouse B')
));
$commandBus->dispatch(new Tracking\Domain\Command\RegisterCargoInTheFacility(
    $cargoId3,
    Tracking\Domain\Model\Facility::named('Factory'),
    Tracking\Domain\Model\Facility::named('Warehouse B')
));

$vehicleFleetId = TraficRegulation\Domain\Model\VehicleFleetId::generate();
$commandBus->dispatch(new TraficRegulation\Domain\Command\CreateVehicleFleet(
    $vehicleFleetId,
));

$commandBus->dispatch(new TraficRegulation\Domain\Command\AddVehicle(
    $vehicleFleetId,
    'Ship',
    TraficRegulation\Domain\Model\Facility::named('Port')
));
$commandBus->dispatch(new TraficRegulation\Domain\Command\AddVehicle(
    $vehicleFleetId,
    'Truck 1',
    TraficRegulation\Domain\Model\Facility::named('Factory')
));
$commandBus->dispatch(new TraficRegulation\Domain\Command\AddVehicle(
    $vehicleFleetId,
    'Truck 2',
    TraficRegulation\Domain\Model\Facility::named('Factory')
));

$commandBus->dispatch(new TraficRegulation\Domain\Command\RepositionVehicleFleet(
    $vehicleFleetId
));
