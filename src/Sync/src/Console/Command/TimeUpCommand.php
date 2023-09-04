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
class TimeUpCommand extends Command
{
    /**
     * Конфигурация команды
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('time-up')
            ->setDescription('Show current date time');
    }

    /**
     * Инициализауия воркера, создание задачи и добавление ее в очередь
     *
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $worker = new TimeWorker();
        $worker->execute();

        $this->getApplication()->setAutoExit(false);
    }

}