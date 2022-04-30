<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardType extends Model
{
    use HasFactory, Uuid;

    protected $guarded = ['id'];
    
    public $timestamps = false;

    /**
     * The cards that belong to the CardType
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function cards()
    {
        return $this->belongsToMany(
            Card::class, 'card_type', 'type_id', 'card_id'
        )->withPivot(['status', 'demo']);
    }


    /**
     * Get all of the prices for the CardType
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function prices()
    {
        return $this->hasMany(CardPrice::class, 'type_id', 'id');
    }
}
