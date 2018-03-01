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

}