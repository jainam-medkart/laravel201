<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\DraftProduct;
use App\Models\PublishedProduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PublishedProductRepository {

    // using : redis/redis-stack-server:latest
    // To do : Explore exception when cache

    public function getActive($perPage = 15)
    {
        return PublishedProduct::with(['category', 'molecules'])
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->paginate($perPage);
    }

    public function getAll($perPage = 15)
    {
        return PublishedProduct::with(['category', 'molecules'])
            ->withTrashed()
            ->paginate($perPage);
    }

    public function getById($id) {
        $cacheKey = "published_product_{$id}";

        return Cache::remember($cacheKey, 3600, function () use ($id) {
            return PublishedProduct::with(['category', 'molecules'])->findOrFail($id);
        });
    }

    public function getLastWsCode()
    {
        return PublishedProduct::max('ws_code');
    }

    public function generateWsCode()
    {
        $lastWsCode = $this->getLastWsCode();
        return $lastWsCode ? $lastWsCode + 1 : 100;
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $moleculeIds = $data['molecule_ids'] ?? [];
            $categoryId = $data['category_id'] ?? null;

            // Generate ws_code
            $data['ws_code'] = $this->generateWsCode();

            $publishedProduct = PublishedProduct::create($data);

            if (!empty($moleculeIds)) {
                $publishedProduct->molecules()->attach($moleculeIds);
            }

            if ($categoryId) {
                $publishedProduct->category()->associate(Category::findOrFail($categoryId));
                $publishedProduct->save();
            }

            return $publishedProduct->load(['category', 'molecules']);
        });
    }

    public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $moleculeIds = $data['molecule_ids'] ?? [];
            $categoryId = $data['category_id'] ?? null;

            $publishedProduct = PublishedProduct::findOrFail($id);
            $publishedProduct->update($data);

            if (!empty($moleculeIds)) {
                $publishedProduct->molecules()->sync($moleculeIds);
            }

            if ($categoryId) {
                $publishedProduct->category()->associate(Category::findOrFail($categoryId));
                $publishedProduct->save();
            }

            return $publishedProduct->load(['category', 'molecules']);
        });
    }

    public function updatePublishedProduct($id)
    {
        return DB::transaction(function () use ($id) {
            $draftProduct = DraftProduct::with(['category', 'molecules'])->findOrFail($id);

            if ($draftProduct->is_published) {
                $combinationString = $draftProduct->molecules->pluck('name')->implode('+');
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
                    'combination_string' => $combinationString, // Set combination_string
                ]);

                $publishedProduct->molecules()->sync($draftProduct->molecules->pluck('id')->toArray());
            }

            return $publishedProduct;
        });
    }

    public function publish(DraftProduct $draftProduct, $userId)
    {
        return DB::transaction(function () use ($draftProduct, $userId) {
            // Generate combination_string
            $combinationString = $draftProduct->molecules->pluck('name')->implode('+');

            // Generate ws_code
            $wsCode = $this->generateWsCode();

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
                'combination_string' => $combinationString, // Set combination_string
                'ws_code' => $wsCode . "", // Set ws_code
            ]);

            $publishedProduct->molecules()->attach($draftProduct->molecules->pluck('id')->toArray());

            // dump($publishedProduct);
            // Update the draft product
            $draftProduct->update([
                'is_published' => true,
            ]);

            return $publishedProduct;
        });
    }

    public function softDelete($id)
    {
        $publishedProduct = PublishedProduct::findOrFail($id);
        $publishedProduct->update([
            'is_active' => false,
            'deleted_by' => Auth::id(),
        ]);
        $publishedProduct->delete();
        return $publishedProduct;
    }

    public function restore($id)
    {
        $publishedProduct = PublishedProduct::withTrashed()->findOrFail($id);
        $publishedProduct->update([
            'is_active' => true,
            'deleted_by' => null,
            'deleted_at' => null,
        ]);
        $publishedProduct->restore();
        return $publishedProduct;
    }
}