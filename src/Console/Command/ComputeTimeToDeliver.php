<?php
declare(strict_types=1);

namespace App\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

final class ComputeTimeToDeliver extends Command
{
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
