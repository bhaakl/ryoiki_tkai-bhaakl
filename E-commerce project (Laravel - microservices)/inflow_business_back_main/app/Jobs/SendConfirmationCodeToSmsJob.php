<?php

namespace App\Jobs;

use App\Services\RabbitMQService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendConfirmationCodeToSmsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected $phone, protected $code)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::debug('SendConfirmationCodeToSmsJob handle');
        $rabbit = new RabbitMQService();
        $rabbit->publishSms($this->phone, $this->code);
    }
}
