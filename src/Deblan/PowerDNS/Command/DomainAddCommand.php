<?php

namespace Deblan\PowerDNS\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Command\Command;
use Deblan\Console\Command\AbstractCommand;
use Deblan\PowerDNS\Model\Domain;
use Deblan\PowerDNS\Model\DomainQuery;

class DomainAddCommand extends AbstractCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('domain:add')
            ->setDescription('Add a domain')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, '')
            ->addOption('type', null, InputOption::VALUE_REQUIRED, '')
            ->addOption('master', null, InputOption::VALUE_REQUIRED, '')
            ->setHelp("The <info>%command.name%</info> ");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);

        $name = $this->getInput()->getOption('name');
        $master = $this->getInput()->getOption('master');
        $type = $this->getInput()->getOption('type');

        while (null === $name || trim($name) === '') {
            $name = $this->getHelper('dialog')->ask($this->getOutput(), 'Name: ', null);
        }

        if ($master === 'null') {
            $master = null;
        } elseif (null === $master || trim($master) === '') {
            $response = $this->getHelper('dialog')->ask($this->getOutput(), 'MASTER [null]: ', null);
            $master = empty($response) ? null : $response;
        }

        if ($type === 'null') {
            $type = null;
        } elseif (null === $type) {
            $response = $this->getHelper('dialog')->ask($this->getOutput(), 'Type [NATIVE]: ', null);
            $type = empty($response) ? 'NATIVE' : $response;
        }

        if (!$this->getHelper('validator')->isDomainMaster($master)) {
            $this->getOutput()->writeln('<error>Invalid master.</error>');

            return;
        }

        if (!$this->getHelper('validator')->isDomainName($name)) {
            $this->getOutput()->writeln('<error>Invalid name.</error>');

            return;
        }

        if (!$this->getHelper('validator')->isDomainType($type)) {
            $this->getOutput()->writeln('<error>Invalid type.</error>');

            return;
        }

        if (DomainQuery::create()->findOneByName($name)) {
            $this->getOutput()->writeln('<error>The domain already exists.</error>');

            return;
        }

        $domain = (new Domain())
            ->setName($name)
            ->setMaster($master)
            ->setType($type);

        $domain->save();

        $this->getOutput()->writeln('<info>Domain added.</info>');
    }
}
