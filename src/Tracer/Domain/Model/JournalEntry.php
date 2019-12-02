<?php
declare(strict_types=1);

namespace App\Tracer\Domain\Model;

use App\TransportTycoon\Domain\Model\Cargo;

final class JournalEntry implements \JsonSerializable
{
    private $event;
    private $time;
    private $transportId;
    private $kind;
    private $location;
    private $destination;
    private $cargos;

    private function __construct(
        string $event,
        int $time,
        int $transportId,
        string $kind,
        string $location,
        ?string $destination,
        array $cargos
    ) {
        $this->event = $event;
        $this->time = $time;
        $this->transportId = $transportId;
        $this->kind = $kind;
        $this->location = strtoupper($location);
        $this->destination = $destination ? strtoupper($destination) : null;
        $this->cargos = $cargos;
    }

    public static function depart(
        int $time,
        int $transportId,
        string $kind,
        string $location,
        string $destination,
        array $cargos
    ): self {
        return new self('DEPART', $time, $transportId, $kind, $location, $destination, $cargos);
    }

    public static function arrive(
        int $time,
        int $transportId,
        string $kind,
        string $location,
        array $cargos
    ): self {
        return new self('ARRIVE', $time, $transportId, $kind, $location, null, $cargos);
    }

    public function jsonSerialize(): array
    {
        $data = [
            'event' => $this->event,
            'time' => $this->time,
            'transport_id' => $this->transportId,
            'kind' => $this->kind,
            'location' => $this->location,
        ];

        if (null !== $this->destination) {
            $data['destination'] = $this->destination;
        }

        if (!empty($this->cargos)) {
            $data['cargo'] = array_map(function (Cargo $cargo): array {
                return [
                    'cargo_id' => $cargo->id(),
                    'origin' => 'FACTORY',
                    'destination' => $cargo->destination()->toString(),
                ];
            }, $this->cargos);
        }

        return $data;
    }
}
