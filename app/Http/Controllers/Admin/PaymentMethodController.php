<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $paymentMethods = PaymentMethod::all();

        return view('admin.payment-method.index', compact('paymentMethods'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'nama' => 'required|string|max:255|unique:payment_methods,nama',
        ]);

        PaymentMethod::create([
            'nama' => $payload['nama'],
        ]);

        return redirect()->route('admin.payment-methods.index')->with('success', 'Metode pembayaran berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $payload = $request->validate([
            'nama' => 'required|string|max:255|unique:payment_methods,nama,' . $id,
        ]);

        $paymentMethod = PaymentMethod::findOrFail($id);
        $paymentMethod->nama = $payload['nama'];
        $paymentMethod->save();

        return redirect()->route('admin.payment-methods.index')->with('success', 'Metode pembayaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        PaymentMethod::destroy($id);

        return redirect()->route('admin.payment-methods.index')->with('success', 'Metode pembayaran berhasil dihapus.');
    }
}
