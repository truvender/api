<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DefaultRate extends Model
{
    use HasFactory, Uuid;

    protected $guarded = ['id'];

    public $timestamps = false;

    /**
     * Get the giftcard that owns the DefaultRate
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function giftcard()
    {
        return $this->belongsTo(Card::class, 'card_id');
    }

    
    /**
     * Get the range that owns the DefaultRate
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function range()
    {
        return $this->belongsTo(TradeRange::class, 'range_id');
    }
}
