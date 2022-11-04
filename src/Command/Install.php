<?php 

namespace Kenjiefx\Forte\Command;

use Kenjiefx\Forte\Installer\Installer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'install')]
class Install extends Command
{
    protected static $defaultDescription = 'Install dependency libraries';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $installer = new Installer($output);
        $installer->install();
        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this->setHelp('This command allows you install libraries.');
    }
}