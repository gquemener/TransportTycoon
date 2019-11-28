<?php
declare(strict_types=1);

namespace App\TransportTycoon\Domain\Event;

use App\TransportTycoon\Domain\Message;
use App\TransportTycoon\Domain\MessageWithPayload;
use App\TransportTycoon\Domain\Model\Vehicle;
use App\TransportTycoon\Domain\Model\Cargo;

final class VehicleWasLoaded implements Message, \JsonSerializable
{
    use MessageWithPayload;

    public function vehicle(): Vehicle
    {
        return $this->payload()['vehicle'];
    }

    public function cargos(): array
    {
        return $this->payload()['cargos'];
    }

    public function jsonSerialize(): array
    {
        return [
            'time' => $this->aggregateRoot()->age(),
            'vehicle' => $this->vehicle()->toString(),
            'cargos' => array_map(function(Cargo $cargo) {
                return $cargo->toString();
            }, $this->cargos()),
        ];
    }
}
