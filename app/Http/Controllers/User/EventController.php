<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\View\View;

class EventController extends Controller
{
    public function show(Event $event): View
    {
        $event->load(['tikets', 'kategori', 'user']);

        return view('events.show', compact('event'));
    }
}
