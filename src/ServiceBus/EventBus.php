<?php
declare(strict_types=1);

namespace App\ServiceBus;

interface EventBus
{
    public function dispatch(object $event): void;
}
