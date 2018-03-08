<?php

namespace Stas;

use Stas\Event;
use Medoo\Medoo;
use Stas\Sms;
use YunpianAdapter\Yunpian;

class App
{
    protected $map = array('start', 'work', 'end');

    protected $config = array();

    public function __construct()
    {
        Event::loadMap($this->map);
        global $APP_CONFIG;
        $this->config = $APP_CONFIG;
        Event::set('Medoo', new Medoo($this->config['db']));
    }

    public function init()
    {
        Event::add('start', function () {
            $Medoo = Event::get('Medoo');
            // 检查是否开启短信发送
            $data = $Medoo->select('setting', ['name', 'value'], ['name' => array('send_sms', 'yunpian', 'sms_templete')]);
            $setting = array_column($data, 'value', 'name');
            
            if ($setting['send_sms'] === 'true') {
                //开始发送
                Event::set('send_sms', true, false);
                //更新秘钥
                Event::set('secret_key', $setting['yunpian'], false);
                //更新短信模板
                Event::set('sms_templete', $setting['sms_templete'], false);
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
            $Medoo = Event::get('Medoo');
            
            //查出一条短信
            $data = $Medoo->get('resume', ['id', 'phone', 'code'], ['ORDER' => 'id', 'not_recycling' => '1', 'res' => '0', 'is_send' => '0']);
            if (!$data) {
                sleep(5);
                return false;
            }
            //发送短信
            $Sms = new Sms(new Yunpian(Event::get('secret_key')));
            $Sms->phoneNumer = $data['phone'];
            $Sms->templete = Event::get('sms_templete');
            $Sms->content = [$data['code']];
            $Sms->sleepTime = 5;
            $Sms->send();
            
            //失败还是成功
            if (empty($Sms->error)) {
                //标记发送成功
                $is_send = 1;
            } else {
                //标记发送失败
                $is_send = 2;
            }
            $Medoo->update('resume', ['is_send' => 1], ['id' => $data['id']]);
            });
    }
}

