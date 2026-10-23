<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Admin\AdminServiceController;

class SuperAdminServiceController extends AdminServiceController
{
    protected string $prefix = 'superadmin';

    protected string $layout = 'layouts.super-admin';
}
