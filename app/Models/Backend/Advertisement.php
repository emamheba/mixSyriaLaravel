<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    use HasFactory;

    protected $table = 'advertisements';
    protected $fillable = ['title','type','size','image','slot','embed_code','redirect_url','click','impression','status'];

    protected $casts = [
        'status' => 'integer'
    ];
}
