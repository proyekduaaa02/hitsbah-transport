<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Admin\AdminReviewController;

class SuperAdminReviewController extends AdminReviewController
{
    protected string $prefix = 'superadmin';

    protected string $layout = 'layouts.super-admin';
}
