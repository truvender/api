<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CryptoRecord extends Model
{
    use HasFactory, Uuid;
    
    protected $guarded = ['id'];

    /**
     * Get the transaction that owns the CryptoRecord
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transaction()
    {
        return $this->belongsTo(Transction::class, 'transaction_id');
    }


    /**
     * Get the asset that owns the CryptoRecord
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function asset()
    {
        return $this->belongsTo(CryptoAsset::class, 'asset_id');
    }

}
