<?php

namespace Deblan\PowerDNS\Command\Helper;

use Deblan\Console\Command\Helper\AbstractHelper;
use Deblan\PowerDNS\Model\Base\Domain;

class DomainHelper extends AbstractHelper
{
    public function showDomain(Domain $domain, $key = 0)
    {
        $this->getOutput()->writeln(sprintf('DOMAIN: <info>%s</info>', $domain->getName()));
        $this->getOutput()->writeln(sprintf('ID    : <info>%d</info>', $domain->getId()));
        $this->getOutput()->writeln(sprintf('TYPE  : <info>%s</info>', $domain->getType()));
        $this->getOutput()->writeln(sprintf('MASTER: <info>%s</info>', $domain->getMaster()));
    }

    public function showShortDomain(Domain $domain, $key = 0)
    {
        $this->getOutput()->writeln(sprintf('<info>%s</info>', $domain->getName()));
    }

    public static function getName()
    {
        return 'domain';
    }
}
