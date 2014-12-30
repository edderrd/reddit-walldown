<?php namespace App; 

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanupCommand extends Command
{
    /**
     * @var ConfigManager
     */
    private $config;

    /**
     * @param ConfigManager $config
     */
    function __construct(ConfigManager $config)
    {
        parent::__construct();
        $this->config = $config;
    }

    /**
     *
     */
    public function configure()
    {
        $this->setName("cleanup")
            ->setDescription("Removes existing imgs on configured folder")
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $files = array_diff(scandir($this->config->downloadPath()), ['..', '.']);
        foreach($files as $file)
        {
            if (strpos($file, '.jpg') == false) continue;
            $path = $this->config->downloadPath()."/{$file}";
            $output->writeln("Removed $path");
            unlink($path);
        }
    }

}