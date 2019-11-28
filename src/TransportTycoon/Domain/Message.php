<?php
declare(strict_types=1);

namespace App\TransportTycoon\Domain;

interface Message
{
    public function aggregateRoot(): ?object;

    public function payload(): array;
}
