<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $config = Config::where('key','subscription_free_limit')->first();
            $model->share_limit = $config->value;
            $model->available_limit = $config->value;
        });
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the user associated with the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function agent()
    {
        return $this->hasOne(Agent::class);
    }

    public function office()
    {
        return $this->hasOne(Office::class);
    }

    public function brand()
    {
        return $this->hasOne(Brand::class);
    }

    public function chargebee()
    {
        return $this->hasMany(Chargebee::class);
    }

    public function shareJob()
    {
        return $this->hasMany(Sharejob::class);
    }

    public function facebook()
    {
        return $this->hasOne(Facebook::class);
    }

    public function automationInstagramLibraries()
    {
        return $this->hasOne(AutomationInstagramLibrary::class);
    }

}
