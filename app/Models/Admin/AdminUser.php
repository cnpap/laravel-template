<?php

namespace App\Models\Admin;

use App\ModelFilters\Admin\AdminUserFilter;
use App\Models\ModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @mixin IdeHelperAdminUser
 */
class AdminUser extends User
{
    use HasApiTokens, HasFactory, Notifiable, ModelTrait;

    protected $table = 'admin_user';

    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'id'                => 'string',
        'email_verified_at' => 'datetime',
    ];

    function modelFilter()
    {
        return $this->provideFilter(AdminUserFilter::class);
    }

    function position()
    {
        return $this->hasOne(AdminPosition::class, 'id', 'admin_position_id');
    }
}
