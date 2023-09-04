<?php

namespace Sync\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function sprintf;

class HowTimeCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('how-time')
            ->setDescription('Show current date time');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $message = $input->getArgument('message');
        $output->writeln(sprintf('<info>Hello to world: %s<info>! ', $message));

        $this->getApplication()->setAutoExit(false);
        return 0;
    }
}