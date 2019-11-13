<?php

use App\Tracking;
use App\TraficRegulation;
use App\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\Config\FileLocator;
use App\ServiceBus\CommandBus;

require __DIR__.'/../vendor/autoload.php';

function logMessage(string $message, ...$params): void
{
    printf('[%s] ' . $message . "\n", date('Y-m-d H:i:s'), ...$params);
}

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

$shipId = TraficRegulation\Domain\Model\VehicleId::generate();
$truckId1 = TraficRegulation\Domain\Model\VehicleId::generate();
$truckId2 = TraficRegulation\Domain\Model\VehicleId::generate();

$commandBus->dispatch(new TraficRegulation\Domain\Command\AddVehicle(
    $shipId,
    TraficRegulation\Domain\Model\Facility::named('Port')
));
$commandBus->dispatch(new TraficRegulation\Domain\Command\AddVehicle(
    $truckId1,
    TraficRegulation\Domain\Model\Facility::named('Factory')
));
$commandBus->dispatch(new TraficRegulation\Domain\Command\AddVehicle(
    $truckId2,
    TraficRegulation\Domain\Model\Facility::named('Factory')
));
