<?php

namespace Deblan\PowerDNS\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Deblan\Console\Command\AbstractCommand;
use Deblan\PowerDNS\Model\ZoneVersionQuery;

class ZoneVersionUnactiveCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('zone:version:unactive')
            ->setDescription('Unactive a zone version')
            ->addArgument('zone_id', InputArgument::REQUIRED, 'ZONE_ID')
            ->addArgument('version', InputArgument::REQUIRED, 'VERSION')
            ->setHelp("The <info>%command.name%</info> ");
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

        $zoneVersion->setIsActive(false)->save();

        $this->getOutput()->writeln('<info>Zone version unactivated.</info>');
    }
}
