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
     * Get all of the transactions for the CryptoAsset
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'asset_id', 'id');
    }


    /**
     * Get the rate associated with the CryptoAsset
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function rates()
    {
        return $this->hasOne(CryptoRate::class, 'asset_id', 'id');
    }


}
