<?php

namespace Deblan\Console;

use ReflectionException;
use ReflectionClass;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Finder\Finder;
use Propel\Runtime\Propel;

class Application extends BaseApplication
{
    protected $commandsPaths = array();

    public function addCommandsPath($path, $namespace)
    {
        $this->commandsPaths[$path] = trim($namespace, '\\');

        return $this;
    }

    public function chdir($directory)
    {
        chdir($directory);

        return $this;
    }

    public function initPropel()
    {
        Propel::init('app/propel/config.php');
    }

    public function loadCommands()
    {
        foreach ($this->commandsPaths as $path => $namespace) {
            $finder = new Finder();
            $finder->name('*Command.php')->in($path);

            foreach ($finder as $file) {
                $className = $namespace.'\\'.str_replace('.php', '', $file->getFilename());

                try {
                    $reflexion = new ReflectionClass($className);

                    if ($reflexion->isInstantiable()) {
                        $command = new $className();

                        $this->addCommands(array(
                            $command,
                        ));
                    }
                } catch (ReflectionException $e) {

                }
            }
        }

        return $this;
    }
}
