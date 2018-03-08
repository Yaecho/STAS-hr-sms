<?php

namespace Stas;

use Stas\Event;
use Stas\App;

class Server
{
    /**
     * 是否在后台运行
     *
     * @var boolean
     * @author Yaecho 
     */
    protected static $daemonize = false;

    /**
     * 标准输出文件
     *
     * @var string
     * @author Yaecho 
     */
    protected static $stdfile = __DIR__ . '/../../log';

    /**
     * pid值存放文件
     *
     * @var string
     * @author Yaecho 
     */
    protected static $pidFile = __DIR__ . '/../../pid';

    /**
     * 主进程pid
     *
     * @var integer
     * @author Yaecho 
     */
    private static $masterPid = 0;

    /**
     * 程序运行入口
     *
     * @return void
     * @author Yaecho 
     */
    public static function run()
    {
        global $APP_CONFIG;
        static::$daemonize = $APP_CONFIG['daemonize'];
        //解析命令行
        self::parseCommand();
        //开启守护
        self::daemon();
        //保存主进程pid
        self::saveMasterPid();
        //注册信号量
        self::installSignal();
        //应用初始化
        $app = new App();
        $app->init();
        //事件循环
        Event::loop();
    }

    /**
     * 解析命令行
     *
     * @return void
     * @author Yaecho 
     */
    public static function parseCommand()
    {
        global $argv;
        if (!isset($argv[1])) {
            return;
        }
        switch ($argv[1]) {
            case 'stop':
                static::callStop();
                exit(0);
        }

    }

    /**
     * 进程守护
     *
     * @return void
     * @author Yaecho 
     */
    protected static function daemon()
    {
        if (!self::$daemonize) {
            return;
        }
        umask(0);
        self::forkChild();
        //进入子进程并成为session loader
        if (-1 === posix_setsid()) {
            throw new \Exception('setsid fail');
        }
        self::forkChild();
        //重置STD
        self::resetStd();
        chdir('/');
    }

    /**
     * fork，进入子进程
     *
     * @return void
     * @author Yaecho 
     */
    protected static function forkChild()
    {
        $pid = pcntl_fork();
        if (-1 === $pid) {
            throw new \Exception('fork fail');
        } elseif ($pid > 0) {
            //退出父进程
            exit(0);
        }
    }

    /**
     * 重置STDOUT、STDERR
     *
     * @return void
     * @author Yaecho 
     */
    protected static function resetStd()
    {
        //设置全局变量
        global $STDOUT, $STDERR;
        //关闭各种描述符 关闭后首次打开的流依次成为 stdout、stderr
        @fclose(STDOUT);
        @fclose(STDERR);
        $STDOUT = fopen(self::$stdfile, 'a');
        $STDERR = fopen(self::$stdfile, 'a');
    }

    /**
     * 保存主进程pid
     *
     * @return void
     * @author Yaecho 
     */
    protected static function saveMasterPid()
    {
        static::$masterPid = posix_getpid();
        file_put_contents(static::$pidFile, static::$masterPid);
    }

    /**
     * 停止
     *
     * @return void
     * @author Yaecho 
     */
    protected static function callStop()
    {
        $master_pid = is_file(static::$pidFile) ? file_get_contents(static::$pidFile) : 0;
        $master_is_alive = $master_pid && @posix_kill($master_pid, 0) && posix_getpid() != $master_pid;
        if (!$master_is_alive) {
            echo 'SMS SYSTEM NOT RUNIND';
            return;
        }
        posix_kill($master_pid, SIGINT);
    }

    /**
     * Install signal handler.
     *
     * @return void
     */
    protected static function installSignal()
    {
        // stop
        pcntl_signal(SIGINT, function () {
            Event::add('end', function () {
                exit(0);
            });
        }, false);
        // 添加信号触发
        Event::add('end', function() {
            //信号分发
            pcntl_signal_dispatch();
        });
    }
}
