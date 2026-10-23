<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ServiceRequest;
use App\Models\InstallationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminServiceController extends ManageController
{
    public function index(Request $request): View
    {
        $daftar = InstallationService::query()
            ->withCount('bookings')
            ->when($request->query('q'), fn ($q, $kata) => $q->where('nama', 'like', "%{$kata}%"))
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return $this->render('manage.services.index', ['services' => $daftar]);
    }

    public function store(ServiceRequest $request): RedirectResponse
    {
        InstallationService::create($request->validated());

        return redirect($this->ke('services.index'))->with('success', 'Data jasa pemasangan berhasil ditambahkan.');
    }

    public function update(ServiceRequest $request, InstallationService $service): RedirectResponse
    {
        $service->update($request->validated());

        return redirect($this->ke('services.index'))->with('success', 'Data jasa pemasangan berhasil diperbarui.');
    }

    public function destroy(InstallationService $service): RedirectResponse
    {
        if ($service->bookings()->exists()) {
            return back()->with('error', 'Jasa tidak dapat dihapus karena sudah memiliki riwayat booking.');
        }

        $service->delete();

        return back()->with('success', 'Data jasa pemasangan berhasil dihapus.');
    }
}
