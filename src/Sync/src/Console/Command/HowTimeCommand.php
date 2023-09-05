<?php

namespace Sync\src\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Sync\src\Console\Producers\TimeProducer;
use Sync\src\Console\Workers\TimeWorker;

/**
 * Class HowTimeCommand
 *
 * @package Sync\src\Console\Command;
 */
class HowTimeCommand extends Command
{
    /**
     * Конфигурация команды
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('how-time')
            ->setDescription('Show current date time');
    }

    /**
     * Инициализауия продюссера, создание задачи и добавление ее в очередь
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $time = date('H:i (m.Y)');
        $producer = new TimeProducer();
        $producer->produce($time);

        $this->getApplication()->setAutoExit(false);

        return 0;
    }
}