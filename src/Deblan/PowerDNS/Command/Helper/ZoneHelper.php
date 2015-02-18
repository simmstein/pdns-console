<?php

namespace Deblan\PowerDNS\Command\Helper;

use Deblan\Console\Command\Helper\AbstractHelper;
use Deblan\PowerDNS\Model\Base\Zone;
use Deblan\PowerDNS\Model\Base\ZoneVersion;

class ZoneHelper extends AbstractHelper
{
    public function showZone(Zone $zone, $key = 0)
    {
        $this->getOutput()->writeln(sprintf('<info>%s</info>.', $zone->getName()));

        if ($zone->getDescription()) {
            $this->getOutput()->writeln($zone->getDescription());
        }

        foreach ($zone->getZoneVersions() as $key => $zoneVersion) {
            $this->showZoneVersion($zoneVersion, $key);
        }
    }

    public function showZoneVersion(ZoneVersion $zoneVersion, $key = 0)
    {
        if ($this->getInput()->getOption('active') && false === $zoneVersion->getIsActive()) {
            return;
        }

        if ($this->getInput()->getOption('no-active') && true === $zoneVersion->getIsActive()) {
            return;
        }

        $this->getOutput()->writeln('');
        $this->getOutput()->writeln(sprintf(
            '<info>Version</info>: <comment>%d</comment> - <info>Active</info>: %s',
            $zoneVersion->getVersion(),
            $zoneVersion->getIsActive() ? 'Yes' : 'No'
        ));

        $this->showZoneVersionRecords($zoneVersion);
    }

    public function showZoneVersionRecords(ZoneVersion $zoneVersion)
    {
        $this->getOutput()->writeln('');
        $this->getOutput()->writeln('<comment>   ID | NAME                  | TYPE      | TTL    | PRIO    | CONTENT</comment>');
        $this->getOutput()->writeln('<comment>----------------------------------------------------------------------</comment>');

        foreach ($zoneVersion->getZoneRecords() as $zoneRecord) {
            $this->getOutput()->writeln(sprintf(
                '%5d | %s | %s | %s | %s | %s',
                $zoneRecord->getId(),
                str_pad($zoneRecord->getName(), 21),
                str_pad($zoneRecord->getType(), 9),
                str_pad($zoneRecord->getTtl(), 6),
                str_pad($zoneRecord->getPrio(), 7),
                $zoneRecord->getContent()
            ));
        }
    }

    public static function getName()
    {
        return 'zone';
    }
}
