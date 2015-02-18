<?php

namespace Deblan\PowerDNS\Command;

use Deblan\Console\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputOption;

abstract class AbstractZoneCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->addOption('active', null, InputOption::VALUE_NONE, '')
            ->addOption('no-active', null, InputOption::VALUE_NONE, '');
    }
}
