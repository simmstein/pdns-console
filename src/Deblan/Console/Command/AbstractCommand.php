<?php

namespace Deblan\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Deblan\PowerDNS\Command\Helper\ZoneHelper;
use Deblan\PowerDNS\Command\Helper\DomainHelper;

abstract class AbstractCommand extends Command
{
    protected $input;

    protected $output;

    public function getHelper($helper)
    {
        if ($helper === 'zone') {
            return ZoneHelper::getInstance($this->getInput(), $this->getOutput());
        }

        if ($helper === 'domain') {
            return DomainHelper::getInstance($this->getInput(), $this->getOutput());
        }

        throw new \InvalidArgumentException(sprintf('Invalid helper "%s"', $helper));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setInput($input);
        $this->setOutput($output);
    }

    public function setInput(InputInterface $input)
    {
        $this->input = $input;

        return $this;
    }

    public function getInput()
    {
        return $this->input;
    }

    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;

        return $this;
    }

    public function getOutput()
    {
        return $this->output;
    }
}
