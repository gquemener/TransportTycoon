<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\Model;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class VehicleFleetId
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
}
