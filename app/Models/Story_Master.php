<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Story_Master extends Model
{
    
    public $table="story_master";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
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
        'story_id' => 'string',
    ];

    public function story_media_detail()
    {
        return $this->hasMany(Story_Media_Master::class, 'story_id', 'story_id');
    }
}
