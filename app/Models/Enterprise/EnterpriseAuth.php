<?php

namespace App\Models\Enterprise;

use App\Models\Model;

/**
 * @mixin IdeHelperEnterpriseAuth
 */
class EnterpriseAuth extends Model
{
    protected $table = 'enterprise_auth';

    const INVITE_ALLOW     = 1;
    const INVITE_NOT_ALLOW = 2;
    const INVITE_ALERT     = 3;
}