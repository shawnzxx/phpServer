<?php

class Index
{
    const RedisKey = "LimitGift";
    public $redis;

    function __construct()
    {
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
        $this->redis = $redis;
    }

    //之前的axgrow业务用国内的AMS，明文是913997639d4fb395a522872c52fe236e， encode后是 "!@*|encryption|*@!IsearPdvfinHdVp/Pg6hQ9JqKIRgosm9e7TXifWCTiZFpOJICn518B9jYS6qWFFA"
    //后来专区海外AMS平台，新的应用ID下的明文是410197d1e562034cb440572138d24af9, encode后是"!@*|encryption|*@!1qGQn8MubH/pcs2rPlG+cW5AYcJM0hhfHmxDoDutAOclisPXes5mxMp5H/dOWojg"
    public function testEncrypt($request){
        $get = isset($request->get) ? $request->get : [];
        $params = $get;
        if(empty($params["token"])){
            return "none token provided";
        }
        $str = $params["token"];
        $a = openssl_encrypt($str, 'AES-256-CBC', '!@*||*@!',0,'@*|encryption|*@');
        $secret="!@*|encryption|*@!".$a;
        var_dump($secret);
        return $secret;
    }

    public function testRedis($request)
    {
        try {
            $get = isset($request->get) ? $request->get : [];
            $params = $get;
            var_dump(is_numeric($params["openId"]));
            $result = "";
            $count = $this->redis->decr(self::RedisKey);
            if ($count >= 0) {
                $result .= self::RedisKey . " still have $count item left" . PHP_EOL;
                $result .= "GET parameters：" . json_encode($get);
                return $result;
            } else {
                $result .= self::RedisKey . " out-of-stock, current stock: $count" . PHP_EOL;
                return $result;
            }
        } catch (Throwable $ex) {
            var_dump($ex);
        }
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
        while (true) {
            try {
                //echo "Child process started, PID=".$worker->pid."\n";
                $kucun = $this->redis->decr("mykey");
                if ($kucun >= 0) {
                    //抢到号的入队列
                    echo "workerId:$worker->pid get number: $kucun" . PHP_EOL;
                } else {
                    echo "workerId:$worker->pid out of stock" . PHP_EOL;
                    $worker->exit(0);
                    break;
                }
                sleep(2);
            } catch (Throwable $exc) {
                var_dump($exc);
                $this->app->log->error($exc->getMessage() . ';file:' . $exc->getFile() . ';line:' . $exc->getLine());
            }
        }
    }

    function __destruct()
    {
        // TODO: Implement __destruct() method.
        $this->redis->close();
    }
}