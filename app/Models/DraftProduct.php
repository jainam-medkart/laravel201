<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DraftProduct extends Model
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
        'is_published',
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
        return $this->belongsToMany(Molecule::class, 'draft_product_molecule');
    }

    public function publishedProduct()
    {
        return $this->belongsTo(PublishedProduct::class, 'published_id');
    }
}
