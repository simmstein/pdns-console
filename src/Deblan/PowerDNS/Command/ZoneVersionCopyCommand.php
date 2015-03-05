<?php

namespace Deblan\PowerDNS\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Deblan\Console\Command\AbstractCommand;
use Deblan\PowerDNS\Model\ZoneVersionQuery;
use Propel\Runtime\ActiveQuery\Criteria;

class ZoneVersionCopyCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('zone:version:copy')
            ->setDescription('Copy a zone version')
            ->addArgument('zone_id', InputArgument::REQUIRED, 'Zone ID')
            ->addArgument('version', InputArgument::REQUIRED, 'Zone version');
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

        $zoneVersionCopy = $zoneVersion->copy();

        $zoneVersionCopy
            ->setVersion(ZoneVersionQuery::create()->orderByVersion(Criteria::DESC)->findOne()->getVersion() + 1)
            ->setIsActive(false);

        foreach ($zoneVersion->getZoneRecords() as $record) {
            $recordCopy = $record->copy();
            $recordCopy->save();

            $zoneVersionCopy->addZoneRecord($recordCopy);
        }

        $zoneVersionCopy->save();

        $this->getOutput()->writeln('<info>Zone version copied.</info>');
    }
}
