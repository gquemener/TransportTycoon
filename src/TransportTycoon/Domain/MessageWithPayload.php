<?php
declare(strict_types=1);

namespace App\TransportTycoon\Domain;

trait MessageWithPayload
{
    private $aggregateRoot;
    private $payload;

    public function __construct(
        ?object $aggregateRoot,
        array $payload = []
    ) {
        $this->aggregateRoot = $aggregateRoot;
        $this->payload = $payload;
    }

    public function aggregateRoot(): ?object
    {
        return $this->aggregateRoot;
    }

    public function payload(): array
    {
        return $this->payload;
    }
}
