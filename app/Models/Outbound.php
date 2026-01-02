<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Outbound extends Model
{
    protected $guarded = ['id'];

    // casting tanggal supaya otomatis jadi format date
    protected $casts = [
        'outbound_date' => 'date',
        'approved_at' => 'datetime',
    ];

    // Membuat relasi siapa yang bisa request
    public function requester()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi detail penjualan/transaksinya apa aja
    public function details()
    {
        return $this->hasMany(OutboundDetail::class);
    }

    // Supaya tau siapa yang mengapprove request outboundnya
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
