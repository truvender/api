<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FiatRate extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Get the currencyFrom that owns the FiatRate
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currencyFrom()
    {
        return $this->belongsTo(Country::class, 'from');
    }

    /**
     * Get the currencyTo that owns the FiatRate
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currencyTo()
    {
        return $this->belongsTo(Country::class, 'to');
    }
}
