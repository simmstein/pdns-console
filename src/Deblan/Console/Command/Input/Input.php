<?php

namespace Deblan\Console\Command\Input;

use Symfony\Component\Console\Input\ArgvInput;

class Input extends ArgvInput
{
    public function getArgument($name)
    {
        try {
            return parent::getArgument($name);
        } catch (\InvalidArgumentException $e) {
            return null;
        }
    }

    public function getOption($name)
    {
        try {
            return parent::getOption($name);
        } catch (\InvalidArgumentException $e) {
            return null;
        }
    }
}
