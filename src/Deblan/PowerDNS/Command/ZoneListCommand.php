<?php

namespace Deblan\PowerDNS\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Deblan\PowerDNS\Model\ZoneQuery;

class ZoneListCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('zone:list')
            ->setDescription('List DNS zones')
            // ->addArgument('foo', InputArgument::OPTIONAL, '')
            // ->addOption('bar', null, InputOption::VALUE_NONE, '')
            ->setHelp("The <info>%command.name%</info> ");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // $this->getContainer()->get('foo.bar');
        // $output->writeln(sprintf('<comment>%s</comment> bar.', $example));
        // $input->getArgument('foo');
        // $input->getOption('bar');

        $zones = ZoneQuery::create()->orderByName()->find();

        foreach ($zones as $zone) {
            $output->writeln(sprintf('<info>%s</info>.', $zone->getName()));

            if ($zone->getDescription()) {
                $output->writeln($zone->getDescription());
                $output->writeln('');
            }

            foreach ($zones->getZoneVersions() as $zoneVersion) {
                $output->writeln(sprintf('<comment>Version </comment> %d', $zoneVersion->getVersion()));

                if ($zoneVersion->getIsActive()) {
                    $output->writeln('<comment>Activeted</comment>');
                }
            }
        }
    }
}
