<?php

namespace Deblan\PowerDNS\Command\Helper;

use Deblan\Console\Command\Helper\AbstractHelper;
use Deblan\PowerDNS\Model\Map\ZoneRecordTableMap;

class ValidatorHelper extends AbstractHelper
{
    public function isIp($value)
    {
        return filter_var($value, FILTER_VALIDATE_IP);
    }

    public function isRecordType($value)
    {
        return in_array($value, ZoneRecordTableMap::getValueSet(ZoneRecordTableMap::COL_TYPE));
    }

    public function isDomainName($value)
    {
        return preg_match('/^(?!\-)(?:[a-zA-Z\d\-]{0,62}[a-zA-Z\d]\.?){1,126}(?!\d+)[a-zA-Z\d]{1,63}$/', $value);
    }

    public function isDomainMaster($value)
    {
        return $value === null || $this->isDomainName($value);
    }

    public function isDomainType($value)
    {
        return in_array($value, ['NATIVE', 'MASTER', 'SLAVE', 'SUPERSLAVE']);
    }

    public static function getName()
    {
        return 'validator';
    }
}
