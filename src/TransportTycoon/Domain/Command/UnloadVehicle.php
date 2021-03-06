<?php
declare(strict_types=1);

namespace App\TransportTycoon\Domain\Command;

use App\TransportTycoon\Domain\Message;
use App\TransportTycoon\Domain\MessageWithPayload;
use App\TransportTycoon\Domain\Model\Vehicle;
use App\TransportTycoon\Domain\Model\Facility;

final class UnloadVehicle implements Message, \JsonSerializable
{
    use MessageWithPayload;

    public function vehicle(): Vehicle
    {
        return $this->payload()['vehicle'];
    }

    public function jsonSerialize(): array
    {
        return [
            'vehicle' => $this->vehicle()->toString(),
        ];
    }
}
