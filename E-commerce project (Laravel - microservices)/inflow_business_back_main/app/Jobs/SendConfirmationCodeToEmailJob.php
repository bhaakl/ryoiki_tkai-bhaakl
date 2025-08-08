<?php

namespace App\Jobs;

use App\Models\Customer;
use App\Services\RabbitMQService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendConfirmationCodeToEmailJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Customer $customer, protected $code)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::debug('SendConfirmationCodeToEmailJob handle');
        $rabbit = new RabbitMQService();
        $rabbit->publishMail($this->customer->email, $this->code);
    }
}
