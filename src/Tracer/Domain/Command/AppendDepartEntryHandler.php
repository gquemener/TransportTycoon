<?php
declare(strict_types=1);

namespace App\Tracer\Domain\Command;

use App\Tracer\Domain\Model\JournalRepository;

final class AppendDepartEntryHandler
{
    private $repository;

    public function __construct(JournalRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(AppendDepartEntry $command): void
    {
        $journalId = $command->aggregateRoot();
        $journal = $this->repository->find($journalId);

        $journal->appendDepartEntry(
            $command->time(),
            $command->transportId(),
            $command->kind(),
            $command->location(),
            $command->destination(),
            $command->cargos()
        );

        $this->repository->persist($journal);
    }
}
