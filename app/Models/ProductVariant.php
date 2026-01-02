<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $guarded = ['id'];

    // Membuat relasi dimana varian miliknya 1 produk
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
