<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CryptoAsset extends Model
{
    use HasFactory, Uuid;

    protected $guarded = ['id'];

    public $timestamps = false;


    /**
     * Get the rate associated with the CryptoAsset
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function rates()
    {
        return $this->hasOne(CryptoRate::class, 'asset_id', 'id');
    }



    /**
     * Get all of the trades for the CryptoAsset
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function trades()
    {
        return $this->hasMany(CryptoTrade::class, 'asset_id');
    }
}
