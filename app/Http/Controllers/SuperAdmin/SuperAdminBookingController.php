<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Admin\AdminBookingController;

class SuperAdminBookingController extends AdminBookingController
{
    protected string $prefix = 'superadmin';

    protected string $layout = 'layouts.super-admin';
}
