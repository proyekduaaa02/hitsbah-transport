<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Admin\AdminOrderController;

class SuperAdminOrderController extends AdminOrderController
{
    protected string $prefix = 'superadmin';

    protected string $layout = 'layouts.super-admin';
}
