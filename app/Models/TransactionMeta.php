<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionMeta extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public $timestamps = false;

    /**
     * Get the transaction that owns the TransactionMeta
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
}
