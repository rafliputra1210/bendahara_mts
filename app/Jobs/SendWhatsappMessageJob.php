<?php

namespace App\Jobs;

use App\Services\FonnteService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendWhatsappMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $target;
    protected string $message;

    /**
     * Create a new job instance.
     */
    public function __construct(string $target, string $message)
    {
        $this->target = $target;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            FonnteService::sendMessage($this->target, $this->message);
        } catch (\Exception $e) {
            Log::error('SendWhatsappMessageJob Error: ' . $e->getMessage());
        }
    }
}
