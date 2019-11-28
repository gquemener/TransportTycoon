<?php
declare(strict_types=1);

namespace App\TransportTycoon\Domain\Model;

use MyCLabs\Enum\Enum;

final class FacilityName extends Enum
{
    private const FACTORY = 'Factory';
    private const PORT = 'Port';
    private const WAREHOUSE_A = 'Warehouse A';
    private const WAREHOUSE_B = 'Warehouse B';

    public function toString(): string
    {
        return $this->__toString();
    }
}
