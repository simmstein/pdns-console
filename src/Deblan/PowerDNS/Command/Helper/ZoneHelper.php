<?php

namespace Deblan\PowerDNS\Command\Helper;

use Deblan\Console\Command\Helper\AbstractHelper;
use Deblan\PowerDNS\Model\Base\Zone;
use Deblan\PowerDNS\Model\Base\ZoneVersion;

class ZoneHelper extends AbstractHelper
{
    const INDENT = "      <fg=cyan>></> ";

    public function showZone(Zone $zone, $key = 0, $withIndent = false)
    {
        $this->getOutput()->writeln(sprintf(
            '%s<info>%s</info>',
            $withIndent ? self::INDENT : '',
            $zone->getName()
        ));

        $this->getOutput()->writeln(sprintf(
            '%s<info>%s</info>',
            $withIndent ? self::INDENT : '',
            str_repeat('-', strlen($zone->getName()))
        ));

        if ($zone->getDescription()) {
            $this->getOutput()->writeln(($withIndent ? self::INDENT : '').$zone->getDescription());
        }

        $this->getOutput()->writeln(sprintf(
            '%sID: %d',
            $withIndent ? self::INDENT : '',
            $zone->getId()
        ));

        if (!$zone->countZoneVersions()) {
            $this->getOutput()->writeln(($withIndent ? self::INDENT : ''));
            $this->getOutput()->writeln(($withIndent ? self::INDENT : '').'No version found.');
        } else {
            foreach ($zone->getZoneVersions() as $key => $zoneVersion) {
                $this->showZoneVersion($zoneVersion, $key, $withIndent);
            }
        }

        $this->getOutput()->writeln(($withIndent ? self::INDENT : ''));
    }

    public function showZoneVersion(ZoneVersion $zoneVersion, $key = 0, $withIndent = false)
    {
        if ($this->getInput()->getOption('active') && false === $zoneVersion->getIsActive()) {
            return;
        }

        if ($this->getInput()->getOption('no-active') && true === $zoneVersion->getIsActive()) {
            return;
        }

        $this->getOutput()->writeln($withIndent ? self::INDENT : '');
        $this->getOutput()->writeln(sprintf(
            '%s<info>Version</info>: <comment>%d</comment> - <info>Active</info>: %s',
            $withIndent ? self::INDENT : '',
            $zoneVersion->getVersion(),
            $zoneVersion->getIsActive() ? 'Yes' : 'No'
        ));

        $this->showZoneVersionRecords($zoneVersion, $withIndent);
    }

    public function showZoneVersionRecords(ZoneVersion $zoneVersion, $withIndent = false)
    {
        $this->getOutput()->writeln($withIndent ? self::INDENT : '');

        if (!$zoneVersion->countZoneRecords()) {
            $this->getOutput()->writeln(($withIndent ? self::INDENT : '').'No record found.');

            return;
        }

        $this->getOutput()->writeln(sprintf(
            '%s<comment>   ID | NAME                  | TYPE      | TTL    | PRIO    | CONTENT</comment>',
            $withIndent ? self::INDENT : ''
        ));

        $this->getOutput()->writeln(sprintf(
            '%s<comment>----------------------------------------------------------------------</comment>',
            $withIndent ? self::INDENT : ''
        ));

        foreach ($zoneVersion->getZoneRecords() as $zoneRecord) {
            $this->getOutput()->writeln(sprintf(
                '%s%5d | %s | %s | %s | %s | %s',
                $withIndent ? self::INDENT : '',
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
