<?php

namespace App\Jobs;

use App\Models\DraftProduct;
use App\Repositories\PublishProductRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
    public function handle(PublishProductRepository $publishProductRepository)
    {
        try {
            $publishProductRepository->publish($this->draftProduct, $this->userId);
        } catch (\Exception $e) {
            dd($e);
        }
    }
}