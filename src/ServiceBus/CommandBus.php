<?php
declare(strict_types=1);

namespace App\ServiceBus;

interface CommandBus
{
    public function dispatch(object $command): void;
}
