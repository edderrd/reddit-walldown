<?php namespace App; 

class ConfigManager
{
    /**
     *
     */
    function __construct()
    {
        $this->config = $this->openOrCreateConfig("~/.reddit-walldown.json");
    }

    /**
     * @return string
     */
    public function downloadPath()
    {
        return $this->sanitizeHomePath($this->config->destination);
    }

    /**
     * @return string
     */
    public function url()
    {
        return $this->config->url;
    }

    /**
     * @param $path
     * @return object
     */
    private function openOrCreateConfig($path)
    {
        $path = $this->sanitizeHomePath($path);
        if (file_exists($path))
        {
            return $this->parseConfig($path);
        }

        return $this->createConfig($path);
    }

    /**
     * @param $path
     * @return Object
     */
    private function parseConfig($path)
    {
        $config = json_decode(file_get_contents($path));
        if (!$config)
        {
            throw new \InvalidArgumentException("Configuration file is not readable or invalid formated");
        }

        return $config;
    }

    /**
     * @param $path
     * @return object
     */
    private function createConfig($path)
    {
        $data = [
            'destination' => "~/",
            'url' => 'http://www.reddit.com/r/earthporn.json',
        ];

        file_put_contents($this->sanitizeHomePath($path), json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
        return (object) $data;
    }

    /**
     * @param $path
     * @return mixed
     */
    private function sanitizeHomePath($path)
    {
        return str_replace('~/', "{$_SERVER['HOME']}/", $path);
    }
}