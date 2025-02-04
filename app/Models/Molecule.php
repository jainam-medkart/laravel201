<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Molecule extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'is_active', 'created_by', 'updated_by'];

    protected $hidden = ['created_by', 'updated_by', 'updated_at', 'created_at' , 'id'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
