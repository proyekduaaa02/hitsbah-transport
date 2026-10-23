<?php

namespace App\Http\Controllers\Admin;

use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminReviewController extends ManageController
{
    public function index(Request $request): View
    {
        $daftar = Review::query()
            ->with(['user:id_user,nama', 'produk:id_produk,nama'])
            ->when($request->query('rating'), fn ($q, $r) => $q->where('rating', (int) $r))
            ->when($request->query('balasan') === 'belum', fn ($q) => $q->whereNull('balasan_admin'))
            ->when($request->query('q'), fn ($q, $kata) => $q->where(function ($q) use ($kata) {
                $q->where('komen', 'like', "%{$kata}%")
                    ->orWhereHas('produk', fn ($p) => $p->where('nama', 'like', "%{$kata}%"))
                    ->orWhereHas('user', fn ($u) => $u->where('nama', 'like', "%{$kata}%"));
            }))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return $this->render('manage.reviews.index', ['reviews' => $daftar]);
    }

    public function reply(Request $request, Review $review): RedirectResponse
    {
        $data = $request->validate(['balasan_admin' => ['required', 'string', 'max:1000']], [], ['balasan_admin' => 'balasan']);

        $review->update([
            'balasan_admin' => $data['balasan_admin'],
            'tanggal_balasan' => now(),
        ]);

        return back()->with('success', 'Balasan ulasan berhasil disimpan.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return back()->with('success', 'Ulasan berhasil dihapus.');
    }
}
