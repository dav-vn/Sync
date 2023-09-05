<?php

namespace Sync\src\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sync\src\Console\Producers\VerifyTokenProducer;

/**
 * Class UpdateTokenCommand
 *
 * @package Sync\src\Console\Command;
 */
class UpdateTokenCommand extends Command
{
    /**
     * Конфигурация команды
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('update-token')
            ->setDescription('Update expired token')
            ->addOption(
                'time',
                't'
            )->addArgument('hours');
    }

    /**
     * Инициализауия воркера, создание задачи и добавление ее в очередь
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $requiredTime = $input->getArgument('hours');
        $producer = new VerifyTokenProducer();
        $producer->produce($requiredTime);

        return 0;
    }
}