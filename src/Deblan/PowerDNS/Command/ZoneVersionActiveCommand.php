<?php

namespace Deblan\PowerDNS\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Deblan\Console\Command\AbstractCommand;
use Deblan\PowerDNS\Model\ZoneVersionQuery;

class ZoneVersionActiveCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('zone:version:active')
            ->setDescription('Active a zone version')
            ->addArgument('zone_id', InputArgument::REQUIRED, 'Zone ID')
            ->addArgument('version', InputArgument::REQUIRED, 'Zone version');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $zoneId = (int) $this->getInput()->getArgument('zone_id');
        $version = (int) $this->getInput()->getArgument('version');

        $zoneVersionCount = ZoneVersionQuery::create()
            ->filterByZoneId($zoneId)
            ->filterByVersion($version)
            ->count();

        if (0 === $zoneVersionCount) {
            $this->getOutput()->writeln('<error>Zone version not found.</error>');

            return;
        }

        foreach (ZoneVersionQuery::create()->findByZoneId($zoneId) as $zoneVersion) {
            if ($zoneVersion->getVersion() === $version) {
                $zoneVersion->setIsActive(true);
            } else {
                $zoneVersion->setIsActive(false);
            }

            $zoneVersion->save();
        }

        $this->getOutput()->writeln('<info>Zone version activated.</info>');
    }
}
