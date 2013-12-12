<?php

namespace Migration\Console;

use Migration\Exception;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;


/**
 * Single command application
 */
class Application extends BaseApplication
{

    private $command;
    private $directory;


    public function __construct($configurator, $context)
    {
        $this->command = new RunCommand($configurator, $context);
        parent::__construct();
    }

    /**
     * @param string $dir path to directory
     */
    public function setDirectory($dir)
    {
        if (!is_dir($dir))
        {
            throw new Exception("Failed setting $dir as root migrations dir.");
        }

        $this->directory = $dir;
    }

    /**
     * @return path to directory
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * Gets the name of the command based on input.
     *
     * @param InputInterface $input The input interface
     * @return string The command name
     */
    protected function getCommandName(InputInterface $input)
    {
        // This should return the name of your command.
        return 'run';
    }

    /**
     * Simulate execution (provides support for non-cli)
     */
    public function runWithArgs(array $args)
    {
        $input = new ArrayInput($args);
        return $this->command->run($input, new \Symfony\Component\Console\Output\ConsoleOutput()); // TODO FIX
    }

    /**
     * Gets the default commands that should always be available.
     *
     * @return array An array of default Command instances
     */
    protected function getDefaultCommands()
    {
        // Keep the core default commands to have the HelpCommand
        // which is used when using the --help option
        $defaultCommands = parent::getDefaultCommands();

        $defaultCommands[] = $this->command;

        return $defaultCommands;
    }

    /**
     * Overridden so that the application doesn't expect the command
     * name to be the first argument.
     */
    public function getDefinition()
    {
        $inputDefinition = parent::getDefinition();
        // clear out the normal first argument, which is the command name
        $inputDefinition->setArguments();

        return $inputDefinition;
    }
}
