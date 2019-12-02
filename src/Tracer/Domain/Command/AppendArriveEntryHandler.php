<?php
declare(strict_types=1);

namespace App\Tracer\Domain\Command;

use App\Tracer\Domain\Model\JournalRepository;

final class AppendArriveEntryHandler
{
    private $repository;

    public function __construct(JournalRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(AppendArriveEntry $command): void
    {
        /** @var \App\Tracer\Domain\Model\JournalId */
        $journalId = $command->aggregateRoot();
        $journal = $this->repository->find($journalId);

        $journal->appendArriveEntry(
            $command->time(),
            $command->transportId(),
            $command->kind(),
            $command->location(),
            $command->cargos()
        );

        $this->repository->persist($journal);
    }
}
