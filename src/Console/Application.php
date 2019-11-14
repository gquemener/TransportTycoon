<?php
declare(strict_types=1);

namespace App\Console;

use Symfony\Component\Console\Application as BaseApplication;
use App\Console\Command\ComputeTimeToDeliver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\Config\FileLocator;

final class Application extends BaseApplication
{
    public function __construct(string $version)
    {
        parent::__construct('Transport Tycoon', $version);

        $containerBuilder = new ContainerBuilder();
        $loader = new PhpFileLoader($containerBuilder, new FileLocator(__DIR__.'/../..'));
        $loader->load('services.php');
        $containerBuilder->compile();

        $this->add(new ComputeTimeToDeliver($containerBuilder));
    }
}
