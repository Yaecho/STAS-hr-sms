<?php

/**
 * 短信类
 */
namespace Stas;

class Sms
{
    /**
     * 短信填充内容
     *
     * @var array
     * @author Yaecho 
     */
    public $content = array();

    /**
     * 短信接受人,多个以逗号隔开
     *
     * @var string
     * @author Yaecho 
     */
    public $phoneNumer = '';

    /**
     * 短信模板
     *
     * @var string
     * @author Yaecho 
     */
    public $templete = '';

    /**
     * 模板占位符
     *
     * @var string
     * @author Yaecho 
     */
    public $placeHolder = '$code$';

    /**
     * 延时发送时间（秒）
     *
     * @var integer
     * @author Yaecho 
     */
    public $sleepTime = 0;

    public $error = array();

    /**
     * 发送短信
     *
     * @return bool
     * @author Yaecho 
     */
    public function send($driver)
    {
        //拼装短信
        $realContent = $this->parseContent();
        //解析手机号码
        $realPhoneNumber = $this->parsePhoneNumber();
        foreach ($realPhoneNumber as $number) {
            //是否需要延时
            if ($this->sleepTime > 0) {
                sleep($this->sleepTime);
            }
            //发送
            $res = $driver->send($number, $realContent);
            //错误记录
            if (!$res) {
                $this->error[$number] = $driver->error;
            }
        }
    }

    /**
     * 解析短信内容
     *
     * @return string
     * @author Yaecho 
     */
    protected function parseContent()
    {
        //分割模板重新拼装
        $templeteArray = explode($this->placeHolder, $this->templete);
        $count = count($templeteArray);
        $result = '';
        for ($i = 0; $i < $count; $i++) {
            $result .= $templeteArray[$i];
            if (isset($this->content[$i])) {
                $result .= $this->content[$i];
            }
        }
        return $result;
    }

    /**
     * 解析手机号码
     *
     * @return array
     * @author Yaecho 
     */
    protected function parsePhoneNumber()
    {
        return explode(',', $this->phoneNumer);
    }
}