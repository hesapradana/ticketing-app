<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'judul',
        'deskripsi',
        'tanggal_waktu',
        'lokasi',
        'kategori_id',
        'gambar',
    ];

    protected $casts = [
        'tanggal_waktu' => 'datetime',
    ];

    public function tikets()
    {
        return $this->hasMany(Tiket::class);
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor: Hitung total stok semua tiket
     */
    public function getTotalStockAttribute(): int
    {
        return $this->tikets->sum('stok');
    }

    /**
     * Cek apakah semua tiket event sudah habis
     */
    public function isSoldOut(): bool
    {
        return $this->total_stock === 0;
    }

    /**
     * Cek apakah stok tiket menipis (kurang dari 5)
     */
    public function isLowStock(): bool
    {
        return $this->total_stock > 0 && $this->total_stock < 5;
    }
}
