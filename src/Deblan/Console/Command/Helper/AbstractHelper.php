<?php

namespace Deblan\Console\Command\Helper;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractHelper
{
    protected static $instances = [];

    public static function getInstance(InputInterface $input, OutputInterface $output)
    {
        $class = get_called_class();
        $name = $class::getName();

        if (empty(self::$instances[$name])) {
            $instance = (new $class)
                ->setInput($input)
                ->setOutput($output);

            self::$instances[$name] = $instance;
        }

        return self::$instances[$name];
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

    abstract public static function getName();
}
