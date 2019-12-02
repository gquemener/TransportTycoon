<?php
declare(strict_types=1);

namespace App\TransportTycoon\Domain\Model;

final class UnloadingArea implements Position
{
    private $facility;
    private $eta;
    private $cargos;

    private function __construct(
        Facility $facility,
        int $eta,
        array $cargos
    ) {
        $this->facility = $facility;
        $this->eta = $eta;
        $this->cargos = $cargos;
    }

    public static function atFacility(
        Facility $facility,
        int $eta,
        array $cargos
    ): self {
        return new self($facility, $eta, $cargos);
    }

    public function process(): void
    {
        --$this->eta;
    }

    public function isFinished(): bool
    {
        return 0 === $this->eta;
    }

    public function facility(): Facility
    {
        return $this->facility;
    }

    public function cargos(): array
    {
        return $this->cargos;
    }

    public function toString(): string
    {
        return sprintf(
            'Loading area of "%s" (ETA: %d hours)',
            $this->facility->toString(),
            $this->eta
        );
    }

    public function equals(Position $position): bool
    {
        return $position instanceof self
            && $position->facility->equals($this->facility)
            && $position->eta === $this->eta;
    }
}
