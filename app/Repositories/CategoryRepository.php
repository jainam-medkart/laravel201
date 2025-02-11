<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository {

    public function getActive()
    {
        return Category::where('is_active', true)->whereNull('deleted_at')->get();
    }

    public function getAll()
    {
        return Category::withTrashed()->get();
    }

    public function find($id)
    {
        return Category::findOrFail($id);
    }

    public function create(array $data)
    {
        return Category::create($data);
    }

    public function update($id, array $data)
    {
        $category = Category::findOrFail($id);
        $category->update($data);
        return $category;
    }

    public function softDelete($id)
    {
        $category = Category::findOrFail($id);
        $category->update([
            'is_active' => false,
            'deleted_by' => auth()->id(),
        ]);
        $category->delete();
        return $category;
    }

    public function restore($id)
    {
        $category = Category::withTrashed()->findOrFail($id);
        $category->update([
            'is_active' => true,
            'deleted_by' => null,
            'deleted_at' => null,
        ]);
        $category->restore();
        return $category;
    }
}