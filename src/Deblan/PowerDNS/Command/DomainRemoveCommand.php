<?php

namespace Deblan\PowerDNS\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Command\Command;
use Deblan\PowerDNS\Model\DomainQuery;
use Deblan\Console\Command\AbstractCommand;

class DomainRemoveCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('domain:remove')
            ->setDescription('Remove a domain')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, '')
            ->addOption('id', null, InputOption::VALUE_REQUIRED, '')
            ->addOption('confirm', null, InputOption::VALUE_NONE, '')
            ->setHelp("The <info>%command.name%</info> ");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $name = $this->getInput()->getOption('name');
        $id = $this->getInput()->getOption('id');
        $id = $this->getInput()->getOption('id');

        if (null === $name && null === $id) {
            $this->getOutput()->writeln('<error>You must give a name or an id.</error>');

            return;
        }

        if ($name) {
            $domain = $this->getQuery()->findOneByName($name);
        } else {
            $domain = $this->getQuery()->findOneById($id);
        }

        if (null === $domain) {
            $this->getOutput()->writeln('<error>No domain found.</error>');

            return;
        }

        $confirm = $this->getInput()->getOption('confirm');

        if (false === $confirm) {
            $confirm = $this->getHelper('dialog')->askConfirmation($this->getOutput(), '<info>Do you confirm? [<comment>no</comment>] ', false);
        }

        if ($confirm) {
            $domain->delete();

            $this->getOutput()->writeln('<info>Domain removed.</info>');
        } else {
            $this->getOutput()->writeln('<info>Aborted.</info>');
        }
    }

    protected function getQuery()
    {
        return DomainQuery::create();
    }
}
