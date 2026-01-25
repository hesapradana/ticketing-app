<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\View\View;

class HistoriesController extends Controller
{
    /**
     * Menampilkan daftar riwayat pesanan.
     */
    public function index(): View
    {
        $histories = Order::with(['user', 'event'])->latest()->get();

        return view('admin.history.index', compact('histories'));
    }

    /**
     * Menampilkan detail riwayat pesanan.
     */
    public function show(string $history): View
    {
        $order = Order::with(['event', 'detailOrders.tiket.ticketType', 'user', 'paymentMethod'])->findOrFail($history);

        return view('admin.history.show', compact('order'));
    }
}
