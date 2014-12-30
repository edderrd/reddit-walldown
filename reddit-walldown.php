#!/usr/bin/env php
<?php

require_once "vendor/autoload.php";

use App\ConfigManager;
use App\RecentDownloader;
use Symfony\Component\Console\Application;

$app = new Application('reddit-walldown', '@package_version@');
$app->add(new RecentDownloader(new ConfigManager()));
$app->run();