<?php

namespace Deblan\PowerDNS\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Propel\Runtime\ActiveQuery\Criteria;
use Deblan\PowerDNS\Model\RecordQuery;
use Deblan\PowerDNS\Model\DomainQuery;
use Deblan\PowerDNS\Model\Record;

class ZonePushCommand extends AbstractZoneCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('zone:push')
            ->setDescription('Push activated zones to PowerDNS tables')
            ->setHelp("<info>%command.name%</info>

After editing zone versions, you have to push all modifications by using <info>%command.name%</info>.

The PDNS records will be replaced with your activated zones versions.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $domains = DomainQuery::create()->filterByZoneId(null, Criteria::NOT_EQUAL)->find();

        foreach ($domains as $domain) {
            RecordQuery::create()->filterByDomain($domain)->find()->delete();
            $zoneVersion = $domain->getZone()->getActiveZoneVersion();

            if ($zoneVersion === null) {
                continue;
            }

            foreach ($zoneVersion->getZoneRecords() as $zoneRecord) {
                $record = new Record();
                $record->hydrateFromZoneRecord($zoneRecord, $domain);
                $record->save();
            }
        }
    }
}
