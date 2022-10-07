<?php

namespace ff\mq;

use ff;
use ff\base\Component;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Connection\Heartbeat\PCNTLHeartbeatSender;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;

class rabbitMq extends Component
{
    public $connection; //connect

    protected $channel; //chanel

    protected $exchange = 'router';

    protected $queue = 'msg';

    protected $consumerTag = 'consumer';

    protected $config;

    public function __construct($config)
    {
        $this->config = $config;

    }

    public function init()
    {
        $this->connection = new AMQPStreamConnection($this->config['host'], $this->config['port'], $this->config['user'], $this->config['pass'], $vhost = '/',
            $insist = false,
            $login_method = 'AMQPLAIN',
            $login_response = null,
            $locale = 'en_US',
            $connection_timeout = 30,
            $read_write_timeout = 60.0,
            $context = null,
            $keepalive = true,
            $heartbeat = 2,
            $channel_rpc_timeout = 0.0,
            $ssl_protocol = null
        );



        $this->channel = $this->connection->channel();

        // $this->declare();

        // $this->bind();
    }

    function declare () {

        $this->quequeDeclare();

        $this->exchangeDeclare();

    }

    public function queQueDeclare()
    {
        $this->channel->queue_declare($this->queue, false, true, false, false);
    }

    public function queQueDeclareKey($queueKey)
    {
        $this->channel->queue_declare($queueKey, false, true, false, false);
    }

    public function exchangeDeclare()
    {
        $this->channel->exchange_declare($this->exchange, AMQPExchangeType::DIRECT, false, true, false);
    }

    public function bind()
    {
        $this->channel->queue_bind($this->queue, $this->exchange);
    }

    public function bindKey($queueKey)
    {
        $this->channel->queue_bind($queueKey, $this->exchange, $queueKey);
    }

    public function close()
    {
        $this->channel->close();
        $this->connection->close();
    }

    public function basic_publish($messageBody, $routing_key)
    {




        $message = new AMQPMessage($messageBody, array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));

        $this->channel->basic_publish($message, $this->exchange, $routing_key);

        $this->close();
    }

    public function basic_consume($callback, $queQue)
    {
        $this->queQueDeclareKey($queQue);

        $this->exchangeDeclare();

        $this->bindKey($queQue);

        $this->channel->basic_consume($queQue, $this->consumerTag, false, false, false, false, $callback);
        $this->registerShutDown();

    }

    public function registerShutDown()
    {
        register_shutdown_function(function ($channel, $connection) {
            $channel->close();
            $connection->close();
        }, $this->channel, $this->connection);
    }

    public function loop()
    {
        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

}
