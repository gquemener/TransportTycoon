<?php
declare(strict_types=1);

namespace App\TransportTycoon\Domain\Model;

final class EnRoute implements Position
{
    private $destination;
    private $eta;

    private function __construct(Facility $destination, int $eta)
    {
        $this->destination = $destination;
        $this->eta = $eta;
    }

    public static function fromRoute(Route $route): self
    {
        return new self(
            $route->destination(),
            $route->eta()
        );
    }

    public function moveForward(): void
    {
        if ($this->hasReachedDestination()) {
            throw new \LogicException('Destination has already been reached');
        }

        --$this->eta;
    }

    public function hasReachedDestination(): bool
    {
        return 0 === $this->eta;
    }

    public function destination(): Facility
    {
        return $this->destination;
    }

    public function toString(): string
    {
        return sprintf(
            'En route to "%s". ETA: %d hours',
            $this->destination->toString(),
            $this->eta
        );
    }

    public function equals(Position $position): bool
    {
        return $position instanceof $this
            && $position->facility->equals($this->facility)
            && $position->eta === $this->eta;
    }
}
