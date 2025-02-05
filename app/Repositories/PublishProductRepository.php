<?php

namespace App\Repositories;

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
                'is_published' => $draftProduct->is_published,
                'status' => $draftProduct->status,
                'category_id' => $draftProduct->category_id,
                'created_by' => $draftProduct->created_by,
                'updated_by' => $draftProduct->updated_by,
                'deleted_by' => $draftProduct->deleted_by,
                'draft_product_id' => $draftProduct->id,
                'published_by' => $userId,
            ]);

            $publishedProduct->molecules()->attach($draftProduct->molecules->pluck('id')->toArray());

            return $publishedProduct;
        });
    }

    
}