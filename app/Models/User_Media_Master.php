<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class User_Media_Master extends Model
{
    
    public $table="user_media_master";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'profile',
        'user_id',
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

    public function getProfileAttribute($value)
    {
        if (!empty($value)) {
            return env('PUBLIC_PATH').'profile/' . $value;
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
        'media_id' => 'string',
        'user_id' => 'string',
    ];

}
