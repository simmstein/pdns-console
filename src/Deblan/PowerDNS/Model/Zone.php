<?php

namespace Deblan\PowerDNS\Model;

use Deblan\PowerDNS\Model\Base\Zone as BaseZone;

class Zone extends BaseZone
{
    public function getActiveZoneVersion()
    {
        $collection = $this->getZoneVersions(ZoneVersionQuery::create()->filterByIsActive(true));

        if (count($collection)) {
            return $collection[0];
        }

        return null;
    }
}
