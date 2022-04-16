<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Key_Master extends Model
{
    public $table="key_master";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key_name',
    ];

}
