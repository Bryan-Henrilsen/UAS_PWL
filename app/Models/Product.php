<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = ['id'];

    // Membuat relasi dimana produk mempunyai banyak varian
    public function variants() 
    {
        return $this->hasMany(ProductVariant::class);
    }
}
