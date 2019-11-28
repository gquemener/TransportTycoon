<?php
declare(strict_types=1);

namespace App\TransportTycoon\Domain\Model;

interface Position
{
    public function toString(): string;

    public function equals(Position $position): bool;
}
