<?php
declare(strict_types=1);

namespace App\TransportTycoon\Domain\Model;

final class PendingCargos
{
    private $cargos;

    private function __construct(array $cargos)
    {
        $this->cargos = $cargos;
    }

    public static function withCargos(Cargo ...$cargos): self
    {
        return new self($cargos);
    }

    public function add(Cargo $cargo): self
    {
        $self = clone $this;
        $self->cargos[] = $cargo;

        return $self;
    }

    public function remove(array $cargos): self
    {
        $self = clone $this;
        foreach ($cargos as $cargo) {
            foreach ($self->cargos as $k => $v) {
                if ($v->equals($cargo)) {
                    unset($self->cargos[$k]);
                    break;
                }
            }
        }

        return $self;
    }

    public function inFacility(Facility $facility): array
    {
        return array_filter($this->cargos, function(Cargo $cargo) use ($facility) {
            return $cargo->position()->equals($facility);
        });
    }
}
