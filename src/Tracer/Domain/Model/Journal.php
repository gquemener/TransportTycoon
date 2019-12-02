<?php
declare(strict_types=1);

namespace App\Tracer\Domain\Model;

final class Journal
{
    private $journalId;
    private $entries;

    private function __construct(JournalId $journalId)
    {
        $this->journalId = $journalId;
    }

    public static function create(JournalId $journalId): self
    {
        return new self($journalId);
    }

    public function id(): JournalId
    {
        return $this->journalId;
    }

    public function entries(): array
    {
        return $this->entries;
    }

    public function appendDepartEntry(
        int $time,
        int $transportId,
        string $kind,
        string $location,
        string $destination,
        array $cargos
    ): void {
        $this->entries[] = JournalEntry::depart($time, $transportId, $kind, $location, $destination, $cargos);
    }

    public function appendArriveEntry(
        int $time,
        int $transportId,
        string $kind,
        string $location,
        array $cargos
    ): void {
        $this->entries[] = JournalEntry::arrive($time, $transportId, $kind, $location, $cargos);
    }
}
