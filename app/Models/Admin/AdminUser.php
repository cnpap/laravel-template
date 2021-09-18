<?php

namespace App\Models\Admin;

use App\Models\Model;
use App\Models\ModelTrait;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int id
 * @property int status
 * @property int sex
 * @property string nick_name
 * @property string real_name
 * @property string avatar
 * @property string phone
 * @property string email
 * @property string password
 */
class AdminUser extends User
{
    use HasApiTokens, HasFactory, Notifiable, ModelTrait;

    protected $table = 'admin_user';

    const MAN   = 1;
    const WOMAN = 2;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
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
}
