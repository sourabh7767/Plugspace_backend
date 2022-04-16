<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class User_Master extends Model
{
    
    public $table="user_master";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'ccode',
        'phone',
        'is_verified',
        'gender',
        'rank',
        'location',
        'is_geo_location',
        'is_apple',
        'apple_id',
        'is_insta',
        'insta_id',   
        'is_manual_email',
        'height',
        'weight',
        'education_status',
        'dob',
        'children', 
        'want_childrens',
        'marring_race',
        'relationship_status',
        'ethinicity',
        'company_name',
        'job_title',
        'make_over',  
        'dress_size',
        'signiat_bills',
        'times_of_engaged',
        'your_body_tatto',
        'age_range_marriage',
        'my_self_men',
        'about_you',
        'nice_meet',
        'is_subscribe',
        'device_type',
        'device_token',
        'is_private',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'pass',
        'created_at',
        'updated_at',
        
        'is_manual_email',
        'ucode',
        'is_verified',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'string',
        'is_insta' => 'string',
        'is_apple' => 'string',
        'is_manual_email' => 'string',
        
    ];

    public function media_detail()
    {
        return $this->hasMany(User_Media_Master::class, 'user_id', 'user_id');
    }

    public function feed_detail()
    {
        return $this->hasMany(Feed_Master::class, 'user_id', 'user_id');
    }
}
