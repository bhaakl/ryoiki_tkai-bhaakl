<?php

namespace App\Services;

use App\Data\OrderData;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService
{
    protected $connection;
    protected $channel;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(
            config('rabbitmq.host'),
            config('rabbitmq.port'),
            config('rabbitmq.user'),
            config('rabbitmq.password'),
            config('rabbitmq.vhost'));
        $this->channel = $this->connection->channel();
    }

    public function publishMail($address, $text, $type = 'confirmation_code')
    {
        Log::debug('publishMail', [config('rabbitmq.host'),
            config('rabbitmq.port'),
            config('rabbitmq.user'),
            config('rabbitmq.password'),
            config('rabbitmq.vhost')]);
        $this->channel->queue_declare('mail', false, true, false, false);

        $message = new \stdClass();
        $message->address = $address;
        $message->type = $type;
        $message->text = $text;
        $msg = new AMQPMessage(json_encode($message));
        $this->channel->basic_publish($msg, '', 'mail');

        $this->channel->close();
        $this->connection->close();
    }

    public function publishSms($phone, $code, $type = 'confirmation_code')
    {
        Log::debug('publishSms', [config('rabbitmq.host'),
            config('rabbitmq.port'),
            config('rabbitmq.user'),
            config('rabbitmq.password'),
            config('rabbitmq.vhost')]);
        $this->channel->queue_declare('sms', false, true, false, false);

        $message = new \stdClass();
        $message->phone = $phone;
        $message->type = $type;
        $message->code = $code;
        $msg = new AMQPMessage(json_encode($message));
        $this->channel->basic_publish($msg, '', 'sms');

        $this->channel->close();
        $this->connection->close();
    }

    public function recreateDB()
    {
        $this->channel->exchange_declare('recreate', 'fanout', false, false, false);
        $msg = new AMQPMessage('recreate');
        $this->channel->basic_publish($msg, 'recreate');
    }

    public function createTenant(Tenant $tenant)
    {
        Log::debug('create tenant msg published');
        $this->channel->exchange_declare('tenant', 'fanout', false, false, false);
        $msg = new AMQPMessage($tenant->toJson());
        $this->channel->basic_publish($msg, 'tenant');
    }

    public function orderCreated(Tenant $tenant, OrderData $orderData)
    {
        Log::debug('create order msg published');
        $this->channel->exchange_declare('bonus', 'direct', false, false, false);
        $message = [
            'event' => 'order.created',
            'data' => [
                'tenant' => $tenant,
                'order' => $orderData
            ]
        ];
        $msg = new AMQPMessage(json_encode($message));
        $this->channel->basic_publish($msg, 'bonus');
    }
}
