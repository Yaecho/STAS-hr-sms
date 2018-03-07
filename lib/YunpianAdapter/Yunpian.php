<?php

namespace YunpianAdapter;

use \Yunpian\Sdk\YunpianClient;
use Stas\SmsDriverInterface;

class Yunpian implements SmsDriverInterface
{

    public $error = null;

    private $clnt;

    public function __construct($apikey)
    {
        $this->clnt = YunpianClient::create($apikey);
    }

    public function send($number, $content)
    {
        $param = [YunpianClient::MOBILE => $number,YunpianClient::TEXT => $content];
        $r = $this->clnt->sms()->single_send($param);
        if(!$r->isSucc()){
            $this->error = $r->msg();
        }
    }

    public function getError()
    {
        return $this->error;
    }
}