<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TicketType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketTypeController extends Controller
{
    /**
     * Menampilkan daftar tipe tiket.
     */
    public function index(): View
    {
        $ticketTypes = TicketType::all();

        return view('admin.ticket-type.index', compact('ticketTypes'));
    }

    /**
     * Menyimpan tipe tiket baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'nama' => 'required|string|max:255|unique:ticket_types,nama',
        ]);

        TicketType::create([
            'nama' => $payload['nama'],
        ]);

        return redirect()->route('admin.ticket-types.index')->with('success', 'Tipe tiket berhasil ditambahkan.');
    }

    /**
     * Memperbarui tipe tiket.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $payload = $request->validate([
            'nama' => 'required|string|max:255|unique:ticket_types,nama,' . $id,
        ]);

        $ticketType = TicketType::findOrFail($id);
        $ticketType->nama = $payload['nama'];
        $ticketType->save();

        return redirect()->route('admin.ticket-types.index')->with('success', 'Tipe tiket berhasil diperbarui.');
    }

    /**
     * Menghapus tipe tiket.
     */
    public function destroy(string $id): RedirectResponse
    {
        TicketType::destroy($id);

        return redirect()->route('admin.ticket-types.index')->with('success', 'Tipe tiket berhasil dihapus.');
    }
}
