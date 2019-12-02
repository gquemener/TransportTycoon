<?php
declare(strict_types=1);

namespace App\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Console\LogDomainEventToConsoleDecorator;
use App\Console\LogDomainCommandToConsoleDecorator;
use Symfony\Component\Console\Input\InputOption;
use App\ServiceBus\CommandBus;
use App\TransportTycoon\Domain\Model\VehicleFleet;
use App\TransportTycoon\Domain\Model\Vehicle;
use App\TransportTycoon\Domain\Model\Facility;
use App\TransportTycoon\Domain\Model\Simulation;
use App\TransportTycoon\Domain\Model\Cargo;
use App\Tracer\Domain\Model\JournalRepository;
use App\ServiceBus\EventBus;
use App\Tracer\Domain\ProcessManager\AppendEntriesToJournal;
use App\TransportTycoon\Domain\Event\VehicleHasStarted;
use Symfony\Component\DependencyInjection\Reference;
use App\TransportTycoon\Domain\Event\VehicleHasParkedInFacility;

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
            ->addOption('dump-log', null, InputOption::VALUE_REQUIRED, 'The directory in which to dump the events log')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commandBus = $this->container->get(CommandBus::class);
        $simulation = new Simulation($commandBus);

        $eventLogger = $this->container->get(LogDomainEventToConsoleDecorator::class);
        $commandLogger = $this->container->get(LogDomainCommandToConsoleDecorator::class);
        $eventLogger->setOutput($output);
        $commandLogger->setOutput($output);

        $cargos = $input->getArgument('cargos');

        if (null !== $dumpLogDir = $input->getOption('dump-log')) {
            $this->container->get(JournalRepository::class)->setBaseDir($dumpLogDir);
            $eventBus = $this->container->get(EventBus::class);
            $listener = $this->container->get(AppendEntriesToJournal::class);
            $eventBus->on(VehicleHasStarted::class, $listener, 128);
            $eventBus->on(VehicleHasParkedInFacility::class, $listener, 128);
        }

        $hours = $simulation->timeToDeliver(
            ...array_map(
                function(string $cargo): Cargo{
                    switch ($cargo) {
                        case 'A':
                            return Cargo::toWarehouseA();

                        case 'B':
                            return Cargo::toWarehouseB();
                    }
                },
                str_split($cargos)
            )
        );

        $output->writeln((string) $hours);
    }
}
