<?php

namespace Migration\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class RunCommand extends Command
{

    const PATH_DATA = 'data';
    const PATH_STRUCT = 'struct';


    private $configurator;
    private $context;


    public function __construct($configurator, $context)
    {
        parent::__construct();
        $this->configurator = $configurator;
        $this->context = $context;
    }

    protected function configure()
    {
        $this
            ->setName('run')
            ->setDescription('Run migrations')
            ->addOption(
                'reset',
                'r',
                InputOption::VALUE_NONE,
                'Drop all tables and run all migrations'
            )
            ->addOption(
               'data',
               'd',
               InputOption::VALUE_NONE,
               'Include data migrations'
            )
        ;
    }

    private function getPath($type)
    {
        if (!in_array($type, array(self::PATH_DATA, self::PATH_STRUCT)))
        {
            throw new Exception;
        }
        return $this->getApplication()->getDirectory() . "/$type";
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('reset') AND $this->context->parameters['productionMode'])
        {
            throw new Exception('Reset není povolen na produkčním prostředí.');
        }

        $runner = new \Migration\Runner($this->context->dibiConnection);
        $runner->addExtension(new \Migration\Extensions\OrmPhp($this->configurator, $this->context, $this->context->dibiConnection));

        $finder = new \Migration\Finders\MultipleDirectories;
        $finder->addDirectory($this->getPath(self::PATH_STRUCT));
        if (isset($_GET['data']))
        {
            $finder->addDirectory($this->getPath(self::PATH_DATA));
        }

        $runner->run($finder, $input->getOption('reset'));
    }
}
