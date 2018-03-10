<?php

/**
 * 简易的事件循环类
 */
namespace Stas;

use stas\RunInterface;

class Event
{
    /**
     * 函数或可运行类
     *
     * @var array
     * @author Yaecho 
     */
    private static $funcs = array();

    /**
     * 运作tag的索引
     *
     * @var array
     * @author Yaecho 
     */
    private static $map = array();

    /**
     * 缓存数据
     *
     * @var array
     * @author Yaecho 
     */
    protected static $data = array();

    /**
     * 新增事件
     *
     * @param string $tag 标签
     * @param  Closure|string $func 匿名函数或类名
     * @return void
     * @author Yaecho 
     */
    public static function add($tag, $func)
    {
        self::$funcs[$tag][] = $func;
    }

    /**
     * 删除tag下所有函数
     *
     * @param string $tag
     * @return void
     * @author Yaecho 
     */
    public static function remove($tag)
    {
        if (array_key_exists($tag, self::$funcs)) {
            unset(self::$funcs[$tag]);
        }
    }

    /**
     * 加载运作tag的索引
     *
     * @param array $map 运作tag的索引
     * @return void
     * @author Yaecho 
     */
    public static function loadMap($map)
    {
        static::$map = $map;
    }

    /**
     * 事件循环
     *
     * @return void
     * @author Yaecho 
     */
    public static function loop()
    {
        while (1) {
            $count = count(static::$map);
            for($i=0;$i<$count;$i++) {
                self::trigger(static::$map[$i]);
            }
        }
    }

    /**
     * 触发标签运行点
     *
     * @param string $tag 标签
     * @return void
     * @author Yaecho 
     */
    protected static function trigger($tag)
    {
        if (!array_key_exists($tag, self::$funcs)) {
            return;
        }
        foreach (self::$funcs[$tag] as $func) {
            // 如果是匿名函数直接调用
            if (is_callable($func)) {
                $func();
            }
            // 如果是字符，判断是不是类，是否实现接口
            if (is_string($func) && class_exists($func)) {
                $class = new $func;
                if ($class instanceof RunInterface) {
                    $class->run();
                }
            }
        }
    }

    /**
     * 保存数据
     *
     * @param string $name
     * @param mixed $value
     * @param bool $no_cover true不覆盖，false覆盖
     * @return void
     * @author Yaecho 
     */
    public static function set($name, $value, $no_cover = true) {
        if (array_key_exists($name, static::$data) && $no_cover) {
            return false;
        }
        static::$data[$name] = $value;
        return true;
    }

    /**
     * 取出数据
     *
     * @param string $name
     * @return void
     * @author Yaecho 
     */
    public static function get($name)
    {
        return static::$data[$name];
    }
}