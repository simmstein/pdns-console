<?php

namespace Deblan\PowerDNS\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Deblan\Console\Command\AbstractCommand;
use Deblan\PowerDNS\Model\Domain;
use Deblan\PowerDNS\Model\DomainQuery;
use Deblan\PowerDNS\Model\ZoneQuery;

class ZoneAssignCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('zone:assign')
            ->setDescription('Assign a zone to a domain.')
            ->addArgument('zone_id', InputArgument::REQUIRED, 'Zone ID')
            ->addArgument('domain_id', InputArgument::REQUIRED, 'Domain ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $zoneId = (int) $this->getInput()->getArgument('zone_id');
        $zone = ZoneQuery::create()->findOneById($zoneId);
        $domain = DomainQuery::create()->findOneById((int) $this->getInput()->getArgument('domain_id'));

        if (null === $zone) {
            $this->getOutput()->writeln('<error>Zone not found.</error>');

            return;
        }

        if (null === $domain) {
            $this->getOutput()->writeln('<error>Domain not found.</error>');

            return;
        }

        $domain->setZone($zone)->save();

        $this->getOutput()->writeln('<info>Domain zone updated.</info>');
    }
}
