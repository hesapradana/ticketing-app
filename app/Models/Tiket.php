<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tiket extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'ticket_type_id',
        'harga',
        'stok',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }

    public function detailOrders()
    {
        return $this->hasMany(DetailOrder::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'detail_orders')
            ->withPivot('jumlah', 'subtotal_harga');
    }

    /**
     * Cek apakah tiket sudah habis
     */
    public function isSoldOut(): bool
    {
        return $this->stok === 0;
    }

    /**
     * Cek apakah stok tiket menipis (kurang dari 5)
     */
    public function isLowStock(): bool
    {
        return $this->stok > 0 && $this->stok < 5;
    }
}
