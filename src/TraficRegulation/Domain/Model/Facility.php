<?php
declare(strict_types=1);

namespace App\TraficRegulation\Domain\Model;

final class Facility implements Position
{
    private $name;

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function named(string $name): self
    {
        return new self($name);
    }

    public function description(): string
    {
        return sprintf('In Facility: %s', $this->name);
    }

    public function toString(): string
    {
        return $this->name;
    }
}
