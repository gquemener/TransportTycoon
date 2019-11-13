<?php

require __DIR__.'/../vendor/autoload.php';

use App\Transportation\Domain\Model\Facility;
use App\Transportation\Domain\Model\Truck;
use App\Transportation\Domain\Model\Cargo;
use App\Transportation\Domain\Service\ItineraryCalculator;

$factory = Facility::named('Factory');
$port = Facility::named('Port');
$warehouseA = Facility::named('Warehouse A');
$warehouseB = Facility::named('Warehouse B');

$factory->addRoute($warehouseA, 5);
$factory->addRoute($port, 4);
$port->addRoute($warehouseB, 1);

$cargos = [
    Cargo::shipTo($warehouseA),
    Cargo::shipTo($warehouseB),
    Cargo::shipTo($warehouseB),
];

$factory->storeCargos($cargos);
$factory->checkIn(new Truck());
$factory->checkIn(new Truck());

$calculator = new ItineraryCalculator();

// loop
for ($i = 1; $i < 8; $i++) {
    logMessage('Starting hour %d', $i);
    foreach ($cargos as $key => $cargo) {
        logMessage('Handling cargo #%d', $key);
        if ($cargo->isInFacility()) {
            $cargo->load($calculator);
        }

        if ($cargo->isEnRoute()) {
            $cargo->progressRoute();
        }

        if ($cargo->hasReachedDestination()) {
            unset($cargos[$key]);
        }
    }
    logMessage('Ending hour %d', $i);
    logMessage('--------------');
}

function logMessage(string $message, ...$params): void
{
    printf($message . "\n", ...$params);
}
