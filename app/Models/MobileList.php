<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MobileList extends Model
{
    use HasFactory;

    protected $table = "mobile_list";
    
    protected $guarded = ['id'];
    
    public $timestamps = false;
}
