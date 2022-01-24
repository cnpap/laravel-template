<?php

namespace App\Models\Admin;

use App\ModelFilters\Admin\AdminOrganizationFilter;
use App\Models\Model;

/**
 * @mixin IdeHelperAdminOrganization
 */
class AdminOrganization extends Model
{
    protected $table = 'admin_organization';

    function modelFilter()
    {
        return $this->provideFilter(AdminOrganizationFilter::class);
    }
}