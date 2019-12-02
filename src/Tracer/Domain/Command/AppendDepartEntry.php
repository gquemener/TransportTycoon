<?php
declare(strict_types=1);

namespace App\Tracer\Domain\Command;

use App\TransportTycoon\Domain\Message;
use App\TransportTycoon\Domain\MessageWithPayload;

final class AppendDepartEntry implements Message, \JsonSerializable
{
    use MessageWithPayload;

    public function time(): int
    {
        return $this->payload()['time'];
    }

    public function transportId(): int
    {
        return $this->payload()['transportId'];
    }

    public function kind(): string
    {
        return $this->payload()['kind'];
    }

    public function location(): string
    {
        return $this->payload()['location'];
    }

    public function destination(): string
    {
        return $this->payload()['destination'];
    }

    public function cargos(): array
    {
        return $this->payload()['cargos'];
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->aggregateRoot()->toString(),
            'entry' => $this->payload(),
        ];
    }
}
