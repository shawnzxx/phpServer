<?php

class Index
{
    public $redis;
    function  __construct()
    {
        $redis = new Redis();
        $redis->connect('127.0.0.1',6379);
        $this->redis = $redis;
    }

    public function home($request)
    {
        $get = isset($request->get) ? $request->get : [];

        //@TODO 业务代码

        $result = "<h1>你好，Swoole。</h1>";
        $result.= "GET参数：".json_encode($get);
        return $result;
    }

    public function testSeckill($request)
    {
        $worker_num = 5;

        //https://www.swoole.co.uk/docs/modules/swoole-process-push
        for ($i = 0; $i < $worker_num; $i++) {
            $process = new swoole_process([$this, 'callback_function'], false);
            $pid = $process->start();
            $workers[$pid] = $process;
        }
        return "[OK]";
    }

    public function callback_function(swoole_process $worker)
    {
        while (true){
            try {
                //echo "Child process started, PID=".$worker->pid."\n";
                $kucun = $this->redis->decr("mykey");
                if($kucun >= 0){
                    //抢到号的入队列
                    echo "workerId:$worker->pid get number: $kucun".PHP_EOL;
                }
                else{
                    echo "workerId:$worker->pid out of stock".PHP_EOL;
                    $worker->exit(0);
                    break;
                }
                sleep(2);
            }
            catch (Exception $e){
                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }
        }
    }

    function __destruct()
    {
        // TODO: Implement __destruct() method.
        $this->redis->close();
    }
}