<?php 

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

require '../vendor/autoload.php';

#[AsCommand(name: 'test')]
class Commando extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $i = 0;
        $progressBar = new ProgressBar($output,6);
        $progressBar->setFormat(
            "%message% \n%current%/%max% [%bar%] %percent:3s%%" 
        );
        $progressBar->setMessage('Stating up...','message');
        $progressBar->advance();
        while($i<5) {
            $progressBar->setMessage('getting repository', 'message'); // set the `item` value
            $progressBar->advance();
            $i++;
            sleep(1);
        }
        $progressBar->finish();
        return Command::SUCCESS;
    }
}

$application = new Application();
$application->add(new Commando());
$application->run();