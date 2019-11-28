<?php
declare(strict_types=1);

namespace App\TransportTycoon\Domain\Model;

final class VehicleLoad
{
    private $cargos;

    private function __construct(array $cargos)
    {
        $this->cargos = $cargos;
    }

    public static function fromCargos(Cargo ...$cargos): self
    {
        return new self($cargos);
    }

    public function cargos(): array
    {
        return $this->cargos;
    }
}
