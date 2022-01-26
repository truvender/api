<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laratrust\Traits\LaratrustUserTrait;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use LaratrustUserTrait;
    use HasApiTokens, HasFactory, Notifiable;
    use Uuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'phone',
        'referrer_id', 
        'has_verify_otp',
        'phone_verified_at',
        'email_verified_at',
        'profile_photo_path',
        'password',
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
        'phone_verified_at' => 'datetime'
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }


    public function getProfilePhotoAttributes()
    {
        return $this->profile_photo_path == null ? 
            "https://avatars.dicebear.com/api/identicon/$this->username.svg" : 
            getFileUrl($this->profile_photo_path);
    }


    /**
     * Get the setting associated with the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function settings()
    {
        return $this->hasOne(Setting::class, 'user_id', 'id');
    }

    /**
     * Get the profile associated with the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id', 'id');
    }

    /**
     * Get all of the wallets for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function wallets()
    {
        return $this->hasMany(Wallet::class, 'user_id', 'id');
    }


    /**
     * Get all of the tokens for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tokens()
    {
        return $this->hasMany(Token::class, 'user_id', 'id');
    }


    /**
     * Get all of the transactions for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function transactions()
    {
        return $this->hasManyThrough(Transaction::class, Wallet::class);
    }


    /**
     * Get the bankingAccount associated with the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function bankingAccount()
    {
        return $this->hasOne(BankAccount::class, 'user_id', 'id');
    }

}
