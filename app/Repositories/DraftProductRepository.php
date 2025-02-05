<?php

namespace App\Repositories;

use App\Jobs\UpdatePublishedProduct as JobsUpdatePublishedProduct;
use App\Models\Category;
use App\Models\DraftProduct;
use App\Models\PublishedProduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use UpdatePublishedProduct;

class DraftProductRepository {

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

    public function updatePublishedProduct($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $draftProduct = $this->update($id, $data);

            if ($draftProduct->is_published) {
                JobsUpdatePublishedProduct::dispatch($draftProduct);
            }

            return $draftProduct;
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