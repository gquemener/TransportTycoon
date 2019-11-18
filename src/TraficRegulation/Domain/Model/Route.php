<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\Model;

final class Route implements Position
{
    private $destination;

    private $eta;

    private function __construct(Facility $destination, int $eta)
    {
        $this->destination = $destination;
        $this->eta = $eta;
    }

    public static function to(Facility $destination, int $eta): self
    {
        return new self($destination, $eta);
    }

    public function description(): string
    {
        return sprintf(
            'En route: %d hour(s) from "%s"',
            $this->eta,
            $this->destination->toString()
        );
    }

    public function progress(): self
    {
        if ($this->isOver()) {
            throw new \RuntimeException('The route is already over');
        }

        $self = clone $this;
        $self->destination = $this->destination;
        $self->eta = $this->eta - 1;

        return $self;
    }

    public function isOver(): bool
    {
        return 0 === $this->eta;
    }

    public function destination(): Facility
    {
        return $this->destination;
    }

    public function eta(): int
    {
        return $this->eta;
    }
}
