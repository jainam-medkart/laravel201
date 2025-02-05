<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\DraftProduct;
use App\Models\PublishedProduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PublishProductRepository {

    public function getActive($perPage = 15)
    {
        return DraftProduct::with(['category', 'molecules'])
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->paginate($perPage);
    }

    public function getAll($perPage = 15)
    {
        return DraftProduct::with(['category', 'molecules'])
            ->withTrashed()
            ->paginate($perPage);
    }

    public function getById($id) {
        return DraftProduct::with(['category', 'molecules'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $moleculeIds = $data['molecule_ids'] ?? [];
            $categoryId = $data['category_id'] ?? null;

            $draftProduct = DraftProduct::create($data);

            if (!empty($moleculeIds)) {
                $draftProduct->molecules()->attach($moleculeIds);
            }

            if ($categoryId) {
                $draftProduct->category()->associate(Category::findOrFail($categoryId));
                $draftProduct->save();
            }

            return $draftProduct->load(['category', 'molecules']);
        });
    }

    public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $moleculeIds = $data['molecule_ids'] ?? [];
            $categoryId = $data['category_id'] ?? null;

            $draftProduct = DraftProduct::findOrFail($id);
            $draftProduct->update($data);

            if (!empty($moleculeIds)) {
                $draftProduct->molecules()->sync($moleculeIds);
            }

            if ($categoryId) {
                $draftProduct->category()->associate(Category::findOrFail($categoryId));
                $draftProduct->save();
            }

            return $draftProduct->load(['category', 'molecules']);
        });
    }

    public function updatePublishedProduct($id)
    {
        return DB::transaction(function () use ($id) {
            $draftProduct = DraftProduct::with(['category', 'molecules'])->findOrFail($id);

            if ($draftProduct->is_published) {
                $publishedProduct = PublishedProduct::where('draft_product_id', $id)->firstOrFail();
                $publishedProduct->update([
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
                    'updated_by' => $draftProduct->updated_by,
                    'updated_at' => now(),
                ]);

                $publishedProduct->molecules()->sync($draftProduct->molecules->pluck('id')->toArray());
            }

            return $draftProduct;
        });
    }

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
                'is_published' => true,
            ]);

            return $publishedProduct;
        });
    }

    public function softDelete($id)
    {
        $draftProduct = DraftProduct::findOrFail($id);
        $draftProduct->update([
            'is_active' => false,
            'deleted_by' => Auth::id(),
        ]);
        $draftProduct->delete();
        return $draftProduct;
    }

    public function restore($id)
    {
        $draftProduct = DraftProduct::withTrashed()->findOrFail($id);
        $draftProduct->update([
            'is_active' => true,
            'deleted_by' => null,
            'deleted_at' => null,
        ]);
        $draftProduct->restore();
        return $draftProduct;
    }
}