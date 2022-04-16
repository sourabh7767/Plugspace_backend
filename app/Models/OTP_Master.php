<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OTP_Master extends Model
{
    public $table="send_otp_master";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ccode',
        'mobile',
        'otp_code',
        'is_verified'
    ];

}
