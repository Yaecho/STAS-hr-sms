<?php

namespace Stas;

use Stas\Event;
use Medoo\Medoo;

class App
{
    protected $map = array('start', 'work', 'end');

    protected $config = array();

    public function __construct()
    {
        Event::loadMap($this->map);
        $this->config = require_once __DIR__ . '/../../config.php';
        Event::set('Medoo', new Medoo($this->config['db']));
    }

    public function init()
    {
        Event::add('start', function () {
            var_dump(Event::get('Medoo'));
            sleep(3);
        });
    }
}

