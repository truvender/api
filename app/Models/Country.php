<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use HasFactory;
    use Uuid;

    protected $guarded = ['id'];

    protected $table = 'countries';
    public $timestamps = false;


    /**
     * Get all of the banks for the Country
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function banks()
    {
        return $this->hasMany(bank::class, 'country_id', 'id');
    }


    /**
     * The cards that belong to the Country
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function cards()
    {
        return $this->belongsToMany(
            Card::class, 'crad_country', 'card_id', 'country'
        )->withPivot('status');
    }


}
