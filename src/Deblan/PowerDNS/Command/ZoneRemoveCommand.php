<?php

namespace Deblan\PowerDNS\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Command\Command;
use Deblan\Console\Command\AbstractCommand;
use Deblan\PowerDNS\Model\ZoneQuery;
use Symfony\Component\Console\Input\InputArgument;

class ZoneRemoveCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('zone:remove')
            ->setDescription('Remove a zone')
            ->addArgument('zone_id', InputArgument::REQUIRED, 'Zone ID')
            ->addOption('confirm', null, InputOption::VALUE_NONE, 'Confirmation')
            ->setHelp("<info>%command.name%</info>

<comment>By removing a zone, you will remove all associated versions and records!</comment>");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $id = $this->getInput()->getArgument('zone_id');

        $zone = ZoneQuery::create()->findOneById($id);

        if (null === $zone) {
            $this->getOutput()->writeln('<error>Zone not found.</error>');

            return;
        }

        $confirm = $this->getInput()->getOption('confirm');

        if (false === $confirm) {
            $this->getOutput()->writeln('<comment>By removing this zone, you will remove all associated versions and records!</comment>');

            $confirm = $this->getHelper('dialog')->askConfirmation($this->getOutput(), '<info>Do you confirm? [<comment>no</comment>] ', false);
        }

        if ($confirm) {
            $zone->delete();

            $this->getOutput()->writeln('<info>Zone removed.</info>');
        } else {
            $this->getOutput()->writeln('<info>Aborted.</info>');
        }
    }
}
