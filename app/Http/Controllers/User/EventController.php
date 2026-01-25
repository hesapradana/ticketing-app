<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\PaymentMethod;
use Illuminate\View\View;

class EventController extends Controller
{
    /**
     * Menampilkan detail event.
     */
    public function show(Event $event): View
    {
        $event->load(['tikets.ticketType', 'kategori', 'user']);
        $paymentMethods = PaymentMethod::all();

        return view('events.show', compact('event', 'paymentMethods'));
    }
}
