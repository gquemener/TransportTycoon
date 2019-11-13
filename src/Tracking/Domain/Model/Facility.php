<?php
declare(strict_types=1);

namespace App\Tracking\Domain\Model;

final class Facility
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

    public function toString(): string
    {
        return $this->name;
    }

    public function equals(Facility $facility): bool
    {
        return get_class($facility) === get_class($this)
            && $facility->name === $this->name;
    }
}
