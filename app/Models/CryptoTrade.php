<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CryptoTrade extends Model
{
    use HasFactory, Uuid;

    protected $guarded = ['id'];

    /**
     * Get the asset that owns the CryptoTrade
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function asset()
    {
        return $this->belongsTo(CryptoAsset::class, 'asset_id');
    }
}
