<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Kategori;
use App\Models\TicketType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    /**
     * Menampilkan daftar event.
     */
    public function index(): View
    {
        $events = Event::with('kategori')->get();

        return view('admin.event.index', compact('events'));
    }

    /**
     * Menampilkan form untuk membuat event baru.
     */
    public function create(): View
    {
        $categories = Kategori::all();

        return view('admin.event.create', compact('categories'));
    }

    /**
     * Menyimpan event baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'tanggal_waktu' => 'required|date',
            'lokasi' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategoris,id',
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            if (!is_dir(public_path('images/events'))) {
                mkdir(public_path('images/events'), 0755, true);
            }

            $imageName = time() . '.' . $request->gambar->extension();
            $request->gambar->move(public_path('images/events'), $imageName);
            $validatedData['gambar'] = $imageName;
        }

        $validatedData['user_id'] = auth()->id();

        Event::create($validatedData);

        return redirect()->route('admin.events.index')->with('success', 'Event berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail event.
     */
    public function show(string $id): View
    {
        $event = Event::with('kategori', 'tikets.ticketType')->findOrFail($id);
        $categories = Kategori::all();
        $ticketTypes = TicketType::all();
        $tickets = $event->tikets;

        return view('admin.event.show', compact('event', 'categories', 'tickets', 'ticketTypes'));
    }

    /**
     * Menampilkan form untuk mengedit event.
     */
    public function edit(string $id): View
    {
        $event = Event::findOrFail($id);
        $categories = Kategori::all();

        return view('admin.event.edit', compact('event', 'categories'));
    }

    /**
     * Memperbarui event.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        try {
            $event = Event::findOrFail($id);

            $validatedData = $request->validate([
                'judul' => 'required|string|max:255',
                'deskripsi' => 'required|string',
                'tanggal_waktu' => 'required|date',
                'lokasi' => 'required|string|max:255',
                'kategori_id' => 'required|exists:kategoris,id',
                'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if ($request->hasFile('gambar')) {
                if (!is_dir(public_path('images/events'))) {
                    mkdir(public_path('images/events'), 0755, true);
                }

                $imageName = time() . '.' . $request->gambar->extension();
                $request->gambar->move(public_path('images/events'), $imageName);
                $validatedData['gambar'] = $imageName;
            }

            $event->update($validatedData);

            return redirect()->route('admin.events.index')->with('success', 'Event berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui event: ' . $e->getMessage()]);
        }
    }

    /**
     * Menghapus event.
     */
    public function destroy(string $id): RedirectResponse
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return redirect()->route('admin.events.index')->with('success', 'Event berhasil dihapus.');
    }
}
