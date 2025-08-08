<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNewPasswordJob implements ShouldQueue
{
    use Queueable;

    public function __construct(protected $customer, protected $new_password)
    {
        $this->customer = $this->customer->toArray();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
    }
}
