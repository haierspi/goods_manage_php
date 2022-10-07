<?php

namespace ff\rpcclient;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class rpcClient
{
    private $connection;
    private $channel;
    private $callback_queue;
    private $response;
    private $corr_id;

    public $rpcServer;///rpc服务器地址

    public function __construct($recServer)
    {
        $this->rpcServer = $recServer;
        $this->connection = new AMQPStreamConnection('47.241.25.1', 5672, 'admin', 'iAPQ1NMPxt5pLjc5S');

        $this->channel = $this->connection->channel();
        list($this->callback_queue, ,) = $this->channel->queue_declare(
            "",
            false,
            false,
            true,
            false
        );
        $this->channel->basic_consume(
            $this->callback_queue,
            '',
            false,
            true,
            false,
            false,
            array(
                $this,
                'onResponse'
            )
        );
    }

    public function onResponse($rep)
    {
        if ($rep->get('correlation_id') == $this->corr_id) {
            $this->response = $rep->body;
        }
    }

    public function call($n)
    {
        $this->response = null;
        $this->corr_id = uniqid();

        $msg = new AMQPMessage(
            (string)$n,
            array(
                'correlation_id' => $this->corr_id,
                'reply_to' => $this->callback_queue
            )
        );
        $this->channel->basic_publish($msg, '', $this->rpcServer);
        while (!$this->response) {
            $this->channel->wait($allowed_methods = null, $non_blocking = false, $timeout = 5);
        }
        return $this->response;
    }
}


