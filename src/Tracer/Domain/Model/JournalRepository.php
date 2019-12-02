<?php
declare(strict_types=1);

namespace App\Tracer\Domain\Model;

interface JournalRepository
{
    public function find(JournalId $id): ?Journal;

    public function persist(Journal $journal): void;
}
