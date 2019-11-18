<?php
declare(strict_types=1);

namespace App\Tracking\Domain\Command;

use App\Tracking\Domain\Model\CargoId;
use App\Tracking\Domain\Model\Facility;

final class RegisterCargoInTheFacility implements \JsonSerializable
{
    private $cargoId;
    private $origin;
    private $destination;

    public function __construct(
        CargoId $cargoId,
        Facility $origin,
        Facility $destination
    ) {
        $this->cargoId = $cargoId->toString();
        $this->origin = $origin->toString();
        $this->destination = $destination->toString();
    }

    public function cargoId(): CargoId
    {
        return CargoId::fromString($this->cargoId);
    }

    public function origin(): Facility
    {
        return Facility::named($this->origin);
    }

    public function destination(): Facility
    {
        return Facility::named($this->destination);
    }

    public function jsonSerialize(): array
    {
        return [
            'cargoId' => $this->cargoId,
            'origin' => $this->origin,
            'destination' => $this->destination,
        ];
    }
}
