<?php
declare(strict_types=1);

namespace App\Tracking\Domain\Model;

use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid;

final class CargoId
{
    private $uuid;

    public function __construct(UuidInterface $uuid)
    {
        $this->uuid = $uuid;
    }

    public static function generate(): self
    {
        return new self(Uuid::uuid4());
    }

    public static function fromString(string $todoId): self
    {
        return new self(Uuid::fromString($todoId));
    }

    public function toString(): string
    {
        return $this->uuid->toString();
    }

    public function equals(CargoId $cargoId): bool
    {
        return get_class($cargoId) === get_class($this)
            && $cargoId->uuid->equals($this->uuid);
    }
}
