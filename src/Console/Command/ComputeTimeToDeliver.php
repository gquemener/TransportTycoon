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
use App\Console\LogDomainCommandToConsoleDecorator;
use App\Debug\Listener\LogDomainEvents;
use Symfony\Component\Console\Input\InputOption;

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
            ->addOption('debug', null, InputOption::VALUE_REQUIRED, 'The directory in which to dump the events log')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $simulator = $this->container->get(Simulator::class);
        $eventLogger = $this->container->get(LogDomainEventToConsoleDecorator::class);
        $commandLogger = $this->container->get(LogDomainCommandToConsoleDecorator::class);
        $eventLogger->setOutput($output);
        $commandLogger->setOutput($output);

$cargos = $input->getArgument('cargos');
        if ($input->hasOption('debug')) {
            $this->container->get(LogDomainEvents::class)->setLogFile(
                sprintf('%s/%s/%s.log', getcwd(), $input->getOption('debug'), $cargos)
            );
        }

        $loops = $simulator->run(array_map(function(string $id) {
            return sprintf('Warehouse %s', $id);
        }, str_split($cargos)));

        $output->writeln($loops);
    }
}
