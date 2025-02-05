<?php

namespace App\Repositories;

use App\Constants\DraftProductStatus;
use App\Models\DraftProduct;
use App\Models\PublishedProduct;
use Illuminate\Support\Facades\DB;

class PublishProductRepository
{
    public function publish(DraftProduct $draftProduct, $userId)
    {
        return DB::transaction(function () use ($draftProduct, $userId) {
            $publishedProduct = PublishedProduct::create([
                'name' => $draftProduct->name,
                'description' => $draftProduct->description,
                'manufacturer' => $draftProduct->manufacturer,
                'mrp' => $draftProduct->mrp,
                'is_active' => $draftProduct->is_active,
                'is_banned' => $draftProduct->is_banned,
                'is_assured' => $draftProduct->is_assured,
                'is_discountinued' => $draftProduct->is_discountinued,
                'is_refrigerated' => $draftProduct->is_refrigerated,
                'category_id' => $draftProduct->category_id,
                'created_by' => $draftProduct->created_by,
                'updated_by' => $draftProduct->updated_by,
                'deleted_by' => $draftProduct->deleted_by,
                'draft_product_id' => $draftProduct->id,
                'published_by' => $userId,
                'published_at' => now(),
            ]);

            $publishedProduct->molecules()->attach($draftProduct->molecules->pluck('id')->toArray());
            
            // Update the draft product
            $draftProduct->update([
                'status' => DraftProductStatus::APPROVED,
                'is_published' => true,
            ]);

            return $publishedProduct;
        });
    }

    
    
}