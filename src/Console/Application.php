<?php
declare(strict_types=1);

namespace App\Console;

use Symfony\Component\Console\Application as BaseApplication;
use App\Console\Command\ComputeTimeToDeliver;

final class Application extends BaseApplication
{
    public function __construct(string $version)
    {
        parent::__construct('Transport Tycoon', $version);

        $this->add(new ComputeTimeToDeliver());
    }
}
