<?php
declare(strict_types=1);

namespace App\TransportTycoon\Domain\Model;

use MyCLabs\Enum\Enum;

/**
 * @method static self FACTORY()
 * @method static self PORT()
 * @method static self WAREHOUSE_A()
 * @method static self WAREHOUSE_B()
 */
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
