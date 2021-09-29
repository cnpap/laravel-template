<?php

namespace App\Models\Admin;

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

    const MAN   = 1;
    const WOMAN = 2;

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
