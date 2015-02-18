<?php

namespace Deblan\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

abstract class AbstractCommand extends Command
{
    protected $input;

    protected $output;

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
