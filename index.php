<?php
//引入composer 自动加载
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/lib/autoload.php';

use Stas\Server;

Server::run();
