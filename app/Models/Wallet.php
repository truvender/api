<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Wallet extends Model
{
    use HasFactory, Uuid;



    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'private',
        'wif',
    ];


    /**
     * Get the user that owns the Wallet
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Get the asset that owns the Wallet
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function asset()
    {
        return $this->belongsTo(CryptoAsset::class, 'asset_id');
    }

    public function updateBalance()
    {
        $endpoint = blockEndpoint($this->asset->symbol, "addrs/" . $this->address ."/balance", false);
        $request = Http::get($endpoint)->json();
        
        $this->update([
            'balance' => $request['balance']
        ]);

        return true;
    }


    /**
     * Get all of the deposis for the Wallet
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deposits()
    {
        return $this->hasMany(Deposit::class, 'wallet_id', 'id');
    }

}
