<?php

namespace App\Console\Commands;

use App\Jobs\RecreateCatalogJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class ConsumeRecreateDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:consume-recreate-d-b';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $connection = new AMQPStreamConnection(
            config('rabbitmq.host'),
            config('rabbitmq.port'),
            config('rabbitmq.user'),
            config('rabbitmq.password'),
            config('rabbitmq.vhost'));
        $channel = $connection->channel();
        $channel->exchange_declare('recreate', 'fanout', false, false, false);

        list($queue_name, ,) = $channel->queue_declare("");

        $channel->queue_bind($queue_name, 'recreate');

        $callback = function ($msg) {
            Log::info('recreating db');
            RecreateCatalogJob::dispatch();
        };

        $channel->basic_consume($queue_name, '', false, true, false, false, $callback);

        try {
            $channel->consume();
        } catch (\Throwable $exception) {
            echo $exception->getMessage();
        }
    }
}
