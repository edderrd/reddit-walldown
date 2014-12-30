<?php namespace App; 

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RecentDownloader extends Command
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
        $this->setName("recent")
            ->setDescription("Download recent items from reddit.com/r/earthporn");
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return mixed
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Downloading images...</info>");
        $imgUrls = $this->fetchImages();
        $this->saveImagesTo($this->config->downloadPath(), $imgUrls, $output);

        return $output->writeln("<info>Downloaded ".count($imgUrls)." images on {$this->config->downloadPath()}");
    }

    /**
     * @return array
     */
    private function fetchImages()
    {
        $data = json_decode(file_get_contents($this->config->url()), true);
        $imgs = [];

        if (! $data)
            throw new \InvalidArgumentException("couldn't download images from reddit, please check");

        foreach($data['data']['children'] as $item)
        {
            if (isset($item['data']['url']))
            {
                if (strpos($item['data']['url'], 'imgur') !== false)
                    $imgs[$item['data']['name']] = str_replace(".jpg", "", $item['data']['url']) . ".jpg";
            }
        }
        return $imgs;
    }

    /**
     * @param $downloadPath
     * @param $imgUrls
     * @param OutputInterface $output
     */
    private function saveImagesTo($downloadPath, $imgUrls, OutputInterface $output)
    {
        if ( ! is_writable($downloadPath))
            throw new \InvalidArgumentException("Destination path '$downloadPath' is not writeable");

        foreach($imgUrls as $name => $url)
        {
            $path = "{$downloadPath}/{$name}.jpg";
            $output->writeln("Saving image {$path}");
            file_put_contents($path, file_get_contents($url));
        }
    }

}