<?php

use ff\helpers\Dumper;
use ff\mq\rabbitMq as RabbitMq;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPRuntimeException;

function viewfile($viewfile)
{
    $view = ff::createObject('ff\base\View');
    $view->cache($viewfile);
    return $view->draw();
}

function viewAssign($varName, $varValue)
{
    $view = ff::createObject('ff\base\View');
    return $view->assign($varName, $varValue);
}

/**
 * RabbitMQ 生产端
 *
 * @param string $message  消息
 * @param string $routeKey 路由key
 * @return void
 * @Author HaierSpi haierspi@qq.com
 * @DateTime 2020-11-27
 */

function rabbitMqPublish($message, $routeKey)
{
    $rabbitMq = ff::createObject('ff\mq\rabbitMq');
    $rabbitMq->init();

    return $rabbitMq->basic_publish($message, $routeKey);
}

/**
 * RabbitMQ 消费端
 *
 * @param callable $consumeFunc function($message){} 闭包函数
 * @param string $queueName 队列名
 * @return loopNotEnd OR exitError
 * @Author HaierSpi haierspi@qq.com
 * @DateTime 2020-11-27
 */
function rabbitMqConsume(callable $callback, $queueName, int $wokerNum = 1, $callback_init = null)
{
    cliddl('RabbitMqConsume: ' . $queueName);
    // if ($wokerNum == 1) {

    if ($wokerNum == 1) {
        cliddl("Worker Master One");

        rabbitMqConsumeWorker($callback, $queueName, 0, $callback_init);
    } else {

        $pool = new \Swoole\Process\Pool($wokerNum, SWOOLE_IPC_UNIXSOCK, 0, true);

        $pool->on('workerStart', function (\Swoole\Process\Pool $pool, int $workerId) use ($callback, $queueName, $callback_init) {

            $pool->getProcess()->useQueue(1);
            $process = $pool->getProcess($workerId);
            $processMain = $pool->getProcess(0);

            cli_set_process_title(\ff::$app->router->actionMethod . ' > Mq:' . $queueName . ' > ' . $workerId);
            cliddl("Worker Start Id: {$workerId} Pid: {$process->pid}");

            rabbitMqConsumeWorker($callback, $queueName, $workerId, $callback_init);
        });
        $pool->start();
    }
}

function rabbitMqConsumeWorker($callback, $queueName, $workerId, $callback_init = null)
{
    while (true) {
        try {

            if ($callback_init) {
                cliddl("workerId:{$workerId} callback_init");
                $callback_init($workerId);
                cliddl("workerId:{$workerId} callback_init OK");
            }

            $rabbitMq = ff::createObject('ff\mq\rabbitMq');
            $rabbitMq->init();

            $rabbitMq->basic_consume(function ($message) use ($callback, $workerId) {
                $message->ack();

                $callback($message->body, $workerId);

                // Send a message with the string "quit" to cancel the consumer.
                if ($message->body === 'quit') {
                    $message->getChannel()->basic_cancel($message->getConsumerTag());
                }
            }, $queueName);

            $rabbitMq->loop();
        } catch (AMQPRuntimeException $e) {
            usleep(100);
            $rabbitMq->close();
            cliddl("workerId:{$workerId} " . $e->getMessage() . PHP_EOL);
            throw  $e;
        } catch (\RuntimeException $e) {
            usleep(100);
            $rabbitMq->close();
            cliddl("workerId:{$workerId} Runtime exception " . PHP_EOL);
            throw  $e;
        } catch (\ErrorException $e) {
            usleep(100);
            $rabbitMq->close();
            cliddl("workerId:{$workerId} Error exception " . PHP_EOL);
            throw  $e;
        }
    }
}

if (!function_exists('dd')) {
    /**
     * Dump the passed variables and end the script.
     *
     * @param  mixed
     * @return void
     */
    function dd(...$args)
    {
        foreach ($args as $x) {
            (new Dumper)->dump($x);
        }
        die(1);
    }
}

if (!function_exists('ddl')) {
    /**
     * Dump the passed variables and end the script.
     *
     * @param  mixed
     * @return void
     */
    function ddl(...$args)
    {
        foreach ($args as $x) {
            (new Dumper)->dump($x);
        }
    }
}

if (!function_exists('cliddl')) {
    /**
     * Dump the passed variables and end the script.
     *
     * @param  mixed
     * @return void
     */
    function cliddl(...$args)
    {
        if (constant('SYSTEM_RUN_MODE') == 'cli') {

            foreach ($args as $arg) {
                if (defined('SYSTEM_CLI_RUNLOG')) {
                    if (is_string($arg)) {
                        $toArg = $arg;
                    } else {
                        $toArg = json_encode($arg, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                    }
                    $content = date('Y-m-d H:i:s') . "  " . $toArg . "\n";
                    $file = SYSTEM_RUNTIME_PATH . '/runlog/' . str_replace('/', '_', \ff::$app->router->actionMethod) . '.log';
                    file_put_contents($file, $content, FILE_APPEND);
                }

                (new Dumper)->dump($arg);
            }
        }
    }
}

if (!function_exists('ddsql')) {
    /**
     * Dump the passed variables and end the script.
     *
     * @param  mixed
     * @return void
     */
    function ddsql($dbKey = null)
    {
        if (is_null($dbKey)) {
            $dasql = \DB::getQueryLog();
        } else {
            $dasql = \DB::connection($dbKey)->getQueryLog();
        }

        foreach ($dasql as $one) {
            $one['query'] = str_replace(constant('SQL_TAIL'), '', $one['query']);
            $sql[] = vsprintf(str_replace('?', "'%s'", $one['query']), $one['bindings']) . constant('SQL_TAIL');
        }
        dd($dasql, $sql);
    }

    function ddsqlr($dbKey = null)
    {
        $dasql = \DB::getQueryLog();

        $sql = [];
        if ($dasql) {
            foreach ($dasql as $one) {
                $one['query'] = str_replace(constant('SQL_TAIL'), '', $one['query']);
                $sql[] = vsprintf(str_replace(['%', '?'], ['%%', "'%s'"], $one['query']), $one['bindings']) . constant('SQL_TAIL');
            }
        }

        return $sql;
    }
}

if (!function_exists('ddsqlf')) {
    /**
     * Dump the passed variables and end the script.
     *
     * @param  mixed
     * @return void
     */
    function ddsqlf($dbKey = null)
    {

        if (is_null($dbKey)) {
            $dasql = \DB::getQueryLog();
        } else {
            $dasql = \DB::connection($dbKey)->getQueryLog();
        }
        foreach ($dasql as $one) {
            $sql[] = vsprintf(str_replace('?', "'%s'", $one['query']), $one['bindings']);
        }
        dd($sql);
    }
}

if (!function_exists('dda')) {
    /**
     * Dump the passed array variables and end the script.
     *
     * @param  mixed
     * @return void
     */
    function dda(...$args)
    {
        foreach ($args as $x) {
            (new Dumper)->dump($x->toArray());
        }
        die(1);
    }
}
