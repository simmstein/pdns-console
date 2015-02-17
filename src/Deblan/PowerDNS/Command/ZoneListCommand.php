<?php

namespace Deblan\PowerDNS\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use Deblan\PowerDNS\Model\ZoneQuery;
use Deblan\PowerDNS\Model\Zone;
use Deblan\PowerDNS\Model\ZoneVersion;

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

        foreach ($zones as $key => $zone) {
            $this->showZone($zone, $output, $key);

        }
    }

    protected function showZone(Zone $zone, OutputInterface $output, $key)
    {
        $output->writeln(sprintf('<info>%s</info>.', $zone->getName()));

        if ($zone->getDescription()) {
            $output->writeln($zone->getDescription());
        }

        foreach ($zone->getZoneVersions() as $key => $zoneVersion) {
            $this->showZoneVersion($zoneVersion, $output, $key);
        }
    }

    protected function showZoneVersion(ZoneVersion $zoneVersion, OutputInterface $output, $key)
    {
        $output->writeln('');
        $output->writeln(sprintf(
            '<info>Version</info>: <comment>%d</comment> - <info>Active</info>: %s',
            $zoneVersion->getVersion(),
            $zoneVersion->getIsActive() ? 'Yes' : 'No'
        ));

        $this->showZoneVersionRecords($zoneVersion, $output);
    }

    protected function showZoneVersionRecords(ZoneVersion $zoneVersion, OutputInterface $output)
    {
        $output->writeln('');
        $output->writeln('<comment>   ID | NAME                  | TYPE      | TTL    | PRIO    | CONTENT</comment>');
        $output->writeln('<comment>----------------------------------------------------------------------</comment>');

        foreach ($zoneVersion->getZoneRecords() as $zoneRecord) {
            $output->writeln(sprintf(
                '%5d | %s | %s | %s | %s | %s',
                $zoneRecord->getId(),
                str_pad($zoneRecord->getName(), 21),
                str_pad($zoneRecord->getType(), 9),
                str_pad($zoneRecord->getTtl(), 6),
                str_pad($zoneRecord->getPrio(), 7),
                $zoneRecord->getContent()
            ));
        }
    }
}
