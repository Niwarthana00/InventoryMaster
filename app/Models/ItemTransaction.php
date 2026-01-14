<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemTransaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Relationship: Transaction belongs to an Item
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // Relationship: Transaction belongs to a User (who performed it)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
