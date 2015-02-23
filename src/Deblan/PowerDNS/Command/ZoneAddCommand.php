<?php

namespace Deblan\PowerDNS\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Command\Command;
use Deblan\Console\Command\AbstractCommand;
use Deblan\PowerDNS\Model\Zone;

class ZoneAddCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('zone:add')
            ->setDescription('Add a zone')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'Zone name')
            ->addOption('description', null, InputOption::VALUE_REQUIRED, 'Zone description');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $name = $this->getInput()->getOption('name');
        $description = $this->getInput()->getOption('description');

        while (null === $name || trim($name) === '') {
            $name = $this->getHelper('dialog')->ask($this->getOutput(), 'Name: ', null);
        }

        if (null === $description || trim($description) === '') {
            $description = $this->getHelper('dialog')->ask($this->getOutput(), 'Description: ', null);
        }

        $zone = (new Zone())
            ->setName($name)
            ->setDescription($description)
            ->save();

        $this->getOutput()->writeln('<info>Zone added.</info>');
    }
}
