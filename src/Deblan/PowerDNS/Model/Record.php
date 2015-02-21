<?php

namespace Deblan\PowerDNS\Model;

use Deblan\PowerDNS\Model\Base\Record as BaseRecord;

class Record extends BaseRecord
{
    public function hydrateFromZoneRecord(ZoneRecord $zoneRecord, Domain $domain)
    {
        $name = ltrim(sprintf(
            '%s.%s',
            str_replace('@', '', $zoneRecord->getName()),
            ltrim($domain->getName(), '.')
        ), '.');

        $this
            ->setDomain($domain)
            ->setName($name)
            ->setType($zoneRecord->getType())
            ->setContent($zoneRecord->getContent())
            ->setTtl($zoneRecord->getTtl())
            ->setPrio($zoneRecord->getPrio())
            ->setChangeDate(time());
    }
}
