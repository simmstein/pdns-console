<?php

namespace Deblan\PowerDNS\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
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
            // ->addArgument('foo', InputArgument::OPTIONAL, '')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, '')
            ->setHelp("The <info>%command.name%</info> ");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        // $this->getContainer()->get('foo.bar');
        // $output->writeln(sprintf('<comment>%s</comment> bar.', $example));
        // $input->getArgument('foo');
        // $input->getOption('bar');

		$query = $this->getZoneQuery();

        $zones = $query->find();

        foreach ($zones as $key => $zone) {
            $this->showZone($zone, $key);
        }
    }

    protected function getZoneQuery()
    {
        $query = ZoneQuery::create()->orderByName();

        if ($this->getInput()->getOption('name')) {
            $query->filterByName(sprintf('%%%s%%', $this->getInput()->getOption('name')));
        }

        return $query;
    }
}
