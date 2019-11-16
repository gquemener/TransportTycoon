<?php
declare(strict_types=1);

namespace App\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Simulation\Domain\Service\Simulator;
use App\TraficRegulation\Domain\Model\Facility;
use App\Console\LogDomainEventToConsoleDecorator;

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
        $simulator = $this->container->get(Simulator::class);
        $consoleLogger = $this->container->get(LogDomainEventToConsoleDecorator::class);
        $consoleLogger->setOutput($output);

        $cargos = array_map(function(string $id) {
            return sprintf('Warehouse %s', $id);
        }, str_split($input->getArgument('cargos')));

        $simulator->run($cargos);

        $output->writeln($simulator->loops());
    }
}
