<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PublishedProduct extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'manufacturer',
        'mrp',
        'is_active',
        'is_banned',
        'is_assured',
        'is_discountinued',
        'is_refrigerated',
        'is_published',
        'status',
        'category_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'published_by',
        'draft_product_id'
    ];

    protected $hidden = [
        'created_by',
        'updated_by',
        'deleted_by',
        'updated_at',
        'created_at',
        'deleted_at'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function molecules()
    {
        return $this->belongsToMany(Molecule::class, 'published_product_molecule');
    }

    public function draftProduct()
    {
        return $this->belongsTo(DraftProduct::class);
    }

    public function publisher()
    {
        return $this->belongsTo(User::class, 'published_by');
    }
}
