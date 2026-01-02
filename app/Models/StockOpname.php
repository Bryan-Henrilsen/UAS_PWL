<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    protected $guarded = ['id'];
    
    // casting tanggal agar otomatis jadi format date
    protected $casts = [
        'so_date' => 'date',
        'approved_at' => 'datetime',
    ];

    // Membuat relasi siapa yang bisa request
    public function requester()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Membuat relasi detail barang apa aja
    public function details()
    {
        return $this->hasMany(StockOpnameDetail::class, 'so_id');
    }

    // biar bisa tau diapprove oleh siapa
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
