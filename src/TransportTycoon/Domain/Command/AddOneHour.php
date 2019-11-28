<?php
declare(strict_types=1);

namespace App\TransportTycoon\Domain\Command;

use App\TransportTycoon\Domain\Message;
use App\TransportTycoon\Domain\MessageWithPayload;

final class AddOneHour implements Message
{
    use MessageWithPayload;
}
