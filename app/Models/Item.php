<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $guarded = []; // Allow mass assignment for simplicity

    // Relationship with transactions
    public function transactions()
    {
        return $this->hasMany(ItemTransaction::class)->latest(); // Latest transactions first
    }
}
