<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutboundDetail extends Model
{
    protected $guarded = ['id'];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
