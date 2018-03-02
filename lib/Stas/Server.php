<?php

namespace Stas;

class Server
{
    protected static $daemonize = true;

    protected static $stdfile = '/dev/null';

    private static $masterPid = 0;

    protected static $pidFile = __DIR__ . '/../pid';

    public static function run()
    {
        //开启守护
        self::daemon();
        self::saveMasterPid();
        while(1) {
            sleep(10);
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
}
