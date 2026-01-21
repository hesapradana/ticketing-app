<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Kategori::all();

        $eventsQuery = Event::with('tikets')
            ->withMin('tikets', 'harga')
            ->orderBy('tanggal_waktu', 'asc');

        if ($request->has('kategori') && $request->kategori) {
            $eventsQuery->where('kategori_id', $request->kategori);
        }

        $events = $eventsQuery->get();

        return view('home', compact('events', 'categories'));
    }
}
