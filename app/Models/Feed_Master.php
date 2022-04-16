<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Feed_Master extends Model
{
    
    public $table="feed_master";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'feed_image',
        'media_type',
        'description',
        'created_at',
        'updated_at',
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
    // protected $casts = [
    //     'media_id' => 'string',
    //     'user_id' => 'string',
    // ];

    protected $casts = [
        'feed_id' => 'string',
    ];
    
    public function getFeedImageAttribute($value)
    {
        if (!empty($value)) {
            return env('PUBLIC_PATH').'story/' . $value;
        } else {
            return '';
        }
    } 
}
