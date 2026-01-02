<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InboundDetail extends Model
{
    protected $guarded = ['id'];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
