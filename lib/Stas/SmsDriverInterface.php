<?php

/**
 * 短信发送接口
 */
namespace Stas;

interface SmsDriverInterface
{
    public function send($number, $content);

    public function getError();

}