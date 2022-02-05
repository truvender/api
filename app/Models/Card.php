<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Card extends Model
{
    use HasFactory, Uuid;

    protected $guarded = ['id'];

    public $timestamps = false;

    /**
     * The countries that belong to the Card
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function countries()
    {
        return $this->belongsToMany(
            Country::class, 'card_country', 'card_id', 'country_id'
        )->withPivot('status');
    }
    

    /**
     * The types that belong to the Card
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function types()
    {
        return $this->belongsToMany(
            CardType::class, 'card_type', 'card_id', 'type_id'
        )->withPivot('status');
    }



    /**
     * Get all of the prices for the Card
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function prices()
    {
        return $this->hasMany(CardPrice::class, 'card_id', 'id');
    }


    /**
     * Get all of the rates for the Card
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rates()
    {
        return $this->hasMany(CardRate::class, 'card_id');
    }


    /**
     * Get all of the default_rates for the Card
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function default_rates()
    {
        return $this->hasMany(DefaultRate::class, 'card_id', 'id');
    }

}
