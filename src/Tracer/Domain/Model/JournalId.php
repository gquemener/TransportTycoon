<?php
declare(strict_types=1);

namespace App\Tracer\Domain\Model;

final class JournalId
{
    private $id;

    private function __construct(string $id)
    {
        $this->id = $id;
    }

    public static function fromString(string $id): self
    {
        if (0 === preg_match('/^[AB]+$/', $id)) {
            throw new \InvalidArgumentException('Journal id can only consist of sequence of "A" or "B" characters');
        }

        return new self($id);
    }

    public function toString(): string
    {
        return $this->id;
    }
}
