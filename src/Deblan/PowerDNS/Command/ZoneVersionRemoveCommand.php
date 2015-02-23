<?php

namespace Deblan\PowerDNS\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Deblan\Console\Command\AbstractCommand;
use Deblan\PowerDNS\Model\ZoneVersionQuery;
use Symfony\Component\Console\Input\InputOption;

class ZoneVersionRemoveCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('zone:version:remove')
            ->setDescription('Remove an unactivated zone version')
            ->addArgument('zone_id', InputArgument::REQUIRED, 'Zone ID')
            ->addArgument('version', InputArgument::REQUIRED, 'Zone version')
            ->addOption('confirm', null, InputOption::VALUE_NONE, '');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $zoneId = (int) $this->getInput()->getArgument('zone_id');
        $version = (int) $this->getInput()->getArgument('version');

        $zoneVersion = ZoneVersionQuery::create()
            ->filterByZoneId($zoneId)
            ->filterByVersion($version)
            ->findOne();

        if (null === $zoneVersion) {
            $this->getOutput()->writeln('<error>Zone version not found.</error>');

            return;
        }

        if ($zoneVersion->getIsActive()) {
            $this->getOutput()->writeln('<error>You can not remove an activated zone version.</error>');

            return;
        }

        $confirm = $this->getInput()->getOption('confirm');

        if (false === $confirm) {
            $confirm = $this->getHelper('dialog')->askConfirmation($this->getOutput(), '<info>Do you confirm? [<comment>no</comment>] ', false);
        }

        if ($confirm) {
            $zoneVersion->delete();

            $this->getOutput()->writeln('<info>Zone version removed.</info>');
        } else {
            $this->getOutput()->writeln('<info>Aborted.</info>');
        }
    }
}
