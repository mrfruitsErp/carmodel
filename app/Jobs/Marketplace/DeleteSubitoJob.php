<?php

namespace App\Jobs\Marketplace;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DeleteSubitoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public function __construct(
        public readonly int $listingId,
        public readonly int $tenantId
    ) {}

    public function handle(): void
    {
        Log::info("[DeleteSubitoJob] listing #{$this->listingId} — automazione browser necessaria");
    }
}