<?php
declare(strict_types=1);

namespace App\Tracking\Domain\Command;

use App\Tracking\Domain\Model\CargoId;
use App\Tracking\Domain\Model\Facility;

final class UnloadCargo implements \JsonSerializable
{
    private $cargoId;
    private $facility;

    public function __construct(CargoId $cargoId, Facility $facility)
    {
        $this->cargoId = $cargoId->toString();
        $this->facility = $facility->toString();
    }

    public function cargoId(): CargoId
    {
        return CargoId::fromString($this->cargoId);
    }

    public function facility(): Facility
    {
        return Facility::named($this->facility);
    }

    public function jsonSerialize(): array
    {
        return [
            'cargoId' => $this->cargoId,
            'facility' => $this->facility,
        ];
    }
}
