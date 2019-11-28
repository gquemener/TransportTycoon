<?php
declare(strict_types=1);

namespace App\TransportTycoon\Domain\Event;

use App\TransportTycoon\Domain\Message;
use App\TransportTycoon\Domain\MessageWithPayload;
use App\TransportTycoon\Domain\Model\Vehicle;
use App\TransportTycoon\Domain\Model\Facility;

final class VehicleHasParkedInFacility implements Message, \JsonSerializable
{
    use MessageWithPayload;

    public function vehicle(): Vehicle
    {
        return $this->payload()['vehicle'];
    }

    public function jsonSerialize(): array
    {
        return [
            'time' => $this->aggregateRoot()->age(),
            'vehicle' => $this->vehicle()->toString(),
        ];
    }
}
