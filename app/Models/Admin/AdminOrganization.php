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

    const Fulltext = [
        'name',
        'code'
    ];

    function modelFilter()
    {
        return $this->provideFilter(AdminOrganizationFilter::class);
    }
}