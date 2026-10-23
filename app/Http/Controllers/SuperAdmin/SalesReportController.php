<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Admin\ManageController;
use App\Services\SalesReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class SalesReportController extends ManageController
{
    protected string $prefix = 'superadmin';

    protected string $layout = 'layouts.super-admin';

    public function index(Request $request, SalesReportService $laporan): View
    {
        $periode = array_key_exists($request->query('periode', ''), SalesReportService::PERIODE)
            ? $request->query('periode')
            : 'bulan';

        try {
            $acuan = $request->filled('tanggal') ? Carbon::parse($request->query('tanggal')) : now();
        } catch (\Throwable) {
            $acuan = now();
        }

        return $this->render('super-admin.reports.index', [
            'laporan' => $laporan->bangun($periode, $acuan),
            'periode' => $periode,
            'acuan' => $acuan,
        ]);
    }
}
