<?php

namespace Stas;

class Server
{
    public static function run()
    {
        // $server = stream_socket_server("tcp://0.0.0.0:8000", $errno, $errstr);
        // // if (!$socket) {
        // //     echo "$errstr ($errno)<br />\n";
        // // } else {
        // //     while ($conn = stream_socket_accept($socket, -1)) {
        // //         fwrite($conn, 'The local time is ' . date('n/j/Y g:i a') . "\n");
        // //         while ($data = fread($conn, 2048)) {
        // //             echo $data;
        // //         }
        // //         //fclose($conn);
        // //     }
        // //     fclose($socket);
        // // }


        // $time = 1000 * 365 * 24 * 3600;

        // $base = new \EventBase;
        // $event = new \Event($base, $server, \Event::READ | \Event::PERSIST, function ($socket, $flag, $base) use ($time) {
            
            
        //     $client = stream_socket_accept($socket, -1);
        //     stream_set_blocking($client, false);

        //     $event = new \EventBufferEvent($base, $client, 0, function ($bev) {
        //         var_dump($bev);
        //     });

        //     $event->enable(\Event::READ);
        //     var_dump("接收到客户端连接");
        // }, $base);

        // var_dump($event->add($time));
        // $base->loop();

    }

    /**
     * 进程守护
     *
     * @return void
     * @author Yaecho 
     */
    protected static function daemon()
    {
        self::forkChild();
        //进入子进程并成为session loader
        if (-1 === posix_setsid()) {
            throw new \Exception('setsid fail');
        }
        self::forkChild();

        //设置全局变量
        global $STDOUT, $STDERR;
        //关闭各种描述符 关闭后首次打开的流依次成为 stdout、stderr
        @fclose(STDOUT);
        @fclose(STDERR);
        $STDOUT = fopen('/dev/null', 'a');
        $STDERR = fopen('/dev/null', 'a');
    
        chdir('/');

        umask(0);
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
            echo $pid;
            //退出父进程
            exit(0);
        }
    } 
}
