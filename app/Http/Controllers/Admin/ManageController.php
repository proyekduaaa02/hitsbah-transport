<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Basis untuk controller pengelolaan yang dipakai bersama Admin dan Super Admin.
 * Controller Super Admin mewarisi controller Admin dan hanya mengganti $prefix / $layout,
 * sehingga logika tidak ditulis dua kali tetapi route, middleware, dan tampilan tetap terpisah.
 */
abstract class ManageController extends Controller
{
    /** Prefix nama route: "admin" atau "superadmin". */
    protected string $prefix = 'admin';

    /** Layout Blade yang dipakai. */
    protected string $layout = 'layouts.admin';

    protected function render(string $view, array $data = []): View
    {
        return view($view, $data + ['prefix' => $this->prefix, 'layout' => $this->layout]);
    }

    protected function ke(string $nama, mixed $parameter = []): string
    {
        return route($this->prefix.'.'.$nama, $parameter);
    }
}
