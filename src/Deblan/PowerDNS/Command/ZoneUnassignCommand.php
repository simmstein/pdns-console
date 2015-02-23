<?php

namespace Deblan\PowerDNS\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Deblan\Console\Command\AbstractCommand;
use Deblan\PowerDNS\Model\Domain;
use Deblan\PowerDNS\Model\DomainQuery;

class ZoneUnassignCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('zone:unassign')
            ->setDescription('Unassign the domain zone')
            ->addArgument('domain_id', InputArgument::REQUIRED, 'Domain ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $domain = DomainQuery::create()->findOneById((int) $this->getInput()->getArgument('domain_id'));

        if (null === $domain) {
            $this->getOutput()->writeln('<error>Domain not found.</error>');

            return;
        }

        $domain->setZone(null)->save();

        $this->getOutput()->writeln('<info>Domain zone updated.</info>');
    }
}
