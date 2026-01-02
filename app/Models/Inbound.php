<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inbound extends Model
{
    protected $guarded = ['id'];

    // casting tanggal supaya otomatis menjadi format date
    protected $casts = [
        'inbound_date' => 'date',
        'approved_at' => 'datetime',
    ];

    // Membaut realsi siapa yang bisa request
    public function requester()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Membuat relasi detail barangnya/pembeliannya apa aja
    public function details()
    {
        return $this->hasMany(InboundDetail::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
