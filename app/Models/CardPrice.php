<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardPrice extends Model
{
    use HasFactory, Uuid;

    protected $guarded = ['id'];

    public $timestamps = false;

    /**
     * Get the card that owns the CardPrice
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function card()
    {
        return $this->belongsTo(Card::class, 'card_id');
    }


    /**
     * Get the type that owns the CardPrice
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(CardType::class, 'type_id');
    }
}
