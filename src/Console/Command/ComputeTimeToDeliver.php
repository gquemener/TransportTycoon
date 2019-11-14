<?php
declare(strict_types=1);

namespace App\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class ComputeTimeToDeliver extends Command
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();

        $this->container = $container;
    }

    protected function configure()
    {
        $this
            ->setName('time-to-deliver')
            ->setDescription('Calculate the time required to deliver a list of cargos')
            ->addArgument('cargos', InputArgument::REQUIRED, 'The list of cargos (ex: AAABBBABBAAA)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }
}
