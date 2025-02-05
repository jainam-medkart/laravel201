<?php

namespace App\Jobs;

use App\Models\DraftProduct;
use App\Repositories\PublishProductRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdatePublishedProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $draftProduct;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(DraftProduct $draftProduct)
    {
        $this->draftProduct = $draftProduct;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(PublishProductRepository $publishProductRepository)
    {
        try {
            $publishProductRepository->updatePublishedProduct($this->draftProduct->id, $this->draftProduct->toArray());
        } catch (\Exception $e) {
            Log::error('Failed to update published product', [
                'draft_product_id' => $this->draftProduct->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}