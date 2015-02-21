<?php

namespace Deblan\PowerDNS\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Command\Command;
use Deblan\Console\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Deblan\PowerDNS\Model\Map\ZoneRecordTableMap;
use Deblan\PowerDNS\Model\ZoneVersionQuery;
use Deblan\PowerDNS\Model\ZoneRecord;

class ZoneRecordAddCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('zone:record:add')
            ->setDescription('Add a zone record')
            ->addArgument('zone_id', InputArgument::REQUIRED, 'ZONE_ID')
            ->addArgument('version', InputArgument::REQUIRED, 'VERSION')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, '')
            ->addOption('type', null, InputOption::VALUE_REQUIRED, '')
            ->addOption('content', null, InputOption::VALUE_REQUIRED, '')
            ->addOption('ttl', null, InputOption::VALUE_REQUIRED, '')
            ->addOption('prio', null, InputOption::VALUE_REQUIRED, '')
            ->setHelp("The <info>%command.name%</info> ");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $name = $this->getInput()->getOption('name');
        $type = $this->getInput()->getOption('type');
        $content = $this->getInput()->getOption('content');
        $ttl = $this->getInput()->getOption('ttl');
        $prio = $this->getInput()->getOption('prio');

        $zoneId = (int) $this->getInput()->getArgument('zone_id');
        $version = (int) $this->getInput()->getArgument('version');

        $zoneVersion = ZoneVersionQuery::create()
            ->filterByZoneId($zoneId)
            ->filterByVersion($version)
            ->findOne();

        if (null === $zoneVersion) {
            $this->getOutput()->writeln('<error>Zone version not found.</error>');

            return;
        }

        if ($zoneVersion->getIsActive()) {
            $this->getOutput()->writeln('<error>You can not add zone record of an activated zone version.</error>');

            return;
        }

        while (null === $name || trim($name) === '') {
            $name = $this->getHelper('dialog')->ask($this->getOutput(), 'Name: ', null);
        }

        while (null === $content || trim($content) === '') {
            $content = $this->getHelper('dialog')->ask($this->getOutput(), 'Content: ', null);
        }

        while (!$this->getHelper('validator')->isRecordType($type)) {
            $this->getOutput()->writeln('');
            $this->getOutput()->writeln(sprintf(
                'Available types: <comment>%s</comment>',
                implode(' ', ZoneRecordTableMap::getValueSet(ZoneRecordTableMap::COL_TYPE))
            ));

            $type = $this->getHelper('dialog')->ask($this->getOutput(), 'Type: ', null);
        }

        while (!is_numeric($ttl)) {
            $ttl = $this->getHelper('dialog')->ask($this->getOutput(), 'TTL: ', null);
        }

        if ($prio === 'null') {
            $prio = null;
        } elseif (null === $prio || trim($prio) === '') {
            $response = $this->getHelper('dialog')->ask($this->getOutput(), 'Prio [null]: ', null);
            $prio = empty($response) ? null : (int) $response;
        }

        $zoneRecord = (new ZoneRecord())
            ->setZoneVersion($zoneVersion)
            ->setName($name)
            ->setType($type)
            ->setContent($content)
            ->setPrio($prio)
            ->setTtl($ttl);

        $zoneRecord->save();

        $this->getOutput()->writeln('<info>Zone record added.</info>');
    }
}
