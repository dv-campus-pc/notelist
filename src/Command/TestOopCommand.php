<?php

namespace App\Command;

use App\Model\Machine\Bmw;
use App\Model\Machine\Car;
use App\Model\Machine\Machine;
use App\Model\Machine\Mercedes;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TestOopCommand extends Command
{
    protected static $defaultName = 'app:test:oop';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $mercedes = new Mercedes();
        $bmw = new Bmw();
        $bmw->move('test', 1);

        $this->someFunction($mercedes);
        $this->someFunction($bmw);

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }

    public function someFunction(Machine $machine): void {
        $machine->move(1, 1);
        $machine->parkingNearDepot();
    }

    public function someFunction2(Car $car): void {
        $car->parkingNearDepot();
    }
}
