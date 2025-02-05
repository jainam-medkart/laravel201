<?php

namespace App\Jobs;

use App\Models\DraftProduct;
use App\Repositories\PublishedProductRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PublishDraftProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $draftProduct;
    protected $userId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(DraftProduct $draftProduct, $userId)
    {
        $this->draftProduct = $draftProduct;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(PublishedProductRepository $publishProductRepository)
    {
        try {
            $publishProductRepository->publish($this->draftProduct, $this->userId);
        } catch (\Exception $e) {
            Log::error('Failed to publish draft product', [
                'draft_product_id' => $this->draftProduct->id,
                'user_id' => $this->userId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}