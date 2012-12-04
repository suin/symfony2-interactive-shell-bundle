<?php

namespace Suin\InteractiveShellBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\DialogHelper;

class ShellCommand extends ContainerAwareCommand
{
    private static $quitCommands = array('quit');

    public function configure()
    {
        $this->setName('suin:shell')
            ->setDescription('Symfony2 interactive shell');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var $dialog DialogHelper */
        $dialog = $this->getHelperSet()->get('dialog');

        while ( true ) {
            $command = $dialog->ask($output, 'symfony > ');
            try {
                if ( $command ) {
                    $this->_sandbox($command);
                }
            } catch (\Exception $e) {
                $output->writeln('<error>'.$e.'</error>');
            }
            if ( in_array($command, self::$quitCommands) ) {
                goto quit;
            }
        }

        quit:
    }

    private function _sandbox($command)
    {
        $container = $this->getContainer();
        call_user_func(function()use($command, $container){
            eval($command);
        });
    }
}
