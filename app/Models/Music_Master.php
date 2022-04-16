<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Music_Master extends Model
{
    
    public $table="music_master";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'music_other_id',
        'title',
        'image_url',
        'media_url',
        'artists_name',
        'language',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
     
        'created_at',
        'updated_at',
        
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'music_other_id' => 'string',
        'music_id' => 'string',
        'artists_name' => 'string',
    ];

}
