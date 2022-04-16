<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class User_Subscription_Master extends Model
{
    public $table="user_subscription_master";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'plan_name',
        'validity',
        'amount',
        'transaction_id',
    ];

}
