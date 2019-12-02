<?php
declare(strict_types=1);

namespace App\TransportTycoon\Domain\Model;

final class EnRoute implements Position
{
    private $origin;
    private $destination;
    private $eta;

    private function __construct(
        Facility $origin,
        Facility $destination,
        int $eta
    ) {
        $this->origin = $origin;
        $this->destination = $destination;
        $this->eta = $eta;
    }

    public static function fromRoute(Route $route): self
    {
        return new self(
            $route->origin(),
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

    public function origin(): Facility
    {
        return $this->origin;
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
        return $position instanceof self
            && $position->origin->equals($this->origin)
            && $position->destination->equals($this->destination)
            && $position->eta === $this->eta;
    }
}
