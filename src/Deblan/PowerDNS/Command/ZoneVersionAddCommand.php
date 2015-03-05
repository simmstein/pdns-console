<?php

namespace Deblan\PowerDNS\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Deblan\Console\Command\AbstractCommand;
use Deblan\PowerDNS\Model\ZoneVersionQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Deblan\PowerDNS\Model\ZoneQuery;
use Deblan\PowerDNS\Model\ZoneVersion;

class ZoneVersionAddCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('zone:version:add')
            ->setDescription('Add a zone version')
            ->addArgument('zone_id', InputArgument::REQUIRED, 'Zone ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $zoneId = (int) $this->getInput()->getArgument('zone_id');

        $zone = ZoneQuery::create()->findOneById($zoneId);

        if (null === $zone) {
            $this->getOutput()->writeln('<error>Zone not found.</error>');

            return;
        }

        (new ZoneVersion())
            ->setVersion($zone->countZoneVersions() ? ZoneVersionQuery::create()->orderByVersion(Criteria::DESC)->findOne()->getVersion() + 1 : 1)
            ->setZone($zone)
            ->setIsActive(false)
            ->save();

        $this->getOutput()->writeln('<info>Zone version added.</info>');
    }
}
