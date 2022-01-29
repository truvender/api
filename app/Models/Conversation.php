<?php

namespace App\Models;

use App\Http\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conversation extends Model
{
    use HasFactory, Uuid, SoftDeletes;

    protected $guarded = ['id'];

    /**
     * Get the team_member that owns the Conversation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team_member()
    {
        return $this->belongsTo(User::class, 'support_member');
    }


    /**
     * Get the user that owns the Conversation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
