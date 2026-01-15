<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DetailOrder;
use App\Models\Order;
use App\Models\Tiket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $user = Auth::user() ?? \App\Models\User::first();
        $orders = Order::where('user_id', $user->id)
            ->with('event')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('detailOrders.tiket', 'event');

        return view('orders.show', compact('order'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'event_id' => 'required|exists:events,id',
            'items' => 'required|array|min:1',
            'items.*.tiket_id' => 'required|integer|exists:tikets,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        $user = Auth::user();

        try {
            $order = DB::transaction(function () use ($data, $user) {
                $total = 0;

                foreach ($data['items'] as $item) {
                    $ticket = Tiket::lockForUpdate()->findOrFail($item['tiket_id']);
                    if ($ticket->stok < $item['jumlah']) {
                        throw new \Exception("Stok tidak cukup untuk tipe: {$ticket->tipe}");
                    }
                    $total += ($ticket->harga ?? 0) * $item['jumlah'];
                }

                $order = Order::create([
                    'user_id' => $user->id,
                    'event_id' => $data['event_id'],
                    'order_date' => Carbon::now(),
                    'total_harga' => $total,
                ]);

                foreach ($data['items'] as $item) {
                    $ticket = Tiket::findOrFail($item['tiket_id']);
                    $subtotal = ($ticket->harga ?? 0) * $item['jumlah'];

                    DetailOrder::create([
                        'order_id' => $order->id,
                        'tiket_id' => $ticket->id,
                        'jumlah' => $item['jumlah'],
                        'subtotal_harga' => $subtotal,
                    ]);

                    $ticket->stok = max(0, $ticket->stok - $item['jumlah']);
                    $ticket->save();
                }

                return $order;
            });

            session()->flash('success', 'Pesanan berhasil dibuat.');

            return response()->json([
                'ok' => true,
                'order_id' => $order->id,
                'redirect' => route('orders.index'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
