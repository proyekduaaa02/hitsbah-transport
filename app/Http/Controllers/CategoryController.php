<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /** Halaman kategori memakai tampilan katalog yang sama, terfilter kategori. */
    public function show(Request $request, Kategori $kategori): View
    {
        return app(ProductController::class)->index($request, $kategori);
    }
}
