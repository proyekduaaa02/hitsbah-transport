<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Admin\AdminPaymentController;

class SuperAdminPaymentController extends AdminPaymentController
{
    protected string $prefix = 'superadmin';

    protected string $layout = 'layouts.super-admin';
}
