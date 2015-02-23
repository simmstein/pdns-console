<?php

namespace Deblan\PowerDNS\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Command\Command;
use Deblan\PowerDNS\Model\ZoneQuery;

class ZoneListCommand extends AbstractZoneCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('zone:list')
            ->setDescription('List DNS zones')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'Filter by zone name')
            ->addOption('id', null, InputOption::VALUE_REQUIRED, 'Filter by zone ID')
            ->addOption('vs', null, InputOption::VALUE_REQUIRED, 'Filter zone version');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $query = $this->getZoneQuery();

        $zones = $query->find();

        foreach ($zones as $key => $zone) {
            $this->getHelper('zone')->showZone($zone, $key);
        }
    }

    protected function getZoneQuery()
    {
        $query = ZoneQuery::create()->orderByName();

        if ($this->getInput()->getOption('name')) {
            $query->filterByName(sprintf('%%%s%%', $this->getInput()->getOption('name')));
        }

        if ($this->getInput()->getOption('id')) {
            $query->filterById((int) $this->getInput()->getOption('id'));
        }

        return $query;
    }
}
