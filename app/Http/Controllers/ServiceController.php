<?php

namespace App\Http\Controllers;

use App\Models\InstallationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(Request $request): View
    {
        return view('jasa.index', [
            'jasa' => InstallationService::query()
                ->when($request->query('q'), fn ($q, $kata) => $q->where('nama', 'like', "%{$kata}%"))
                ->orderBy('harga')
                ->paginate(9)
                ->withQueryString(),
        ]);
    }

    public function show(InstallationService $jasa): View
    {
        return view('jasa.show', [
            'jasa' => $jasa,
            'lainnya' => InstallationService::query()->where('id_services', '!=', $jasa->id_services)->take(3)->get(),
        ]);
    }
}
