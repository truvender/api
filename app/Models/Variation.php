<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Variation extends Model
{
    use HasFactory, Uuid;

    public $timestamps = false;

    protected $guarded = ['id'];

    /**
     * Get all of the billPayments for the Variation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function billPayments()
    {
        return $this->hasMany(BillPayment::class, 'variation_id', 'id');
    }
    
}
