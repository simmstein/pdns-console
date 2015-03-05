<?php

namespace Deblan\PowerDNS\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Deblan\PowerDNS\Model\DomainQuery;
use Deblan\Console\Command\AbstractCommand;

class DomainListCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('domain:list')
            ->setDescription('List domains')
            ->addOption('short', null, InputOption::VALUE_NONE, 'Show only domain names')
            ->addOption('zone', null, InputOption::VALUE_NONE, 'Show zone assigned (with all zone versions)')
            ->addOption('active', null, InputOption::VALUE_NONE, 'Show the activated zones versions');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $query = $this->getDomainQuery();

        $domains = $query->find();

        foreach ($domains as $key => $domain) {
            if ($this->getInput()->getOption('short')) {
                $this->getHelper('domain')->showShortDomain($domain, $key);

                continue;
            }

            $this->getHelper('domain')->showDomain($domain, $key);

            if ($this->getInput()->getOption('zone') && $domain->getZone()) {
                $this->getOutput()->writeln('');
                $this->getHelper('zone')->showZone($domain->getZone(), 0, true);
                $this->getOutput()->writeln('');
                $this->getOutput()->writeln('');
            }
        }
    }

    protected function getDomainQuery()
    {
        $query = DomainQuery::create()->orderByName();

        return $query;
    }
}
