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
            $Medoo = Event::get('Medoo');
            // 检查是否开启短信发送
            $data = $Medoo->select('setting', ['name', 'value'], ['name' => array('send_sms', 'yunpian')]);
            $setting = array_column($data, 'value', 'name');
            
            if ($setting['send_sms'] === 'true') {
                //开始发送
                Event::set('send_sms', true, false);
                //更新秘钥
                Event::set('secret_key', $setting['yunpian'], false);
            } else {
                //停止发送
                Event::set('send_sms', false, false);
                sleep(10);
            }
        });
        Event::add('work', function (){
            if (!Event::get('send_sms')) {
                return;
            }
            echo 'sending';
            sleep(1);
        });
    }
}

