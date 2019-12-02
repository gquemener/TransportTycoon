<?php
declare(strict_types=1);

namespace App\Tracer\Infrastructure;

use App\Tracer\Domain\Model\JournalRepository;
use App\Tracer\Domain\Model\Journal;
use App\Tracer\Domain\Model\JournalId;

final class FilesystemJournalRepository implements JournalRepository
{
    private $baseDir;
    private $journals;

    public function setBaseDir(string $baseDir)
    {
        $this->baseDir = $baseDir;
    }

    public function find(JournalId $id): ?Journal
    {
        if (!isset($this->journals[$id->toString()])) {
            $this->journals[$id->toString()] = Journal::create($id);
        }

        return $this->journals[$id->toString()];
    }

    public function persist(Journal $journal): void
    {
        if (null === $this->baseDir) {
            throw new \RuntimeException('Base directory has not been set');
        }

        $path = $this->baseDir . DIRECTORY_SEPARATOR . $journal->id()->toString() . '.log';
        if (is_file($path)) {
            unlink($path);
        }

        $resource = fopen($path, 'a');

        foreach ($journal->entries() as $entry) {
            fwrite($resource, json_encode($entry) . "\n");
        }

        fclose($resource);
    }
}
