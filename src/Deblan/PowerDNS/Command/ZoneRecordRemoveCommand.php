<?php

namespace Deblan\PowerDNS\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Deblan\Console\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Deblan\PowerDNS\Model\ZoneRecordQuery;
use Symfony\Component\Console\Input\InputOption;

class ZoneRecordRemoveCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('zone:record:remove')
            ->setDescription('Remove a zone record')
            ->addArgument('id', InputArgument::REQUIRED, 'Record ID (the record can not be associated with an active zone version)')
            ->addOption('confirm', null, InputOption::VALUE_NONE, 'Confirmation');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $id = (int) $this->getInput()->getArgument('id');

        $zoneRecord = ZoneRecordQuery::create()->findOneById($id);

        if (null === $zoneRecord) {
            $this->getOutput()->writeln('<error>Zone record not found.</error>');

            return;
        }

        if ($zoneRecord->getZoneVersion()->getIsActive()) {
            $this->getOutput()->writeln('<error>You can not remove a record of an active zone version.</error>');

            return;
        }

        $confirm = $this->getInput()->getOption('confirm');

        if (false === $confirm) {
            $confirm = $this->getHelper('dialog')->askConfirmation($this->getOutput(), '<info>Do you confirm? [<comment>no</comment>] ', false);
        }

        if ($confirm) {
            $zoneRecord->delete();

            $this->getOutput()->writeln('<info>Zone record removed.</info>');
        } else {
            $this->getOutput()->writeln('<info>Aborted.</info>');
        }
    }
}
