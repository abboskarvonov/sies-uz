<?php

namespace App\Jobs;

use App\Models\Page;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class IncrementPageViews implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $pageId
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Direct DB query - tezroq va model event trigger qilmaydi
        DB::table('pages')
            ->where('id', $this->pageId)
            ->increment('views');
    }
}
