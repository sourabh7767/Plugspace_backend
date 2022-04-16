<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Story_Media_Master extends Model
{
    
    public $table="story_media_master";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       
        'story_id',
        'media',
        'media_type',
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

    public function getMediaAttribute($value)
    {
        if (!empty($value)) {
            return env('PUBLIC_PATH').'story/' . $value;
        } else {
            return '';
        }
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'story_id' => 'string',
        'story_media_id' => 'string',
    ];

}
